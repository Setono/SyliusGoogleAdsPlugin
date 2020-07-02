<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventListener;

use Setono\SyliusGoogleAdsPlugin\Event\GtagPurchaseEvent;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\TagBag\DTO\PurchaseEventDTO;
use Setono\TagBag\Tag\GtagEvent;
use Setono\TagBag\Tag\GtagLibrary;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;

final class PurchaseSubscriber extends TagSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => 'track',
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface || !$this->isShopContext()) {
            return;
        }

        $channel = $order->getChannel();
        if (null === $channel) {
            return;
        }

        $conversions = $this->getConversionsByCategory(ConversionInterface::CATEGORY_PURCHASE);

        if (count($conversions) === 0) {
            return;
        }

        foreach ($conversions as $conversion) {
            $dto = new PurchaseEventDTO(
                $conversion->getConversionId() . '/' . $conversion->getConversionLabel(),
                (string) $order->getCurrencyCode(),
                $this->formatMoney($order->getTotal()),
                (string) $order->getNumber()
            );

            $this->eventDispatcher->dispatch(new GtagPurchaseEvent($dto, $order));

            $this->tagBag->addTag(GtagEvent::createFromDTO($dto)->addDependency(GtagLibrary::NAME));
        }
    }
}
