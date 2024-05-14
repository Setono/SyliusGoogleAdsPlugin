<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\TrackingInformation;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

abstract class AbstractTrackingInformationStorage implements TrackingInformationStorageInterface, EventSubscriberInterface, LoggerAwareInterface
{
    protected LoggerInterface $logger;

    protected ?TrackingInformation $trackingInformation = null;

    public function __construct()
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
                $value = TrackingInformation::fromQuery($value);
            } catch (\InvalidArgumentException) {
                return;
            }
        }

        $this->trackingInformation = $value;
    }

    abstract public function persist(ResponseEvent $event): void;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
