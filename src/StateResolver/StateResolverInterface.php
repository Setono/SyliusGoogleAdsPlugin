<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\StateResolver;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;

interface StateResolverInterface
{
    /**
     * Returns the resolved conversion state. MUST return the initial state if it can't resolve anything
     *
     * Returns one of pending, ready, cancelled.
     *
     * Ready means it will be downloaded by Google
     * Pending means it will be tried to be resolved
     * Cancelled means it will never be downloaded by Google
     */
    public function resolve(ConversionInterface $conversion): string;
}
