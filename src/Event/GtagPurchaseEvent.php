<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Event;

use Setono\TagBag\DTO\PurchaseEventDTO;
use Sylius\Component\Order\Model\OrderInterface;

final class GtagPurchaseEvent
{
    private PurchaseEventDTO $purchaseEventDTO;

    private OrderInterface $order;

    public function __construct(PurchaseEventDTO $purchaseEventDTO, OrderInterface $order)
    {
        $this->purchaseEventDTO = $purchaseEventDTO;
        $this->order = $order;
    }

    public function getPurchaseEventDTO(): PurchaseEventDTO
    {
        return $this->purchaseEventDTO;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }
}
