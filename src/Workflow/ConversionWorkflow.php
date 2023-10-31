<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Workflow;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ConversionWorkflow
{
    public const NAME = 'setono_sylius_google_ads__conversion';

    public const TRANSITION_DISQUALIFY = 'disqualify';

    public const TRANSITION_QUALIFY = 'qualify';

    public const TRANSITION_PROCESS = 'process';

    public const TRANSITION_UPLOAD_CONVERSION = 'upload_conversion';

    public const TRANSITION_DELIVER = 'deliver';

    public const TRANSITION_FAIL = 'fail';

    public const TRANSITION_RETRY = 'retry';

    private function __construct()
    {
    }

    /**
     * @return array<array-key, string>
     */
    public static function getStates(): array
    {
        return [
            ConversionInterface::STATE_CONVERSION_UPLOADED,
            ConversionInterface::STATE_DELIVERED,
            ConversionInterface::STATE_FAILED,
            ConversionInterface::STATE_DISQUALIFIED,
            ConversionInterface::STATE_PENDING,
            ConversionInterface::STATE_QUALIFIED,
        ];
    }

    public static function getConfig(): array
    {
        $transitions = [];
        foreach (self::getTransitions() as $transition) {
            $transitions[$transition->getName()] = [
                'from' => $transition->getFroms(),
                'to' => $transition->getTos(),
            ];
        }

        return [
            self::NAME => [
                'type' => 'state_machine',
                'marking_store' => [
                    'type' => 'method',
                    'property' => 'state',
                ],
                'supports' => ConversionInterface::class,
                'initial_marking' => ConversionInterface::STATE_PENDING,
                'places' => self::getStates(),
                'transitions' => $transitions,
            ],
        ];
    }

    public static function getWorkflow(EventDispatcherInterface $eventDispatcher): Workflow
    {
        $definitionBuilder = new DefinitionBuilder(self::getStates(), self::getTransitions());

        return new Workflow(
            $definitionBuilder->build(),
            new MethodMarkingStore(true, 'state'),
            $eventDispatcher,
            self::NAME,
        );
    }

    /**
     * @return array<array-key, Transition>
     */
    public static function getTransitions(): array
    {
        return [
            new Transition(
                self::TRANSITION_DISQUALIFY,
                ConversionInterface::STATE_PENDING,
                ConversionInterface::STATE_DISQUALIFIED,
            ),
            new Transition(
                self::TRANSITION_QUALIFY,
                ConversionInterface::STATE_PENDING,
                ConversionInterface::STATE_QUALIFIED,
            ),
            new Transition(
                self::TRANSITION_UPLOAD_CONVERSION,
                ConversionInterface::STATE_QUALIFIED,
                ConversionInterface::STATE_CONVERSION_UPLOADED,
            ),
            new Transition(
                self::TRANSITION_DELIVER,
                ConversionInterface::STATE_CONVERSION_UPLOADED,
                ConversionInterface::STATE_DELIVERED,
            ),
            new Transition(
                self::TRANSITION_FAIL,
                [ConversionInterface::STATE_PENDING, ConversionInterface::STATE_QUALIFIED, ConversionInterface::STATE_CONVERSION_UPLOADED],
                ConversionInterface::STATE_FAILED,
            ),
            new Transition(
                self::TRANSITION_RETRY,
                ConversionInterface::STATE_FAILED,
                ConversionInterface::STATE_PENDING,
            ),
        ];
    }
}
