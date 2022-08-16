<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\OrderProcessor;

use Setono\MainRequestTrait\MainRequestTrait;
use Setono\SyliusGoogleAdsPlugin\Exception\WrongOrderTypeException;
use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class SetGoogleClickIdOrderProcessor implements OrderProcessorInterface
{
    use MainRequestTrait;

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
        WrongOrderTypeException::assert($order);

        $request = $this->getMainRequestFromRequestStack($this->requestStack);
        if (null === $request) {
            return;
        }

        if (!$request->cookies->has($this->cookieName)) {
            return;
        }

        $order->setGoogleClickId((string) $request->cookies->get($this->cookieName));
    }
}
