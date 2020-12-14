<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Conversion implements ConversionInterface
{
    use TimestampableTrait;

    protected ?int $id = null;

    protected ?string $name = null;

    protected ?string $googleClickId = null;

    protected ?string $category = null;

    protected ?int $value = null;

    protected ?string $currencyCode = null;

    protected ?ChannelInterface $channel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getGoogleClickId(): ?string
    {
        return $this->googleClickId;
    }

    public function setGoogleClickId(string $googleClickId): void
    {
        $this->googleClickId = $googleClickId;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }
}
