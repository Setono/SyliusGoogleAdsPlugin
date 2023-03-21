<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionActionInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<ConversionActionInterface>
 */
interface ConversionActionRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the conversions that are enabled and enabled on the given channel
     *
     * @return ConversionActionInterface[]
     */
    public function findEnabledByChannelAndCategory(ChannelInterface $channel, string $category): array;

    /**
     * Returns an array of channels that are present on one or more *enabled* conversion actions
     *
     * @return ChannelInterface[]
     */
    public function findChannels(): array;
}
