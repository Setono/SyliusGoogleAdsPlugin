<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Doctrine\Persistence\ManagerRegistry;
use Setono\CompositeCompilerPass\CompositeService;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
        private readonly ValidatorInterface $validator,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function process(ConversionInterface $conversion): void
    {
        $errors = $this->validator->validate($conversion, [], ['conversion_processing']);
        foreach ($this->services as $service) {
            try {
                $service->process($conversion);
            } catch (\Throwable $e) {
                $conversion->setError($e->getMessage());
                $conversion->setState(ConversionInterface::STATE_FAILED);

                break;
            }
        }

        if ($this->workflow->can($conversion, ConversionWorkflow::TRANSITION_DELIVER)) {
            $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_DELIVER);
        }

        $this->getManager($conversion)->flush();
    }
}
