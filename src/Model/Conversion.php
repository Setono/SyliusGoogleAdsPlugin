<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Conversion implements ConversionInterface
{
    use TimestampableTrait;

    protected ?int $id = null;

    protected ?string $googleClickId = null;

    protected ?int $value = null;

    protected ?string $currencyCode = null;

    protected string $state = ConversionInterface::STATE_PENDING;

    protected ?\DateTimeImmutable $lastCheckedAt = null;

    protected \DateTimeImmutable $nextCheckAt;

    protected int $checks = 0;

    protected ?string $processIdentifier = null;

    protected ?ChannelInterface $channel = null;

    protected ?OrderInterface $order = null;

    public function __construct()
    {
        $this->nextCheckAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoogleClickId(): ?string
    {
        return $this->googleClickId;
    }

    public function setGoogleClickId(string $googleClickId): void
    {
        $this->googleClickId = $googleClickId;
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

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getLastCheckedAt(): ?\DateTimeImmutable
    {
        return $this->lastCheckedAt;
    }

    public function setLastCheckedAt(\DateTimeImmutable $lastCheckedAt): void
    {
        $this->lastCheckedAt = $lastCheckedAt;
    }

    public function getNextCheckAt(): \DateTimeImmutable
    {
        return $this->nextCheckAt;
    }

    public function setNextCheckAt(\DateTimeImmutable $nextCheck): void
    {
        $this->nextCheckAt = $nextCheck;
    }

    public function getChecks(): int
    {
        return $this->checks;
    }

    public function setChecks(int $checks): void
    {
        $this->checks = $checks;
    }

    public function incrementChecks(int $increment = 1): void
    {
        $this->checks += $increment;
    }

    public function getProcessIdentifier(): ?string
    {
        return $this->processIdentifier;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;
    }
}
