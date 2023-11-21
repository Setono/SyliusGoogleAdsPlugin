<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber;

use Setono\SyliusGoogleAdsPlugin\TrackingInformation\TrackingInformation;
use Setono\SyliusGoogleAdsPlugin\TrackingInformation\TrackingInformationStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class MigrateLegacyCookieSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TrackingInformationStorageInterface $trackingInformationStorage,
        private readonly string $legacyCookieName,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['store', 1], // should be run before \Setono\SyliusGoogleAdsPlugin\TrackingInformation\CookieBasedTrackingInformationStorage::persist
        ];
    }

    public function store(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $cookieValue = $event->getRequest()->cookies->get($this->legacyCookieName);
        if (!is_string($cookieValue) || '' === $cookieValue) {
            return;
        }

        $this->trackingInformationStorage->store(new TrackingInformation($cookieValue, null, null));

        // remove the legacy cookie
        $event->getResponse()->headers->clearCookie($this->legacyCookieName);
    }
}
