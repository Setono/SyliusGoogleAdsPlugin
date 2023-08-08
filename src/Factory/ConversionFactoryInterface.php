<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @extends FactoryInterface<ConversionInterface>
 */
interface ConversionFactoryInterface extends FactoryInterface
{
    public function createNew(): ConversionInterface;

    public function createFromOrder(OrderInterface $order): ConversionInterface;
}
