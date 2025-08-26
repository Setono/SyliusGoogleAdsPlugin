<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @experimental
 */
interface CustomerListFactoryInterface extends FactoryInterface
{
    public function createNew(): CustomerListInterface;
}
