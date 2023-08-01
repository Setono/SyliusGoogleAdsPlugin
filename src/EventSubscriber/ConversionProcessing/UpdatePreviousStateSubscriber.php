<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\EventSubscriber\ConversionProcessing;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\LeaveEvent;
use Symfony\Component\Workflow\Marking;
use Webmozart\Assert\Assert;

final class UpdatePreviousStateSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            sprintf('workflow.%s.leave', ConversionWorkflow::NAME) => 'update',
        ];
    }

    public function update(LeaveEvent $event): void
    {
        $marking = $event->getMarking();
        if (!$marking instanceof Marking) {
            return;
        }

        $places = $marking->getPlaces();
        Assert::isArray($places);

        $places = array_keys($places);
        Assert::count($places, 1); // a state machine can only be in one place at a time

        $previousState = $places[0];
        Assert::string($previousState);

        /** @var ConversionInterface|object $conversion */
        $conversion = $event->getSubject();
        Assert::isInstanceOf($conversion, ConversionInterface::class);

        $conversion->setPreviousState($previousState);
    }
}
