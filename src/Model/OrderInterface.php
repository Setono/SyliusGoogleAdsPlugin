<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

use Sylius\Component\Core\Model\OrderInterface as BaseOrderInterface;

// todo: Do we really need this order interface? Why not put these values on the conversion instead?
interface OrderInterface extends BaseOrderInterface
{
    /**
     * Returns the related Google click id to this order (if any)
     */
    public function getGoogleClickId(): ?string;

    public function setGoogleClickId(?string $googleClickId): void;

    /**
     * The user agent of the user completing the order
     */
    public function getUserAgent(): ?string;

    public function setUserAgent(?string $userAgent): void;
}
