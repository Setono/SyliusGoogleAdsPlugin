<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Event;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class ConversionProcessingStartedEvent extends Event
{
    public bool $stopProcessing = false;

    public ?string $stopProcessingReason = null;

    public function __construct(public readonly ConversionInterface $conversion)
    {
    }

    public function stopProcessing(string $reason = null): void
    {
        $this->stopProcessing = true;
        $this->stopProcessingReason = $reason;
    }
}
