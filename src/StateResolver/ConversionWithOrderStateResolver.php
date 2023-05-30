<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\StateResolver;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;

/**
 * This state resolver more or less mimics the default behavior of the Google Ads javascript tracking snippet
 * which is injected on the 'success' page which usually means the order is completed and paid, but hasn't been shipped
 */
final class ConversionWithOrderStateResolver implements StateResolverInterface
{
    public function resolve(ConversionInterface $conversion): string
    {
        $initialState = $conversion->getState();

        $order = $conversion->getOrder();
        if (null === $order) {
            return $initialState;
        }

        if (!$order instanceof OrderInterface) {
            return $initialState;
        }

        if ($order->getPaymentState() === OrderPaymentStates::STATE_CANCELLED) {
            return ConversionInterface::STATE_CANCELLED;
        }

        if (OrderInterface::STATE_CANCELLED === $order->getState()) {
            return ConversionInterface::STATE_CANCELLED;
        }

        if (!in_array($order->getPaymentState(), [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_AUTHORIZED], true)) {
            return $initialState;
        }

        return ConversionInterface::STATE_READY;
    }
}
