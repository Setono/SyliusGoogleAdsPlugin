<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\MerchantMappingInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<MerchantMappingInterface>
 */
interface MerchantMappingRepositoryInterface extends RepositoryInterface
{
    public function findOneByChannel(ChannelInterface $channel): ?MerchantMappingInterface;
}
