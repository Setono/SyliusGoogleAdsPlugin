<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;

interface ConversionProcessorInterface
{
    public function process(ConversionInterface $conversion): void;

    /**
     * Returns true if this conversion processor is eligible to run.
     * This method MUST be called before process.
     */
    public function isEligible(ConversionInterface $conversion): bool;
}
