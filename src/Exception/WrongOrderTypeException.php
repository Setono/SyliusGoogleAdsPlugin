<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Exception;

use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class WrongOrderTypeException
{
    /**
     * @psalm-assert OrderInterface $order
     */
    public static function assert(mixed $order): void
    {
        Assert::isInstanceOf($order, OrderInterface::class, sprintf(
            'You must implement the %s in your Sylius application. Read the readme here: https://github.com/Setono/SyliusGoogleAdsPlugin',
            OrderInterface::class,
        ));
    }
}
