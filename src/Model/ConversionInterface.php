<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ConversionInterface extends ResourceInterface, TimestampableInterface
{
    public const STATE_PENDING = 'pending';

    public const STATE_READY = 'ready';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_PROCESSING = 'processing';

    public const STATE_DELIVERED = 'delivered';

    public function getId(): ?int;

    public function getGoogleClickId(): ?string;

    public function setGoogleClickId(string $googleClickId): void;

    public function getValue(): ?int;

    public function setValue(int $value): void;

    public function getCurrencyCode(): ?string;

    public function setCurrencyCode(string $currencyCode): void;

    public function getState(): string;

    public function setState(string $state): void;

    /**
     * The last time a conversion was checked for state.
     * If null it means that the conversion hasn't been checked yet
     */
    public function getLastCheckedAt(): ?\DateTimeImmutable;

    /**
     * Sets the last time a conversion was checked for state
     */
    public function setLastCheckedAt(\DateTimeImmutable $lastCheckedAt): void;

    /**
     * The next time a conversion should be checked for state.
     */
    public function getNextCheckAt(): \DateTimeImmutable;

    /**
     * Sets the next time a conversion should be checked for state
     */
    public function setNextCheckAt(\DateTimeImmutable $nextCheck): void;

    /**
     * Returns the number of times a conversion has been checked for state
     */
    public function getChecks(): int;

    public function setChecks(int $checks): void;

    public function incrementChecks(int $increment = 1): void;

    public function getProcessIdentifier(): ?string;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(ChannelInterface $channel): void;

    public function getOrder(): ?OrderInterface;

    public function setOrder(OrderInterface $order): void;
}
