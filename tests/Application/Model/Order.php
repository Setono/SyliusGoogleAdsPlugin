<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGoogleAdsPlugin\Application\Model;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface as SetonoSyliusGoogleAdsOrderInterface;
use Setono\SyliusGoogleAdsPlugin\Model\OrderTrait as SetonoSyliusGoogleAdsOrderTrait;
use Sylius\Component\Core\Model\Order as BaseOrder;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_order")
 */
class Order extends BaseOrder implements SetonoSyliusGoogleAdsOrderInterface
{
    use SetonoSyliusGoogleAdsOrderTrait;
}
