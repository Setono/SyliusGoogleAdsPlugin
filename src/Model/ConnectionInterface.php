<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface ConnectionInterface extends ResourceInterface, ToggleableInterface, ChannelsAwareInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDeveloperToken(): ?string;

    public function setDeveloperToken(?string $developerToken): void;

    public function getClientId(): ?string;

    public function setClientId(?string $clientId): void;

    public function getClientSecret(): ?string;

    public function setClientSecret(?string $clientSecret): void;

    public function getAccessToken(): ?string;

    public function setAccessToken(?string $accessToken): void;

    /**
     * Returns true if this connection is able to authorize with Google (i.e. the required properties has been set)
     */
    public function canAuthorize(): bool;

    /**
     * @return Collection<int, ConnectionMappingInterface>
     */
    public function getConnectionMappings(): Collection;

    public function addConnectionMapping(ConnectionMappingInterface $connectionMapping): void;

    public function removeConnectionMapping(ConnectionMappingInterface $connectionMapping): void;

    public function hasConnectionMapping(ConnectionMappingInterface $connectionMapping): bool;
}
