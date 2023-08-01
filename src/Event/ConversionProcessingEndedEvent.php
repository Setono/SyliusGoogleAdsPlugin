<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Event;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class ConversionProcessingEndedEvent extends Event
{
    public function __construct(public readonly ConversionInterface $conversion)
    {
    }
}
