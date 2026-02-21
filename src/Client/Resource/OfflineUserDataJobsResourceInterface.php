<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client\Resource;

use Google\Ads\GoogleAds\V20\Resources\OfflineUserDataJob;
use Google\Ads\GoogleAds\V20\Services\OfflineUserDataJobOperation;

/**
 * @experimental
 *
 * @extends CreatableResourceInterface<OfflineUserDataJob>
 * @extends ReadableResourceInterface<OfflineUserDataJob>
 */
interface OfflineUserDataJobsResourceInterface extends CreatableResourceInterface, ReadableResourceInterface
{
    /**
     * @param list<OfflineUserDataJobOperation> $operations
     */
    public function addOperations(string $resourceName, array $operations): void;

    public function run(string $resourceName): void;
}
