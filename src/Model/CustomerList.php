<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;

/**
 * @experimental
 */
class CustomerList implements CustomerListInterface
{
    use TimestampableTrait;
    use ToggleableTrait;

    protected ?int $id = null;

    protected ?string $name = null;

    protected ?int $membershipLifespan = null;

    protected ?string $resourceName = null;

    protected ?\DateTimeInterface $uploadedAt = null;

    protected ?ChannelInterface $channel = null;

    protected ?int $customerTypeCategory = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getMembershipLifespan(): ?int
    {
        return $this->membershipLifespan;
    }

    public function setMembershipLifespan(?int $membershipLifespan): void
    {
        $this->membershipLifespan = $membershipLifespan;
    }

    public function getResourceName(): ?string
    {
        return $this->resourceName;
    }

    public function setResourceName(?string $resourceName): void
    {
        $this->resourceName = $resourceName;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploadedAt;
    }

    public function isUploaded(): bool
    {
        return null !== $this->uploadedAt;
    }

    public function setUploadedAt(?\DateTimeInterface $uploadedAt): void
    {
        $this->uploadedAt = $uploadedAt;
    }

    public function getCustomerTypeCategory(): ?int
    {
        return $this->customerTypeCategory;
    }

    public function setCustomerTypeCategory(?int $customerTypeCategory): void
    {
        $this->customerTypeCategory = $customerTypeCategory;
    }
}
