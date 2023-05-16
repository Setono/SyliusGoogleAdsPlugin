<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Processor;

use Google\Ads\GoogleAds\Util\V13\ResourceNames;
use Google\Ads\GoogleAds\V13\Services\ClickConversion;
use Google\Ads\GoogleAds\V13\Services\ClickConversionResult;
use Google\Ads\GoogleAds\V13\Services\UploadClickConversionsResponse;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Webmozart\Assert\Assert;

final class ConversionProcessor implements ConversionProcessorInterface
{
    public function __construct(private readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory)
    {
    }

    public function process(ConversionInterface $conversion): void
    {
        $channel = $conversion->getChannel();
        Assert::notNull($channel);

        foreach ($this->getConnections() as $connection) {
            $customerId = $connection->getGoogleAdsCustomerId($channel);

            $client = $this->googleAdsClientFactory->createFromConnection($connection);

            // Creates a click conversion by specifying currency as USD.
            $clickConversion = new ClickConversion([
                'conversion_action' => ResourceNames::forConversionAction($customerId, $conversionActionId),
                'conversion_value' => round($conversion->getValue() / 100, 2),
                'conversion_date_time' => $conversion->getCreatedAt()?->format('Y-m-d H:i:sP'),
                'currency_code' => $conversion->getCurrencyCode(),
                'order_id' => $conversion->getOrder()?->getId(),
                'gclid' => $conversion->getGoogleClickId(),
            ]);

            // Issues a request to upload the click conversion.
            $conversionUploadServiceClient = $client->getConversionUploadServiceClient();
            /** @var UploadClickConversionsResponse $response */
            $response = $conversionUploadServiceClient->uploadClickConversions(
                $customerId,
                [$clickConversion],
                true, // notice that we only add one operation so in practice it's not a partial error, but just an error
            );

            // Prints the status message if any partial failure error is returned.
            // Note: The details of each partial failure error are not printed here, you can refer to
            // the example HandlePartialFailure.php to learn more.
            if ($response->hasPartialFailureError()) {
                printf(
                    "Partial failures occurred: '%s'.%s",
                    $response->getPartialFailureError()?->getMessage(),
                    \PHP_EOL,
                );
            } else {
                /** @var ClickConversionResult $uploadedClickConversion */
                $uploadedClickConversion = $response->getResults()[0];
                printf(
                    "Uploaded click conversion that occurred at '%s' from Google Click ID '%s' " .
                    "to '%s'.%s",
                    $uploadedClickConversion->getConversionDateTime(),
                    $uploadedClickConversion->getGclid(),
                    $uploadedClickConversion->getConversionAction(),
                    \PHP_EOL,
                );
            }
        }
    }

    /**
     * todo it should return the connections available on the $conversion->getChannel() and the connections should be enabled
     *
     * @return list<ConnectionInterface>
     */
    private function getConnections(): array
    {
    }
}
