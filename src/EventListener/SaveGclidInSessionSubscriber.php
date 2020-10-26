<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SaveGclidInSessionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'save',
        ];
    }

    public function save(ResponseEvent $event): void
    {
        if(!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if(!$request->query->has('gclid')) {
            return;
        }

        $session = $request->getSession();
        $session->set('setono_sylius_google_ads_gclid', $request->query->get('gclid'));
    }
}
