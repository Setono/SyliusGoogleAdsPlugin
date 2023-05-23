<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Google\Ads\GoogleAds\Lib\V13\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\V13\Services\GoogleAdsRow;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;
use Webmozart\Assert\Assert;

final class ConversionActionIdsResolver implements ConversionActionIdsResolverInterface
{
    public function __construct(private readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory)
    {
    }

    public function getConversionActionIdsFromConnectionMapping(ConnectionMappingInterface $connectionMapping): array
    {
        $connection = $connectionMapping->getConnection();
        Assert::notNull($connection);

        $client = $this->googleAdsClientFactory->createFromConnection($connection, $connectionMapping->getManagerId());

        $googleAdsServiceClient = $client->getGoogleAdsServiceClient();

        $query = "SELECT conversion_action.id, conversion_action.name, conversion_action.origin, conversion_action.category FROM conversion_action WHERE conversion_action.status = 'ENABLED' AND conversion_action.category = 'PURCHASE'";

        /** @var GoogleAdsServerStreamDecorator $stream */
        $stream = $googleAdsServiceClient->searchStream((string) $connectionMapping->getCustomerId(), $query);

        $conversionActionIds = [];

        /** @var GoogleAdsRow $googleAdsRow */
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $conversionAction = $googleAdsRow->getConversionAction();
            if (null === $conversionAction) {
                continue;
            }

            $conversionActionIds[] = new ConversionActionId($conversionAction->getName(), (int) $conversionAction->getId());
        }

        return $conversionActionIds;
    }
}
