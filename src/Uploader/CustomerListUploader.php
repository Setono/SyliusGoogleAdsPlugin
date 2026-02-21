<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Uploader;

use Doctrine\Persistence\ManagerRegistry;
use Google\Ads\GoogleAds\V20\Common\CrmBasedUserListInfo;
use Google\Ads\GoogleAds\V20\Enums\CustomerMatchUploadKeyTypeEnum\CustomerMatchUploadKeyType;
use Google\Ads\GoogleAds\V20\Resources\UserList;
use Google\Ads\GoogleAds\V20\Resources\UserListCustomerType;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusGoogleAdsPlugin\Factory\ClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final class CustomerListUploader implements CustomerListUploaderInterface
{
    use ORMTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly ClientFactoryInterface $clientFactory,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function upload(CustomerListInterface $customerList): void
    {
        if (!$customerList->isEnabled()) {
            return;
        }

        $channel = $customerList->getChannel();
        Assert::notNull($channel);

        $client = $this->clientFactory->createFromChannel($channel);

        $resourceName = $client->userLists()->createOrUpdate(
            $customerList->getResourceName(),
            function (UserList $userList) {
                $userList->setCrmBasedUserList(new CrmBasedUserListInfo([
                    'upload_key_type' => CustomerMatchUploadKeyType::CONTACT_INFO,
                ]));
            },
            function (UserList $userList) use ($customerList) {
                $userList->setName((string) $customerList->getName());
                $userList->setDescription(sprintf(
                    'A list of customers that originates from the Sylius channel %s',
                    (string) $customerList->getChannel()?->getName(),
                ));
                $userList->setMembershipLifeSpan((int) $customerList->getMembershipLifespan());
            },
        );

        $customerList->setResourceName($resourceName);
        $customerList->setUploadedAt(new \DateTimeImmutable());
        $this->getManager($customerList)->flush();

        // Set the customer type on the user list if specified
        $customerTypeCategory = $customerList->getCustomerTypeCategory();
        if (null !== $customerTypeCategory) {
            $userListCustomerType = new UserListCustomerType();
            $userListCustomerType->setUserList($resourceName);
            $userListCustomerType->setCustomerTypeCategory($customerTypeCategory);

            try {
                $client->userListCustomerTypes()->create($userListCustomerType);
            } catch (\Throwable) {
                // If a customer type already exists, Google Ads API will throw an error
                // This is expected behavior when re-uploading user list customer types (they are not updatable)
            }
        }
    }
}
