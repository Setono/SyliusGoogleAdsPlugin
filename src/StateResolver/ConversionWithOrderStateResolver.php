<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\StateResolver;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

final class ConversionWithOrderStateResolver implements StateResolverInterface
{
    public function resolve(ConversionInterface $conversion): string
    {
        $order = $conversion->getOrder();
        if (null === $order) {
            return $conversion->getState();
        }

        $state = $conversion->getState();

        if (!$order instanceof OrderInterface) {
            return $state;
        }

        if (!in_array($order->getPaymentState(), [PaymentInterface::STATE_COMPLETED, PaymentInterface::STATE_AUTHORIZED], true)) {
            return $state;
        }

        return ConversionInterface::STATE_READY;
    }
}
