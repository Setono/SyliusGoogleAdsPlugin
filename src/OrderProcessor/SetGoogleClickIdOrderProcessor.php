<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\OrderProcessor;

use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class SetGoogleClickIdOrderProcessor implements OrderProcessorInterface
{
    private RequestStack $requestStack;

    private string $cookieName;

    public function __construct(RequestStack $requestStack, string $cookieName)
    {
        $this->requestStack = $requestStack;
        $this->cookieName = $cookieName;
    }

    /**
     * @param BaseOrderInterface|OrderInterface $order
     */
    public function process(BaseOrderInterface $order): void
    {
        Assert::isInstanceOf($order, OrderInterface::class, sprintf(
            'You must implement the %s in your Sylius application. Read the readme here: https://github.com/Setono/SyliusGoogleAdsPlugin',
            OrderInterface::class
        ));

        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return;
        }

        if (!$request->cookies->has($this->cookieName)) {
            return;
        }

        $order->setGoogleClickId((string) $request->cookies->get($this->cookieName));
    }
}
