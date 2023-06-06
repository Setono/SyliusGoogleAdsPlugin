<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConsentChecker;

interface ConsentCheckerInterface
{
    /**
     * Returns true if the user has consented to uploading conversions to Google
     */
    public function hasUploadConversionConsent(): bool;

    /**
     * Returns true if the user has consented to uploading enhanced conversions to Google
     */
    public function hasUploadEnhancedConversionConsent(): bool;
}
