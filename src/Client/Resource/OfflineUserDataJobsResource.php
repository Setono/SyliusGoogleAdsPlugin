<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client\Resource;

use Google\Ads\GoogleAds\V24\Resources\OfflineUserDataJob;
use Google\Ads\GoogleAds\V24\Services\AddOfflineUserDataJobOperationsRequest;
use Google\Ads\GoogleAds\V24\Services\CreateOfflineUserDataJobRequest;
use Google\Ads\GoogleAds\V24\Services\OfflineUserDataJobOperation;
use Google\Ads\GoogleAds\V24\Services\RunOfflineUserDataJobRequest;

/**
 * @experimental
 * See https://developers.google.com/google-ads/api/reference/rpc/v24/OfflineUserDataJob
 *
 * @extends AbstractResource<OfflineUserDataJob>
 */
final class OfflineUserDataJobsResource extends AbstractResource implements OfflineUserDataJobsResourceInterface
{
    public function create(object $obj): string
    {
        return $this->googleAdsClient->getOfflineUserDataJobServiceClient()->createOfflineUserDataJob(
            CreateOfflineUserDataJobRequest::build($this->client->getCustomerId(), $obj),
        )->getResourceName();
    }

    /**
     * @param list<OfflineUserDataJobOperation> $operations
     */
    public function addOperations(string $resourceName, array $operations): void
    {
        $this->googleAdsClient->getOfflineUserDataJobServiceClient()->addOfflineUserDataJobOperations(
            AddOfflineUserDataJobOperationsRequest::build($resourceName, $operations)
                ->setEnablePartialFailure(true),
        );
    }

    public function run(string $resourceName): void
    {
        $this->googleAdsClient->getOfflineUserDataJobServiceClient()->runOfflineUserDataJob(
            RunOfflineUserDataJobRequest::build($resourceName),
        );
    }

    protected static function getResourceClass(): string
    {
        return OfflineUserDataJob::class;
    }
}
