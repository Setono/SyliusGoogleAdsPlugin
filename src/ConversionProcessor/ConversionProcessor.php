<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Google\Ads\GoogleAds\Util\V13\ResourceNames;
use Google\Ads\GoogleAds\V13\Common\UserIdentifier;
use Google\Ads\GoogleAds\V13\Enums\UserIdentifierSourceEnum\UserIdentifierSource;
use Google\Ads\GoogleAds\V13\Services\ClickConversion;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Logger\ConversionLogger;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionMappingRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;
use Webmozart\Assert\Assert;

final class ConversionProcessor extends AbstractConversionProcessor
{
    public function __construct(
        WorkflowInterface $workflow,
        GoogleAdsClientFactoryInterface $googleAdsClientFactory,
        ConnectionMappingRepositoryInterface $connectionMappingRepository,
        private readonly string $enhancedConversionUploadDelay,
    ) {
        parent::__construct($workflow, $googleAdsClientFactory, $connectionMappingRepository);
    }

    public function isEligible(ConversionInterface $conversion): bool
    {
        return $this->workflow->can($conversion, ConversionWorkflow::TRANSITION_UPLOAD_CONVERSION);
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

        $client = $this->googleAdsClientFactory->createFromConnection($connection, $managerId, new ConversionLogger($conversion));

        $createdAt = $conversion->getCreatedAt();
        Assert::notNull($createdAt);

        // Google doesn't allow daylight savings time when uploading, so we need this small hack to turn our time into UTC first
        $createdAt = \DateTimeImmutable::createFromInterface($createdAt)->setTimezone(new \DateTimeZone('UTC'));

        $clickConversion = new ClickConversion([
            'conversion_action' => ResourceNames::forConversionAction(
                (string) $customerId,
                (string) $connectionMapping->getConversionActionId(),
            ),
            'conversion_value' => round((int) $conversion->getValue() / 100, 2),
            'conversion_date_time' => $createdAt->format('Y-m-d H:i:sP'),
            'currency_code' => $conversion->getCurrencyCode(),
            'order_id' => $order->getId(),
            'gclid' => $conversion->getGoogleClickId(),
        ]);

        $clickConversion->setUserIdentifiers([
            new UserIdentifier([
                'hashed_email' => self::normalizeAndHashEmailAddress($order->getCustomer()?->getEmailCanonical()),
                'user_identifier_source' => UserIdentifierSource::FIRST_PARTY,
            ]),
        ]);

        $conversionUploadServiceClient = $client->getConversionUploadServiceClient();

        $response = $conversionUploadServiceClient->uploadClickConversions(
            (string) $customerId,
            [$clickConversion],
            true, // notice that we only add one operation so in practice it's not a partial error, but just an error
        );

        if ($response->hasPartialFailureError()) {
            throw new \RuntimeException(sprintf(
                'Uploading the conversion failed: %s',
                (string) $response->getPartialFailureError()?->getMessage(),
            ));
        }

        $conversion->setNextProcessingAt((new \DateTimeImmutable())->add(new \DateInterval($this->enhancedConversionUploadDelay)));

        $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_UPLOAD_CONVERSION);
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
