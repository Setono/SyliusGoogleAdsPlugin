<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber;

use Setono\SyliusGoogleAdsPlugin\TrackingInformation\TrackingInformationStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class StoreTrackingInformationSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TrackingInformationStorageInterface $trackingInformationStorage)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'store',
        ];
    }

    public function store(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->trackingInformationStorage->store($event->getRequest());
    }
}
