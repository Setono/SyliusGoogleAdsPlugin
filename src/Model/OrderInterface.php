<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Core\Model\OrderInterface as BaseOrderInterface;

interface OrderInterface extends BaseOrderInterface
{
    /**
     * Returns the related Google click id to this order (if any)
     */
    public function getGoogleClickId(): ?string;

    public function setGoogleClickId(?string $googleClickId): void;
}
