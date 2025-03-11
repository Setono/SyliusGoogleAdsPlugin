<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Event;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionActionInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * This event is fired when we want to persist a conversion based on an order.
 *
 * Listen to this event if you want to change values on the conversion before it's persisted
 */
final class PrePersistConversionFromOrderEvent
{
    /** @psalm-readonly */
    public ConversionInterface $conversion;

    /** @psalm-readonly */
    public ConversionActionInterface $conversionAction;

    /** @psalm-readonly */
    public OrderInterface $order;

    public function __construct(
        ConversionInterface $conversion,
        ConversionActionInterface $conversionAction,
        OrderInterface $order,
    ) {
        $this->conversion = $conversion;
        $this->conversionAction = $conversionAction;
        $this->order = $order;
    }
}
