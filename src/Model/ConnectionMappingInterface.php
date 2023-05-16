<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Setono\SyliusGoogleAdsPlugin\Resolver\CustomerId;
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ConnectionMappingInterface extends ResourceInterface, ChannelAwareInterface
{
    public function getId(): ?int;

    public function getConnection(): ?ConnectionInterface;

    public function setConnection(?ConnectionInterface $connection): void;

    public function getCustomerId(): ?int;

    public function setCustomerId(null|int|CustomerId $customerId): void;

    public function getConversionActionId(): ?int;

    public function setConversionActionId(?int $conversionActionId): void;
}
