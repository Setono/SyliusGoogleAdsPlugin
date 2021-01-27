<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ConversionFactory implements ConversionFactoryInterface
{
    private FactoryInterface $decorated;

    /** @var array<string, string> */
    private array $defaultStates;

    /**
     * @param array<string, string> $defaultStates
     */
    public function __construct(FactoryInterface $decorated, array $defaultStates)
    {
        $this->decorated = $decorated;
        $this->defaultStates = $defaultStates;
    }

    public function createNew(string $category = null): ConversionInterface
    {
        /** @var ConversionInterface $conversion */
        $conversion = $this->decorated->createNew();

        if (null !== $category) {
            $conversion->setCategory($category);
            $conversion->setState($this->defaultStates[$category] ?? ConversionInterface::STATE_READY);
        }

        return $conversion;
    }

    public function createFromOrder(OrderInterface $order, string $category): ConversionInterface
    {
        $conversion = $this->createNew($category);
        $conversion->setValue($order->getTotal());
        $conversion->setCurrencyCode((string) $order->getCurrencyCode());
        $conversion->setOrder($order);

        return $conversion;
    }
}
