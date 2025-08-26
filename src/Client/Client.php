<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client;

use Google\Ads\GoogleAds\Lib\V19\GoogleAdsClient;
use Google\Ads\GoogleAds\V19\Services\SearchGoogleAdsStreamRequest;
use Setono\SyliusGoogleAdsPlugin\Client\Resource\OfflineUserDataJobsResource;
use Setono\SyliusGoogleAdsPlugin\Client\Resource\OfflineUserDataJobsResourceInterface;
use Setono\SyliusGoogleAdsPlugin\Client\Resource\UserListCustomerTypesResource;
use Setono\SyliusGoogleAdsPlugin\Client\Resource\UserListCustomerTypesResourceInterface;
use Setono\SyliusGoogleAdsPlugin\Client\Resource\UserListsResource;
use Setono\SyliusGoogleAdsPlugin\Client\Resource\UserListsResourceInterface;

/**
 * @experimental
 */
final class Client implements ClientInterface
{
    private ?OfflineUserDataJobsResourceInterface $offlineUserDataJobsResource = null;

    private ?UserListsResourceInterface $userListsResource = null;

    private ?UserListCustomerTypesResourceInterface $userListCustomerTypesResource = null;

    public function __construct(
        private readonly GoogleAdsClient $googleAdsClient,
        private readonly string $customerId,
    ) {
    }

    public function offlineUserDataJobs(): OfflineUserDataJobsResourceInterface
    {
        if (null === $this->offlineUserDataJobsResource) {
            $this->offlineUserDataJobsResource = new OfflineUserDataJobsResource($this, $this->googleAdsClient);
        }

        return $this->offlineUserDataJobsResource;
    }

    public function userLists(): UserListsResourceInterface
    {
        if (null === $this->userListsResource) {
            $this->userListsResource = new UserListsResource($this, $this->googleAdsClient);
        }

        return $this->userListsResource;
    }

    public function userListCustomerTypes(): UserListCustomerTypesResourceInterface
    {
        if (null === $this->userListCustomerTypesResource) {
            $this->userListCustomerTypesResource = new UserListCustomerTypesResource($this, $this->googleAdsClient);
        }

        return $this->userListCustomerTypesResource;
    }

    public function search(string $query): SearchResponse
    {
        return SearchResponse::fromServerStream($this->googleAdsClient->getGoogleAdsServiceClient()->searchStream(
            SearchGoogleAdsStreamRequest::build($this->customerId, $query),
        ));
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }
}
