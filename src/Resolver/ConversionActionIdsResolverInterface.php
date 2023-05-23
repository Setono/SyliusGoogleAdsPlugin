<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;

interface ConversionActionIdsResolverInterface
{
    /**
     * @return list<ConversionActionId>
     */
    public function getConversionActionIdsFromConnectionMapping(ConnectionMappingInterface $connectionMapping): array;
}
