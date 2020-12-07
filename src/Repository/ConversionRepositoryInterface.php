<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionActionInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ConversionRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the conversions that are enabled and enabled on the given channel
     *
     * @return ConversionActionInterface[]
     */
    public function findEnabledByChannel(ChannelInterface $channel): array;
}
