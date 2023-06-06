<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;

interface ConversionProcessorInterface
{
    public function process(ConversionInterface $conversion): void;
}
