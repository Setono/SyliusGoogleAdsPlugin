<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\StateResolver;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;

interface StateResolverInterface
{
    /**
     * Returns the resolved conversion state
     */
    public function resolve(ConversionInterface $conversion): string;
}
