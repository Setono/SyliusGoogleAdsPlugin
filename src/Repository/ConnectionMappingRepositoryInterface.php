<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<ConnectionMappingInterface>
 */
interface ConnectionMappingRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array<array-key, ConnectionMappingInterface>
     */
    public function findEnabledByChannel(ChannelInterface $channel): array;
}
