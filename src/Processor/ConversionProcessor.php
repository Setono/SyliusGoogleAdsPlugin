<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Processor;

use Doctrine\Persistence\ManagerRegistry;
use Google\Ads\GoogleAds\Util\V13\ResourceNames;
use Google\Ads\GoogleAds\V13\Common\OfflineUserAddressInfo;
use Google\Ads\GoogleAds\V13\Common\UserIdentifier;
use Google\Ads\GoogleAds\V13\Enums\ConversionAdjustmentTypeEnum\ConversionAdjustmentType;
use Google\Ads\GoogleAds\V13\Enums\UserIdentifierSourceEnum\UserIdentifierSource;
use Google\Ads\GoogleAds\V13\Services\ClickConversion;
use Google\Ads\GoogleAds\V13\Services\ConversionAdjustment;
use Google\Ads\GoogleAds\V13\Services\GclidDateTimePair;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Exception\WrongOrderTypeException;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionMappingRepositoryInterface;
use Webmozart\Assert\Assert;

final class ConversionProcessor implements ConversionProcessorInterface
{
    use ORMManagerTrait;

    public function __construct(
        private readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory,
        private readonly ConnectionMappingRepositoryInterface $connectionMappingRepository,
        ManagerRegistry $managerRegistry,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function process(ConversionInterface $conversion): void
    {
        $channel = $conversion->getChannel();
        Assert::notNull($channel);

        $connectionMapping = $this->connectionMappingRepository->findOneEnabledByChannel($channel);
        Assert::notNull($connectionMapping);

        $connection = $connectionMapping->getConnection();
        Assert::notNull($connection);

        $managerId = $connectionMapping->getManagerId();
        Assert::notNull($managerId);

        $customerId = $connectionMapping->getCustomerId();
        Assert::notNull($customerId);

        /** @var OrderInterface $order */
        $order = $conversion->getOrder();
        WrongOrderTypeException::assert($order);

        $billingAddress = $order->getBillingAddress();
        Assert::notNull($billingAddress);

        $client = $this->googleAdsClientFactory->createFromConnection($connection, $managerId);

        $clickConversion = new ClickConversion([
            'conversion_action' => ResourceNames::forConversionAction(
                (string) $customerId,
                (string) $connectionMapping->getConversionActionId(),
            ),
            'conversion_value' => round((int) $conversion->getValue() / 100, 2),
            'conversion_date_time' => $conversion->getCreatedAt()?->format('Y-m-d H:i:sP'),
            'currency_code' => $conversion->getCurrencyCode(),
            'order_id' => $order->getId(),
            'gclid' => $conversion->getGoogleClickId(),
        ]);

        $conversionUploadServiceClient = $client->getConversionUploadServiceClient();

        $response = $conversionUploadServiceClient->uploadClickConversions(
            (string) $customerId,
            [$clickConversion],
            true, // notice that we only add one operation so in practice it's not a partial error, but just an error
        );

        if ($response->hasPartialFailureError()) {
            $conversion->setState(ConversionInterface::STATE_FAILED);
            $conversion->setError((string) $response->getPartialFailureError()?->getMessage());
            $this->getManager($conversion)->flush();

            return;
        }

        $conversionAdjustment = new ConversionAdjustment([
            'conversion_action' => ResourceNames::forConversionAction(
                (string) $customerId,
                (string) $connectionMapping->getConversionActionId(),
            ),
            'adjustment_type' => ConversionAdjustmentType::ENHANCEMENT,
            'order_id' => $order->getId(),
        ]);

        $addressIdentifier = new UserIdentifier([
            'address_info' => new OfflineUserAddressInfo([
                'hashed_first_name' => self::normalizeAndHash($billingAddress->getFirstName()),
                'hashed_last_name' => self::normalizeAndHash($billingAddress->getLastName()),
                'hashed_street_address' => self::normalizeAndHash($billingAddress->getStreet()),
                'city' => $billingAddress->getCity(),
                'postal_code' => $billingAddress->getPostcode(),
                'country_code' => $billingAddress->getCountryCode(),
            ]),
            'user_identifier_source' => UserIdentifierSource::FIRST_PARTY,
        ]);

        $emailIdentifier = new UserIdentifier([
            'hashed_email' => self::normalizeAndHashEmailAddress($order->getCustomer()?->getEmailCanonical()),
            'user_identifier_source' => UserIdentifierSource::FIRST_PARTY,
        ]);

        $conversionAdjustment->setUserIdentifiers([$addressIdentifier, $emailIdentifier]);

        $checkoutCompletedAt = $order->getCheckoutCompletedAt();

        if (null !== $checkoutCompletedAt) {
            $conversionAdjustment->setGclidDateTimePair(new GclidDateTimePair([
                'conversion_date_time' => $checkoutCompletedAt->format('Y-m-d H:i:sP'),
            ]));
        }

        $userAgent = $order->getUserAgent();
        if (null !== $userAgent) {
            $conversionAdjustment->setUserAgent($userAgent);
        }

        $conversionAdjustmentUploadServiceClient = $client->getConversionAdjustmentUploadServiceClient();
        $response = $conversionAdjustmentUploadServiceClient->uploadConversionAdjustments(
            (string) $customerId,
            [$conversionAdjustment],
            true,
        );

        // Prints the status message if any partial failure error is returned.
        // Note: The details of each partial failure error are not printed here, you can refer to
        // the example HandlePartialFailure.php to learn more.
        if ($response->hasPartialFailureError()) {
            $conversion->setState(ConversionInterface::STATE_FAILED);
            $conversion->setError((string) $response->getPartialFailureError()?->getMessage());
        } else {
            $conversion->setState(ConversionInterface::STATE_DELIVERED);
            $conversion->setError(null);
        }

        $this->getManager($conversion)->flush();
    }

    private static function normalizeAndHash(?string $value): string
    {
        if (null === $value) {
            return '';
        }

        // Uses the SHA-256 hash algorithm for hashing user identifiers in a privacy-safe way, as
        // described at https://support.google.com/google-ads/answer/9888656.
        return hash('sha256', strtolower(trim($value)));
    }

    /**
     * Returns the result of normalizing and hashing an email address. For this use case, Google
     * Ads requires removal of any '.' characters preceding "gmail.com" or "googlemail.com".
     *
     * @param string $emailAddress the email address to normalize and hash
     *
     * @return string the normalized and hashed email address
     */
    private static function normalizeAndHashEmailAddress(?string $emailAddress): string
    {
        if (null === $emailAddress) {
            return '';
        }

        $normalizedEmail = strtolower($emailAddress);
        $emailParts = explode('@', $normalizedEmail);
        if (count($emailParts) > 1 && preg_match('/^(gmail|googlemail)\.com\s*/', $emailParts[1])) {
            // Removes any '.' characters from the portion of the email address before the domain
            // if the domain is gmail.com or googlemail.com.
            $emailParts[0] = str_replace('.', '', $emailParts[0]);
            $normalizedEmail = sprintf('%s@%s', $emailParts[0], $emailParts[1]);
        }

        return self::normalizeAndHash($normalizedEmail);
    }
}
