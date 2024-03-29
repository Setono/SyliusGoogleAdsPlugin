<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Webmozart\Assert\Assert;

class Conversion implements ConversionInterface
{
    use TimestampableTrait;

    protected ?int $id = null;

    protected ?int $version = 1;

    protected ?string $gclid = null;

    protected ?string $gbraid = null;

    protected ?string $wbraid = null;

    protected ?string $userAgent = null;

    protected ?int $value = null;

    protected ?string $currencyCode = null;

    protected ?Consent $consent = null;

    protected string $state = ConversionInterface::STATE_PENDING;

    protected ?string $previousState = null;

    protected ?\DateTimeImmutable $stateUpdatedAt = null;

    protected bool $processing = false;

    protected ?\DateTimeImmutable $lastProcessingStartedAt = null;

    protected ?\DateTimeImmutable $lastProcessingEndedAt = null;

    protected ?\DateTimeImmutable $nextProcessingAt = null;

    protected int $processingCount = 0;

    /** @var list<string> */
    protected array $logMessages = [];

    protected ?ChannelInterface $channel = null;

    protected ?OrderInterface $order = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): void
    {
        $this->version = $version;
    }

    public function getTrackingId(): string
    {
        $val = $this->{$this->getTrackingIdParameter()};
        Assert::nullOrString($val);

        if (null !== $val) {
            return $val;
        }

        throw new \RuntimeException('This conversion has no tracking id');
    }

    public function getTrackingIdParameter(): string
    {
        foreach (['gclid', 'gbraid', 'wbraid'] as $parameter) {
            if (null !== $this->{$parameter}) {
                return $parameter;
            }
        }

        throw new \RuntimeException('This conversion has no tracking id');
    }

    public function getGclid(): ?string
    {
        return $this->gclid;
    }

    public function setGclid(?string $gclid): void
    {
        $this->gclid = $gclid;
    }

    public function getGbraid(): ?string
    {
        return $this->gbraid;
    }

    public function setGbraid(?string $gbraid): void
    {
        $this->gbraid = $gbraid;
    }

    public function getWbraid(): ?string
    {
        return $this->wbraid;
    }

    public function setWbraid(?string $wbraid): void
    {
        $this->wbraid = $wbraid;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): void
    {
        $this->userAgent = $userAgent;
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

    public function getConsent(): ?Consent
    {
        return $this->consent;
    }

    public function setConsent(?Consent $consent): void
    {
        $this->consent = $consent;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getPreviousState(): ?string
    {
        return $this->previousState;
    }

    public function setPreviousState(?string $previousState): void
    {
        $this->previousState = $previousState;
    }

    public function getStateUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->stateUpdatedAt;
    }

    public function setStateUpdatedAt(\DateTimeImmutable $stateUpdatedAt): void
    {
        $this->stateUpdatedAt = $stateUpdatedAt;
    }

    public function isProcessing(): bool
    {
        return $this->processing;
    }

    public function setProcessing(bool $processing): void
    {
        $this->processing = $processing;
    }

    public function getLastProcessingStartedAt(): ?\DateTimeImmutable
    {
        return $this->lastProcessingStartedAt;
    }

    public function setLastProcessingStartedAt(\DateTimeImmutable $lastProcessingStartedAt): void
    {
        $this->lastProcessingStartedAt = $lastProcessingStartedAt;
    }

    public function getLastProcessingEndedAt(): ?\DateTimeImmutable
    {
        return $this->lastProcessingEndedAt;
    }

    public function setLastProcessingEndedAt(\DateTimeImmutable $lastProcessingEndedAt): void
    {
        $this->lastProcessingEndedAt = $lastProcessingEndedAt;
    }

    public function getNextProcessingAt(): ?\DateTimeImmutable
    {
        return $this->nextProcessingAt;
    }

    public function setNextProcessingAt(?\DateTimeImmutable $nextProcessingAt): void
    {
        $this->nextProcessingAt = $nextProcessingAt;
    }

    public function getProcessingCount(): int
    {
        return $this->processingCount;
    }

    public function setProcessingCount(int $processingCount): void
    {
        $this->processingCount = $processingCount;
    }

    public function incrementProcessingCount(int $increment = 1): void
    {
        $this->processingCount += $increment;
    }

    public function getLogMessages(): array
    {
        return $this->logMessages;
    }

    public function setLogMessages(array $logMessages): void
    {
        $this->logMessages = [];

        foreach ($logMessages as $logMessage) {
            $this->addLogMessage($logMessage);
        }
    }

    public function addLogMessage(array|string $logMessage): void
    {
        $logMessage = (array) $logMessage;
        foreach ($logMessage as $value) {
            $this->logMessages[] = sprintf('[%s] %s', (new \DateTimeImmutable())->format('Y-m-d H:i:s'), $value);
        }
    }

    public function hasLogMessages(): bool
    {
        return [] !== $this->logMessages;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
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

    public function isFailed(): bool
    {
        return $this->getState() === self::STATE_FAILED;
    }
}
