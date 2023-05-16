<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;

interface ConversionActionIdsResolverInterface
{
    /**
     * @return list<ConversionActionId>
     */
    public function getConversionActionIdsFromConnectionAndCustomer(
        ConnectionInterface $connection,
        int $customerId,
    ): array;
}
