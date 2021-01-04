<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConsentChecker;

final class AlwaysGivenConsentChecker implements ConsentCheckerInterface
{
    public function hasConsent(): bool
    {
        return true;
    }
}
