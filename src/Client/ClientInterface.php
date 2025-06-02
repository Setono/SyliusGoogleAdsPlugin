<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client;

use Setono\SyliusGoogleAdsPlugin\Client\Resource\OfflineUserDataJobsResourceInterface;
use Setono\SyliusGoogleAdsPlugin\Client\Resource\UserListCustomerTypesResourceInterface;
use Setono\SyliusGoogleAdsPlugin\Client\Resource\UserListsResourceInterface;

/**
 * @experimental
 */
interface ClientInterface
{
    public function userLists(): UserListsResourceInterface;

    public function userListCustomerTypes(): UserListCustomerTypesResourceInterface;

    public function offlineUserDataJobs(): OfflineUserDataJobsResourceInterface;

    public function search(string $query): SearchResponse;

    public function getCustomerId(): string;
}
