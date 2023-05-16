<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Setono\SyliusGoogleAdsPlugin\Resolver\CustomerId;
use Sylius\Component\Channel\Model\ChannelInterface;

final class ConnectionMapping implements ConnectionMappingInterface
{
    private ?int $id = null;

    private ?ConnectionInterface $connection = null;

    private ?ChannelInterface $channel = null;

    private ?int $customerId = null;

    private ?int $conversionActionId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConnection(): ?ConnectionInterface
    {
        return $this->connection;
    }

    public function setConnection(?ConnectionInterface $connection): void
    {
        $this->connection = $connection;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(null|int|CustomerId $customerId): void
    {
        if ($customerId instanceof CustomerId) {
            $customerId = $customerId->customerId;
        }

        $this->customerId = $customerId;
    }

    public function getConversionActionId(): ?int
    {
        return $this->conversionActionId;
    }

    public function setConversionActionId(?int $conversionActionId): void
    {
        $this->conversionActionId = $conversionActionId;
    }
}
