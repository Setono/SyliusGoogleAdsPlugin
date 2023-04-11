<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

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

    public function getRefreshToken(): ?string;

    public function setRefreshToken(?string $refreshToken): void;
}
