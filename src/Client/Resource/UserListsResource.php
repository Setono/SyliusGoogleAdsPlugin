<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client\Resource;

use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\V24\Resources\UserList;
use Google\Ads\GoogleAds\V24\Services\MutateUserListsRequest;
use Google\Ads\GoogleAds\V24\Services\UserListOperation;

/**
 * @internal
 *
 * See https://developers.google.com/google-ads/api/reference/rpc/v24/UserList
 *
 * @extends AbstractResource<UserList>
 */
final class UserListsResource extends AbstractResource implements UserListsResourceInterface
{
    public function create(object $obj): string
    {
        return $this->handleMutateResponse($this->googleAdsClient->getUserListServiceClient()->mutateUserLists(
            MutateUserListsRequest::build($this->client->getCustomerId(), [
                (new UserListOperation())->setCreate($obj),
            ]),
        ));
    }

    public function update(object $obj): string
    {
        return $this->handleMutateResponse($this->googleAdsClient->getUserListServiceClient()->mutateUserLists(
            MutateUserListsRequest::build($this->client->getCustomerId(), [
                (new UserListOperation())
                    ->setUpdate($obj)
                    ->setUpdateMask(FieldMasks::allSetFieldsOf($obj)),
            ]),
        ));
    }

    protected static function getResourceClass(): string
    {
        return UserList::class;
    }
}
