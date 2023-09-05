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

    private ?int $managerId = null;

    private ?int $customerId = null;

    private ?int $conversionActionId = null;

    private ?int $enhancedConversionActionId = null;

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

    public function getManagerId(): ?int
    {
        return $this->managerId;
    }

    public function setManagerId(?int $managerId): void
    {
        $this->managerId = $managerId;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(null|int|CustomerId $customerId): void
    {
        if ($customerId instanceof CustomerId) {
            $this->setManagerId($customerId->managerId);
            $customerId = $customerId->customerId;
        }

        $this->customerId = $customerId;
    }

    public function getConversionActionId(): ?int
    {
        return $this->conversionActionId;
    }

    public function setConversionActionId(null|int $conversionActionId): void
    {
        $this->conversionActionId = $conversionActionId;
    }

    public function getEnhancedConversionActionId(): ?int
    {
        return $this->enhancedConversionActionId;
    }

    public function setEnhancedConversionActionId(?int $enhancedConversionActionId): void
    {
        $this->enhancedConversionActionId = $enhancedConversionActionId;
    }
}
