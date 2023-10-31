<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Setono\SyliusGoogleAdsPlugin\ConversionProcessor\QualificationVoter\QualificationVoterInterface;
use Setono\SyliusGoogleAdsPlugin\ConversionProcessor\QualificationVoter\Vote;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;
use Webmozart\Assert\Assert;

final class QualificationConversionProcessor implements ConversionProcessorInterface
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly QualificationVoterInterface $qualificationVoter,
        private readonly int $initialNextProcessingDelay = 300,
    ) {
    }

    public function isEligible(ConversionInterface $conversion): bool
    {
        return $this->workflow->can($conversion, ConversionWorkflow::TRANSITION_QUALIFY) &&
            $this->workflow->can($conversion, ConversionWorkflow::TRANSITION_DISQUALIFY);
    }

    public function process(ConversionInterface $conversion): void
    {
        Assert::true($this->isEligible($conversion));

        $vote = $this->qualificationVoter->vote($conversion);
        $conversion->addLogMessage($vote->reasons);

        switch ($vote->value) {
            case Vote::QUALIFY:
                $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_QUALIFY);

                break;
            case Vote::DISQUALIFY:
                $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_DISQUALIFY);

                break;
            case Vote::ABSTAIN:
                $nextProcessingAt = $this->calculateNextProcessingAt($conversion);
                $conversion->addLogMessage(sprintf(
                    'The next time this conversion will be processed it set to %s',
                    $nextProcessingAt->format('Y-m-d H:i'),
                ));
                $conversion->setNextProcessingAt($nextProcessingAt);

                break;
        }
    }

    private function calculateNextProcessingAt(ConversionInterface $conversion): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())
            ->add(new \DateInterval(sprintf(
                'PT%dS',
                $this->initialNextProcessingDelay * 2 ** $conversion->getProcessingCount(),
            )));
    }
}
