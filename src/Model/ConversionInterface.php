<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ConversionInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(string $name): void;

    public function getGoogleClickId(): ?string;

    public function setGoogleClickId(string $googleClickId): void;

    public function getCategory(): ?string;

    public function setCategory(string $category): void;

    public function getValue(): ?int;

    public function setValue(int $value): void;

    public function getCurrencyCode(): ?string;

    public function setCurrencyCode(string $currencyCode): void;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(ChannelInterface $channel): void;
}
