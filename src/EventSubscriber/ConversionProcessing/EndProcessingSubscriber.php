<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber\ConversionProcessing;

use Setono\SyliusGoogleAdsPlugin\Event\ConversionProcessingEndedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EndProcessingSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConversionProcessingEndedEvent::class => 'end',
        ];
    }

    public function end(ConversionProcessingEndedEvent $event): void
    {
        $now = new \DateTimeImmutable();

        $event->conversion->setProcessing(false);
        $event->conversion->setLastProcessingEndedAt($now);
        $event->conversion->incrementProcessingCount();
        $event->conversion->addLogMessage(sprintf(
            'Processing (run #%d) ended',
            $event->conversion->getProcessingCount(),
        ));
    }
}
