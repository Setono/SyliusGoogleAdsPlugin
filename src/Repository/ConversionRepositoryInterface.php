<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ConversionRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the conversions that are enabled, enabled on the given channel and has the given category
     */
    public function findEnabledByChannelAndCategory(ChannelInterface $channel, string $category): array;
}
