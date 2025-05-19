<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MerchantMappingInterface extends ResourceInterface, ChannelAwareInterface
{
    public function getId(): ?int;

    public function getMerchantId(): ?string;

    public function setMerchantId(?string $merchantId): void;
}
