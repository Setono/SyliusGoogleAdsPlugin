<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ToggleableTrait;

class Connection implements ConnectionInterface
{
    use ToggleableTrait;

    protected ?int $id = null;

    protected ?string $name = null;

    protected ?string $developerToken = null;

    protected ?string $clientId = null;

    protected ?string $clientSecret = null;

    protected ?string $refreshToken = null;

    /** @var Collection<int, ConnectionMappingInterface> */
    protected Collection $connectionMappings;

    public function __construct()
    {
        $this->connectionMappings = new ArrayCollection();
    }

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

    public function getDeveloperToken(): ?string
    {
        return $this->developerToken;
    }

    public function setDeveloperToken(?string $developerToken): void
    {
        $this->developerToken = $developerToken;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(?string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function canAuthorize(): bool
    {
        return null !== $this->clientId && null !== $this->clientSecret && null !== $this->developerToken;
    }

    public function getConnectionMappings(): Collection
    {
        return $this->connectionMappings;
    }

    public function addConnectionMapping(ConnectionMappingInterface $connectionMapping): void
    {
        if (!$this->hasConnectionMapping($connectionMapping)) {
            $this->connectionMappings->add($connectionMapping);
            $connectionMapping->setConnection($this);
        }
    }

    public function removeConnectionMapping(ConnectionMappingInterface $connectionMapping): void
    {
        if ($this->hasConnectionMapping($connectionMapping)) {
            $this->connectionMappings->removeElement($connectionMapping);
            $connectionMapping->setConnection(null);
        }
    }

    public function hasConnectionMapping(ConnectionMappingInterface $connectionMapping): bool
    {
        return $this->connectionMappings->contains($connectionMapping);
    }
}
