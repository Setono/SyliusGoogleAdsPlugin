<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConsentChecker;

interface ConsentCheckerInterface
{
    /**
     * Returns true if the user has consented to sending data to Google
     */
    public function hasConsent(): bool;
}
