<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;

interface CustomerIdsResolverInterface
{
    /**
     * @return list<CustomerId>
     */
    public function getCustomerIdsFromConnection(ConnectionInterface $connection): array;
}
