<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;

class Connection implements ConnectionInterface
{
    use ToggleableTrait;

    protected ?int $id = null;

    protected ?string $name = null;

    protected ?string $developerToken = null;

    protected ?string $clientId = null;

    protected ?string $clientSecret = null;

    protected ?string $accessToken = null;

    /**
     * todo remove this
     *
     * @var Collection|BaseChannelInterface[]
     *
     * @psalm-var Collection<array-key, BaseChannelInterface>
     */
    protected Collection $channels;

    /** @var Collection<int, ConnectionMappingInterface> */
    protected Collection $connectionMappings;

    public function __construct()
    {
        $this->channels = new ArrayCollection();
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

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function canAuthorize(): bool
    {
        return null !== $this->clientId && null !== $this->clientSecret && null !== $this->developerToken;
    }

    // todo remove
    public function getGoogleAdsCustomerId(BaseChannelInterface $channel): ?int
    {
        return 1;
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
