<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber\ConversionProcessing;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class UpdateStateUpdatedAtSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            sprintf('workflow.%s.completed', ConversionWorkflow::NAME) => 'update',
        ];
    }

    public function update(CompletedEvent $event): void
    {
        /** @var ConversionInterface|object $conversion */
        $conversion = $event->getSubject();
        Assert::isInstanceOf($conversion, ConversionInterface::class);

        $conversion->setStateUpdatedAt(new \DateTimeImmutable());
    }
}
