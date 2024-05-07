<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber;

use Setono\SyliusGoogleAdsPlugin\TrackingInformation\TrackingInformation;
use Setono\SyliusGoogleAdsPlugin\TrackingInformation\TrackingInformationStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This subscriber is responsible for migrating the storage of the tracking information from the cookie to the client metadata.
 * This subscriber should only be used when the storage is set to client_metadata
 */
final class MigrateStorageSubscriber implements EventSubscriberInterface
{
    private ?TrackingInformation $trackingInformation = null;

    public function __construct(
        private readonly TrackingInformationStorageInterface $trackingInformationStorage,
        private readonly string $cookieName,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['migrate', 5], // we want to run this before the StoreTrackingInformationSubscriber
            KernelEvents::RESPONSE => 'remove',
        ];
    }

    public function migrate(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        try {
            $this->trackingInformation = TrackingInformation::fromCookie($event->getRequest(), $this->cookieName);
        } catch (\Throwable) {
            return;
        }

        $this->trackingInformationStorage->store($this->trackingInformation);
    }

    public function remove(ResponseEvent $event): void
    {
        if (null === $this->trackingInformation || !$event->isMainRequest()) {
            return;
        }

        try {
            $cookie = $this->trackingInformation->toCookie($this->cookieName, 1);
        } catch (\Throwable) {
            return;
        }

        $event->getResponse()->headers->setCookie($cookie);
    }
}
