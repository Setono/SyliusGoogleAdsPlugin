<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\TrackingInformation;

use Symfony\Component\HttpFoundation\Request;

interface TrackingInformationStorageInterface
{
    /**
     * If the $value is a Request object it will store any tracking information found in the request
     * If the $value is a TrackingInformation object it will store that
     */
    public function store(Request|TrackingInformation $value): void;

    /**
     * Returns the tracking information if any
     */
    public function get(): ?TrackingInformation;
}
