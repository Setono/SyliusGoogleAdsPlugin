<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber\ConversionProcessing;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Transition;
use Webmozart\Assert\Assert;

/**
 * When the state for the conversion goes to certain states (i.e. disqualified, failed, or delivered)
 * we will set the 'next processing at' timestamp to null to avoid the conversion to be eligible for processing
 */
final class NullifyNextProcessingSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            sprintf('workflow.%s.completed', ConversionWorkflow::NAME) => 'update',
        ];
    }

    public function update(CompletedEvent $event): void
    {
        $transition = $event->getTransition();
        if (!$transition instanceof Transition) {
            return;
        }

        $transition = $transition->getName();

        if (!in_array($transition, [ConversionWorkflow::TRANSITION_DISQUALIFY, ConversionWorkflow::TRANSITION_FAIL, ConversionWorkflow::TRANSITION_DELIVER], true)) {
            return;
        }

        /** @var ConversionInterface|object $conversion */
        $conversion = $event->getSubject();
        Assert::isInstanceOf($conversion, ConversionInterface::class);

        $conversion->setNextProcessingAt(null);
    }
}
