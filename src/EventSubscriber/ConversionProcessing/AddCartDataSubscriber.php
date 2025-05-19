<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber\ConversionProcessing;

use Google\Ads\GoogleAds\V19\Services\CartData;
use Setono\SyliusGoogleAdsPlugin\Event\PreSetClickConversionDataEvent;
use Setono\SyliusGoogleAdsPlugin\Repository\MerchantMappingRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddCartDataSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MerchantMappingRepositoryInterface $merchantMappingRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PreSetClickConversionDataEvent::class => 'add',
        ];
    }

    public function add(PreSetClickConversionDataEvent $event): void
    {
        $order = $event->conversion->getOrder();
        if (null === $order) {
            return;
        }

        $channel = $order->getChannel();
        if (null === $channel) {
            return;
        }

        $localeCode = $order->getLocaleCode();
        if (null === $localeCode) {
            return;
        }

        $merchantId = $this->merchantMappingRepository->findOneByChannel($channel)?->getMerchantId();
        if (null === $merchantId) {
            return;
        }

        $countryCode = $order->getBillingAddress()?->getCountryCode();
        if (null === $countryCode) {
            return;
        }

        $items = self::getItems($order);
        if ([] === $items) {
            return;
        }

        $event->data['cart_data'] = new CartData([
            'merchant_id' => $merchantId,
            'feed_country_code' => $countryCode,
            'feed_language_code' => $localeCode,
            'local_transaction_cost' => 0, // TODO: Sum of all transaction level discounts, such as free shipping and coupon discounts for the whole cart. The currency code is the same as that in the ClickConversion message.
            'items' => $items,
        ]);
    }

    /**
     * @return list<CartData\Item>
     */
    private static function getItems(OrderInterface $order): array
    {
        $items = [];

        foreach ($order->getItems() as $item) {
            $productId = $item->getVariant()?->getCode();
            if (null === $productId) {
                continue;
            }

            $items[] = new CartData\Item([
                'product_id' => $productId,
                'quantity' => $item->getQuantity(),
                'unit_price' => round($item->getUnitPrice() / 100, 2),
            ]);
        }

        return $items;
    }
}
