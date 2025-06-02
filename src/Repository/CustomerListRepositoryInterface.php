<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @experimental
 */
interface CustomerListRepositoryInterface extends RepositoryInterface
{
    /**
     * @return list<CustomerListInterface>
     */
    public function findEnabled(): array;

    /**
     * Will retrieve a list of customer lists that are eligible for upload to Google Ads
     *
     * @return list<CustomerListInterface>
     */
    public function findPendingForUpload(): array;

    /**
     * Returns true if at least one customer list exists for the given channel
     */
    public function hasAtLeastOneForChannel(ChannelInterface $channel): bool;
}
