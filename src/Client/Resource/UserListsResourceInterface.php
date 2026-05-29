<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client\Resource;

use Google\Ads\GoogleAds\V24\Resources\UserList;

/**
 * @internal
 *
 * @extends UpdatableResourceInterface<UserList>
 * @extends ReadableResourceInterface<UserList>
 */
interface UserListsResourceInterface extends UpdatableResourceInterface, ReadableResourceInterface
{
}
