<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Google\Ads\GoogleAds\Util\V13\ResourceNames;
use Google\Ads\GoogleAds\V13\Common\OfflineUserAddressInfo;
use Google\Ads\GoogleAds\V13\Common\UserIdentifier;
use Google\Ads\GoogleAds\V13\Enums\ConversionAdjustmentTypeEnum\ConversionAdjustmentType;
use Google\Ads\GoogleAds\V13\Enums\UserIdentifierSourceEnum\UserIdentifierSource;
use Google\Ads\GoogleAds\V13\Services\ConversionAdjustment;
use Setono\SyliusGoogleAdsPlugin\Logger\ConversionLogger;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Webmozart\Assert\Assert;

final class EnhancedConversionProcessor extends AbstractConversionProcessor
{
    public function isEligible(ConversionInterface $conversion): bool
    {
        $stateUpdatedAt = $conversion->getStateUpdatedAt();
        if (null === $stateUpdatedAt) {
            return false;
        }

        $lastUpdatedThreshold = (new \DateTimeImmutable())->sub(new \DateInterval('PT24H'));

        return $stateUpdatedAt <= $lastUpdatedThreshold && $this->workflow->can($conversion, ConversionWorkflow::TRANSITION_UPLOAD_ENHANCED_CONVERSION);
    }

    public function process(ConversionInterface $conversion): void
    {
        Assert::true($this->isEligible($conversion));

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

        $order = $conversion->getOrder();
        Assert::notNull($order);

        $billingAddress = $order->getBillingAddress();
        Assert::notNull($billingAddress);

        $client = $this->googleAdsClientFactory->createFromConnection($connection, $managerId, new ConversionLogger($conversion));

        $conversionAdjustment = new ConversionAdjustment([
            'conversion_action' => ResourceNames::forConversionAction(
                (string) $customerId,
                (string) $connectionMapping->getEnhancedConversionActionId(),
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

        $userAgent = $conversion->getUserAgent();
        if (null !== $userAgent) {
            $conversionAdjustment->setUserAgent($userAgent);
        }

        $conversionAdjustmentUploadServiceClient = $client->getConversionAdjustmentUploadServiceClient();
        $response = $conversionAdjustmentUploadServiceClient->uploadConversionAdjustments(
            (string) $customerId,
            [$conversionAdjustment],
            true,
        );

        if ($response->hasPartialFailureError()) {
            throw new \RuntimeException(sprintf(
                'Uploading the enhanced conversion failed: %s',
                (string) $response->getPartialFailureError()?->getMessage(),
            ));
        }

        $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_UPLOAD_ENHANCED_CONVERSION);
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
