<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Event;

use Setono\SyliusGoogleAdsPlugin\Model\Consent;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PreSetClickConversionConsentEvent extends Event
{
    public function __construct(
        public readonly ConversionInterface $conversion,
        public readonly Consent $consent,
    ) {
    }
}
