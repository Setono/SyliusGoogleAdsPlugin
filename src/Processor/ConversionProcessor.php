<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Processor;

use Doctrine\Persistence\ManagerRegistry;
use Google\Ads\GoogleAds\Util\V13\ResourceNames;
use Google\Ads\GoogleAds\V13\Services\ClickConversion;
use Google\Ads\GoogleAds\V13\Services\ClickConversionResult;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionMappingRepositoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
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

        foreach ($this->getConnectionMappings($channel) as $connectionMapping) {
            $connection = $connectionMapping->getConnection();
            Assert::notNull($connection);

            $managerId = $connectionMapping->getManagerId();
            Assert::notNull($managerId);

            $customerId = $connectionMapping->getCustomerId();
            Assert::notNull($customerId);

            $client = $this->googleAdsClientFactory->createFromConnection($connection, $managerId);

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

            if ($response->hasPartialFailureError()) {
                $conversion->setState(ConversionInterface::STATE_FAILED);
                $conversion->setError((string) $response->getPartialFailureError()?->getMessage());

                printf("Partial failures occurred: '%s'.\n", (string) $response->getPartialFailureError()?->getMessage());
            } else {
                $conversion->setState(ConversionInterface::STATE_DELIVERED);
                $conversion->setError(null);

                /** @var ClickConversionResult $uploadedClickConversion */
                $uploadedClickConversion = $response->getResults()[0];
                printf(
                    "Uploaded click conversion that occurred at '%s' from Google Click ID '%s' to '%s'.\n",
                    $uploadedClickConversion->getConversionDateTime(),
                    $uploadedClickConversion->getGclid(),
                    $uploadedClickConversion->getConversionAction(),
                );
            }

            $this->getManager($conversion)->flush();
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
