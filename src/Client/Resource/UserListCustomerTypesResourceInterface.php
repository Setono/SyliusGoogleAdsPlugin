<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client\Resource;

use Google\Ads\GoogleAds\V19\Resources\UserListCustomerType;

/**
 * @experimental
 *
 * @extends CreatableResourceInterface<UserListCustomerType>
 * @extends ReadableResourceInterface<UserListCustomerType>
 */
interface UserListCustomerTypesResourceInterface extends CreatableResourceInterface, ReadableResourceInterface
{
}
