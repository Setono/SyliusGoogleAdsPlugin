<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

/**
 * @experimental
 */
interface CustomerListInterface extends ResourceInterface, ChannelAwareInterface, ToggleableInterface, TimestampableInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): void;

    /**
     * The number of days a customer on this list is considered a member.
     * After this period, the customer will be evicted from the list unless the customer performed another purchase
     */
    public function getMembershipLifespan(): ?int;

    public function setMembershipLifespan(?int $membershipLifespan): void;

    /**
     * This is the immutable resource name from Google (format: customers/{customer_id}/userLists/{user_list_id})
     */
    public function getResourceName(): ?string;

    public function setResourceName(?string $resourceName): void;

    /**
     * Denotes the last time this customer list was uploaded to Google. If null, the customer hasn't been uploaded yet
     */
    public function getUploadedAt(): ?\DateTimeInterface;

    /**
     * Returns true if the customer list was uploaded successfully to Google
     */
    public function isUploaded(): bool;

    public function setUploadedAt(?\DateTimeInterface $uploadedAt): void;

    /**
     * Get the customer type category for this user list
     *
     * @return int|null UserListCustomerTypeCategory enum value
     */
    public function getCustomerTypeCategory(): ?int;

    /**
     * Set the customer type category for this user list
     *
     * @param int|null $customerTypeCategory UserListCustomerTypeCategory enum value
     */
    public function setCustomerTypeCategory(?int $customerTypeCategory): void;
}
