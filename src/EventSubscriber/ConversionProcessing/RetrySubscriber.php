<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber\ConversionProcessing;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Transition;
use Webmozart\Assert\Assert;

final class RetrySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            sprintf('workflow.%s.completed', ConversionWorkflow::NAME) => 'retry',
        ];
    }

    public function retry(CompletedEvent $event): void
    {
        $transition = $event->getTransition();
        if (!$transition instanceof Transition) {
            return;
        }

        if ($transition->getName() !== ConversionWorkflow::TRANSITION_RETRY) {
            return;
        }

        /** @var ConversionInterface|object $conversion */
        $conversion = $event->getSubject();
        Assert::isInstanceOf($conversion, ConversionInterface::class);

        $conversion->setNextProcessingAt(new \DateTimeImmutable());
    }
}
