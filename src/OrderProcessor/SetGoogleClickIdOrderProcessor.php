<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\OrderProcessor;

use Setono\SyliusGoogleAdsPlugin\Exception\WrongOrderTypeException;
use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class SetGoogleClickIdOrderProcessor implements OrderProcessorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly string $cookieName,
    ) {
    }

    /**
     * @param BaseOrderInterface|OrderInterface $order
     */
    public function process(BaseOrderInterface $order): void
    {
        WrongOrderTypeException::assert($order);

        $cookieValue = (string) $this->requestStack->getMainRequest()?->cookies->get($this->cookieName);
        if ('' === $cookieValue) {
            return;
        }

        $order->setGoogleClickId($cookieValue);
    }
}
