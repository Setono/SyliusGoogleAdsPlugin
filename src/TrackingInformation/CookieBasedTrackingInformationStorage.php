<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\TrackingInformation;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class CookieBasedTrackingInformationStorage extends AbstractTrackingInformationStorage
{
    public function __construct(private readonly RequestStack $requestStack, private readonly string $cookieName)
    {
        parent::__construct();
    }

    public function get(): ?TrackingInformation
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return null;
        }

        try {
            return TrackingInformation::fromCookie($request, $this->cookieName);
        } catch (\Throwable $e) {
            $this->logger->error($e);

            return null;
        }
    }

    public function persist(ResponseEvent $event): void
    {
        if (null === $this->trackingInformation) {
            return;
        }

        try {
            $cookie = $this->trackingInformation->toCookie(
                $this->cookieName,
                new \DateTimeImmutable('+90 days'), // todo this should be set to the 'Click-through conversion window' in your Google conversion action settings
            );
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Could not create a cookie based on the tracking information. The error was: %s', $e->getMessage()));

            return;
        }

        $event->getResponse()->headers->setCookie($cookie);
    }
}
