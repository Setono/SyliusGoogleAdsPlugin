<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\TrackingInformation;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CookieBasedTrackingInformationStorage implements TrackingInformationStorageInterface, EventSubscriberInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    private ?TrackingInformation $trackingInformation = null;

    public function __construct(private readonly RequestStack $requestStack, private readonly string $cookieName)
    {
        $this->logger = new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'persist',
        ];
    }

    public function store(Request|TrackingInformation $value): void
    {
        if ($value instanceof Request) {
            try {
                $value = TrackingInformation::fromRequest($value);
            } catch (\InvalidArgumentException) {
                return;
            }
        }

        $this->trackingInformation = $value;
    }

    public function get(): ?TrackingInformation
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return null;
        }

        $encodedCookieValue = $request->cookies->get($this->cookieName);
        if (!is_string($encodedCookieValue) || '' === $encodedCookieValue) {
            return null;
        }

        $decodedCookieValue = base64_decode($encodedCookieValue, true);
        if (false === $decodedCookieValue) {
            $this->logger->error(sprintf(
                'The tracking information cookie was present, but the data was corrupt. The encoded data was: "%s"',
                $encodedCookieValue,
            ));

            return null;
        }

        try {
            return TrackingInformation::fromJson($decodedCookieValue);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf(
                'The tracking information cookie was present, but the data was corrupt. The JSON was: "%s" and the error was: %s',
                $decodedCookieValue,
                $e->getMessage(),
            ));

            return null;
        }
    }

    public function persist(ResponseEvent $event): void
    {
        if (null === $this->trackingInformation) {
            return;
        }

        try {
            $json = json_encode($this->trackingInformation, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logger->error(sprintf('Could not JSON encode the tracking information. The error was: %s', $e->getMessage()));

            return;
        }

        $event->getResponse()->headers->setCookie(Cookie::create(
            $this->cookieName,
            base64_encode($json),
            new \DateTimeImmutable('+90 days'), // this should be set to the 'Click-through conversion window' in your Google conversion action settings
            null,
            null,
            false,
            false,
        ));
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
