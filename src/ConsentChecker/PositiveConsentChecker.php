<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConsentChecker;

/**
 * This is just a default consent checker that will return true for all consents
 */
final class PositiveConsentChecker implements ConsentCheckerInterface
{
    public function hasUploadConversionConsent(): bool
    {
        return true;
    }

    public function hasUploadEnhancedConversionConsent(): bool
    {
        return true;
    }
}
