<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Event;

use Google\Ads\GoogleAds\V19\Services\ClickConversion;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PreUploadConversionEvent extends Event
{
    public function __construct(
        public readonly ClickConversion $clickConversion,
        public readonly ConversionInterface $conversion,
    ) {
    }
}
