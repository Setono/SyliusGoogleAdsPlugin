<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Factory;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ConversionFactory implements ConversionFactoryInterface
{
    public function __construct(private readonly FactoryInterface $decorated)
    {
    }

    public function createNew(): ConversionInterface
    {
        /** @var ConversionInterface|object $conversion */
        $conversion = $this->decorated->createNew();
        Assert::isInstanceOf($conversion, ConversionInterface::class);

        // There's no reason to process a conversion immediately after it has been saved to the database
        // because in all likelihood the payment has not been processed yet
        $conversion->setNextProcessingAt(new \DateTimeImmutable('+10 minutes'));

        return $conversion;
    }

    public function createFromOrder(OrderInterface $order): ConversionInterface
    {
        $conversion = $this->createNew();
        $conversion->setValue($order->getTotal());
        $conversion->setCurrencyCode((string) $order->getCurrencyCode());
        $conversion->setOrder($order);

        $clickId = $order->getGoogleClickId();
        if (null !== $clickId) {
            $conversion->setGoogleClickId($clickId);
        }

        return $conversion;
    }
}
