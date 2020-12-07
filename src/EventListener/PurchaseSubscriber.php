<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventListener;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusGoogleAdsPlugin\Event\GtagPurchaseEvent;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionActionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Setono\TagBag\DTO\PurchaseEventDTO;
use Setono\TagBag\Tag\GtagEvent;
use Setono\TagBag\Tag\GtagLibrary;
use Setono\TagBag\TagBagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class PurchaseSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private string $cookieName;

    public function __construct(RequestStack $requestStack, string $cookieName)
    {
        $this->requestStack = $requestStack;
        $this->cookieName = $cookieName;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => 'track',
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            return;
        }

        $request = $this->requestStack->getMasterRequest();
        if(null === $request) {
            return;
        }

        if(!$request->cookies->has($this->cookieName)) {
            return;
        }


    }
}
