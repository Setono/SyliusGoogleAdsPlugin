<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface ConversionInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    public function getGoogleClickId(): ?string;

    public function setGoogleClickId(string $googleClickId): void;

    public function getCategory(): ?string;

    public function setCategory(string $category): void;

    public function getValue(): ?int;

    public function setValue(string $value): void;

    public function getCurrencyCode(): ?string;

    public function setCurrencyCode(string $currencyCode): void;
}
