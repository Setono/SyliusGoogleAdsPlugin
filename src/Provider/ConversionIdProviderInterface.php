<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Provider;

interface ConversionIdProviderInterface
{
    /**
     * @return iterable<int>
     */
    public function getConversionIds(): iterable;
}
