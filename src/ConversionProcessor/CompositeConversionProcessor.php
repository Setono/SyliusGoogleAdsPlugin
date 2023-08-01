<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\CompositeCompilerPass\CompositeService;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Event\ConversionProcessingEndedEvent;
use Setono\SyliusGoogleAdsPlugin\Event\ConversionProcessingStartedEvent;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @extends CompositeService<ConversionProcessorInterface>
 */
final class CompositeConversionProcessor extends CompositeService implements ConversionProcessorInterface
{
    use ORMManagerTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly WorkflowInterface $workflow,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function process(ConversionInterface $conversion): void
    {
        $processingStartedEvent = new ConversionProcessingStartedEvent($conversion);
        $this->eventDispatcher->dispatch($processingStartedEvent);

        if ($processingStartedEvent->stopProcessing && null !== $processingStartedEvent->stopProcessingReason) {
            $conversion->addLogMessage($processingStartedEvent->stopProcessingReason);
        }

        $manager = $this->getManager($conversion);

        try {
            $manager->flush();
        } catch (OptimisticLockException) {
            // if an OptimisticLockException is thrown we know that another process
            // is processing this conversion, so we will just return and do nothing
            return;
        }

        if (!$processingStartedEvent->stopProcessing) {
            foreach ($this->services as $service) {
                if (!$service->isEligible($conversion)) {
                    continue;
                }

                try {
                    $service->process($conversion);
                } catch (\Throwable $e) {
                    $conversion->addLogMessage($e->getMessage());
                    $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_FAIL);

                    break;
                }
            }

            if ($this->workflow->can($conversion, ConversionWorkflow::TRANSITION_DELIVER)) {
                $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_DELIVER);
            }
        }

        $this->eventDispatcher->dispatch(new ConversionProcessingEndedEvent($conversion));

        $manager->flush();
    }

    public function isEligible(ConversionInterface $conversion): bool
    {
        foreach ($this->services as $service) {
            if ($service->isEligible($conversion)) {
                return true;
            }
        }

        return false;
    }
}
