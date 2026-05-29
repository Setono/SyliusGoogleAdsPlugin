<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Event;

use Google\Ads\GoogleAds\V24\Common\UserIdentifier;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @internal
 */
final class PreSetClickConversionUserIdentifiersEvent extends Event
{
    /** @var list<UserIdentifier> */
    public array $userIdentifiers = [];

    public function __construct(
        public readonly ConversionInterface $conversion,
    ) {
    }
}
