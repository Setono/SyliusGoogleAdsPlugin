<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Processor;

use Google\Ads\GoogleAds\Util\V13\ResourceNames;
use Google\Ads\GoogleAds\V13\Services\ClickConversion;
use Google\Ads\GoogleAds\V13\Services\ClickConversionResult;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionMappingRepositoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class ConversionProcessor implements ConversionProcessorInterface
{
    public function __construct(
        private readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory,
        private readonly ConnectionMappingRepositoryInterface $connectionMappingRepository,
    ) {
    }

    public function process(ConversionInterface $conversion): void
    {
        $channel = $conversion->getChannel();
        Assert::notNull($channel);

        foreach ($this->getConnectionMappings($channel) as $connectionMapping) {
            $connection = $connectionMapping->getConnection();
            Assert::notNull($connection);

            $customerId = $connectionMapping->getCustomerId();
            Assert::notNull($customerId);

            $client = $this->googleAdsClientFactory->createFromConnection($connection);

            // Creates a click conversion by specifying currency as USD.
            $clickConversion = new ClickConversion([
                'conversion_action' => ResourceNames::forConversionAction(
                    (string) $customerId,
                    (string) $connectionMapping->getConversionActionId(),
                ),
                'conversion_value' => round((int) $conversion->getValue() / 100, 2),
                'conversion_date_time' => $conversion->getCreatedAt()?->format('Y-m-d H:i:sP'),
                'currency_code' => $conversion->getCurrencyCode(),
                'order_id' => $conversion->getOrder()?->getId(),
                'gclid' => $conversion->getGoogleClickId(),
            ]);

            $conversionUploadServiceClient = $client->getConversionUploadServiceClient();

            $response = $conversionUploadServiceClient->uploadClickConversions(
                (string) $customerId,
                [$clickConversion],
                true, // notice that we only add one operation so in practice it's not a partial error, but just an error
            );

            // Prints the status message if any partial failure error is returned.
            // Note: The details of each partial failure error are not printed here, you can refer to
            // the example HandlePartialFailure.php to learn more.
            if ($response->hasPartialFailureError()) {
                printf("Partial failures occurred: '%s'.\n", (string) $response->getPartialFailureError()?->getMessage());
            } else {
                /** @var ClickConversionResult $uploadedClickConversion */
                $uploadedClickConversion = $response->getResults()[0];
                printf(
                    "Uploaded click conversion that occurred at '%s' from Google Click ID '%s' to '%s'.\n",
                    $uploadedClickConversion->getConversionDateTime(),
                    $uploadedClickConversion->getGclid(),
                    $uploadedClickConversion->getConversionAction(),
                );
            }
        }
    }

    /**
     * @return array<array-key, ConnectionMappingInterface>
     */
    private function getConnectionMappings(ChannelInterface $channel): array
    {
        return $this->connectionMappingRepository->findEnabledByChannel($channel);
    }
}
