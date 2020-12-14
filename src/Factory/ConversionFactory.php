<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ConversionFactory implements ConversionFactoryInterface
{
    private FactoryInterface $decorated;

    public function __construct(FactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function createNew(): ConversionInterface
    {
        /** @var ConversionInterface $conversion */
        $conversion = $this->decorated->createNew();

        return $conversion;
    }

    public function createFromOrder(OrderInterface $order): ConversionInterface
    {
        $conversion = $this->createNew();
        $conversion->setValue($order->getTotal());
        $conversion->setCurrencyCode((string) $order->getCurrencyCode());

        return $conversion;
    }
}
