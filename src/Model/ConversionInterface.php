<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\VersionedInterface;

interface ConversionInterface extends ResourceInterface, TimestampableInterface, VersionedInterface
{
    public const STATE_PENDING = 'pending';

    public const STATE_QUALIFIED = 'qualified';

    public const STATE_DISQUALIFIED = 'disqualified';

    public const STATE_FAILED = 'failed';

    public const STATE_CONVERSION_UPLOADED = 'conversion_uploaded';

    public const STATE_DELIVERED = 'delivered';

    public function getId(): ?int;

    /**
     * Returns the value of either gclid, gbraid or wbraid (in that order)
     *
     * @throws \RuntimeException if none of the tracking ids are set
     */
    public function getTrackingId(): string;

    /**
     * Returns the name of the tracking id parameter (gclid, gbraid or wbraid)
     */
    public function getTrackingIdParameter(): string;

    public function getGclid(): ?string;

    public function setGclid(?string $gclid): void;

    public function getGbraid(): ?string;

    public function setGbraid(?string $gbraid): void;

    public function getWbraid(): ?string;

    public function setWbraid(?string $wbraid): void;

    /**
     * The user agent of the user completing the order
     */
    public function getUserAgent(): ?string;

    public function setUserAgent(?string $userAgent): void;

    public function getValue(): ?int;

    public function setValue(int $value): void;

    public function getCurrencyCode(): ?string;

    public function setCurrencyCode(string $currencyCode): void;

    public function setConsent(?Consent $consent): void;

    public function getConsent(): ?Consent;

    public function getState(): string;

    public function setState(string $state): void;

    public function getPreviousState(): ?string;

    public function setPreviousState(string $previousState): void;

    /**
     * Returns the last time the state was updated or null if it hasn't been updated yet
     */
    public function getStateUpdatedAt(): ?\DateTimeImmutable;

    public function setStateUpdatedAt(\DateTimeImmutable $stateUpdatedAt): void;

    /**
     * Returns true if the conversion is being processed
     */
    public function isProcessing(): bool;

    public function setProcessing(bool $processing): void;

    /**
     * The last time a processing was started on this conversion.
     * If null it means that the conversion hasn't been tried processed yet
     */
    public function getLastProcessingStartedAt(): ?\DateTimeImmutable;

    public function setLastProcessingStartedAt(\DateTimeImmutable $lastProcessingStartedAt): void;

    /**
     * The last time a conversion was processed.
     * If null it means that the conversion hasn't been processed yet
     */
    public function getLastProcessingEndedAt(): ?\DateTimeImmutable;

    /**
     * Sets the last time a conversion was processed
     */
    public function setLastProcessingEndedAt(\DateTimeImmutable $lastProcessingEndedAt): void;

    /**
     * The next time a conversion should be processed or null if it should not be processed
     */
    public function getNextProcessingAt(): ?\DateTimeImmutable;

    /**
     * Sets the next time a conversion should be processed.
     * If the conversion should not be processed again, set this value to null
     */
    public function setNextProcessingAt(?\DateTimeImmutable $nextProcessingAt): void;

    /**
     * Returns the number of times a conversion has been processed
     */
    public function getProcessingCount(): int;

    public function setProcessingCount(int $processingCount): void;

    public function incrementProcessingCount(int $increment = 1): void;

    /**
     * @return list<string>
     */
    public function getLogMessages(): array;

    /**
     * @param list<string> $logMessages
     */
    public function setLogMessages(array $logMessages): void;

    /**
     * @param list<string>|string $logMessage
     */
    public function addLogMessage(array|string $logMessage): void;

    public function hasLogMessages(): bool;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    public function getOrder(): ?OrderInterface;

    public function setOrder(OrderInterface $order): void;

    /**
     * Returns true if the state equals 'failed'
     */
    public function isFailed(): bool;
}
