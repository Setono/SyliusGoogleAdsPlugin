<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber\ConversionProcessing;

use Setono\SyliusGoogleAdsPlugin\Event\ConversionProcessingStartedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class StartProcessingSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConversionProcessingStartedEvent::class => 'start',
        ];
    }

    public function start(ConversionProcessingStartedEvent $event): void
    {
        $event->conversion->setLastProcessingStartedAt(new \DateTimeImmutable());
        $event->conversion->setProcessing(true);
        $event->conversion->setLogMessages(['Processing started']);
    }
}
