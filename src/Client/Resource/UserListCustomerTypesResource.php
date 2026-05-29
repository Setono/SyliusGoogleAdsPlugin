<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client\Resource;

use Google\Ads\GoogleAds\V24\Resources\UserListCustomerType;
use Google\Ads\GoogleAds\V24\Services\MutateUserListCustomerTypesRequest;
use Google\Ads\GoogleAds\V24\Services\UserListCustomerTypeOperation;

/**
 * @internal
 *
 * @experimental
 * See https://developers.google.com/google-ads/api/reference/rpc/v24/UserListCustomerType
 *
 * @extends AbstractResource<UserListCustomerType>
 */
final class UserListCustomerTypesResource extends AbstractResource implements UserListCustomerTypesResourceInterface
{
    public function create(object $obj): string
    {
        return $this->handleMutateResponse(
            $this->googleAdsClient->getUserListCustomerTypeServiceClient()->mutateUserListCustomerTypes(
                MutateUserListCustomerTypesRequest::build($this->client->getCustomerId(), [
                    (new UserListCustomerTypeOperation())
                        ->setCreate($obj),
                ]),
            ),
        );
    }

    protected static function getResourceClass(): string
    {
        return UserListCustomerType::class;
    }
}
