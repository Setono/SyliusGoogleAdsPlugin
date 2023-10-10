<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber;

use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This subscriber will store the gclid in a cookie when a user enters the website with the ?gclid query parameter set
 */
final class StoreGclidSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly string $cookieName)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'save',
        ];
    }

    public function save(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->query->has('gclid')) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->setCookie(Cookie::create(
            $this->cookieName,
            (string) $request->query->get('gclid'),
            new DateTimeImmutable('+90 days'), // this should be set to the 'Click-through conversion window' in your Google conversion action settings
            null,
            null,
            false,
            false,
        ));
    }
}
