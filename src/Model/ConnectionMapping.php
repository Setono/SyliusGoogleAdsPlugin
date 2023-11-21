<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Setono\SyliusGoogleAdsPlugin\Resolver\CustomerId;
use Sylius\Component\Channel\Model\ChannelInterface;

class ConnectionMapping implements ConnectionMappingInterface
{
    private ?int $id = null;

    private ?ConnectionInterface $connection = null;

    private ?ChannelInterface $channel = null;

    private ?string $managerId = null;

    private ?string $customerId = null;

    private ?string $conversionActionId = null;

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

    public function getManagerId(): ?string
    {
        return $this->managerId;
    }

    public function setManagerId(?string $managerId): void
    {
        $this->managerId = $managerId;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(null|string|CustomerId $customerId): void
    {
        if ($customerId instanceof CustomerId) {
            $this->setManagerId($customerId->managerId);
            $customerId = $customerId->customerId;
        }

        $this->customerId = $customerId;
    }

    public function getConversionActionId(): ?string
    {
        return $this->conversionActionId;
    }

    public function setConversionActionId(?string $conversionActionId): void
    {
        $this->conversionActionId = $conversionActionId;
    }
}
