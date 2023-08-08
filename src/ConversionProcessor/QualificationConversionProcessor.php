<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Webmozart\Assert\Assert;

/**
 * This qualification processor more or less mimics the default behavior of the Google Ads javascript tracking snippet
 * which is injected on the 'success' page which usually means the order is completed and paid, but hasn't been shipped
 */
final class QualificationConversionProcessor extends AbstractConversionProcessor
{
    private int $initialNextProcessingDelay = 300;

    public function isEligible(ConversionInterface $conversion): bool
    {
        return $this->workflow->can($conversion, ConversionWorkflow::TRANSITION_QUALIFY) &&
            $this->workflow->can($conversion, ConversionWorkflow::TRANSITION_DISQUALIFY);
    }

    public function process(ConversionInterface $conversion): void
    {
        Assert::true($this->isEligible($conversion));

        /** @var OrderInterface $order */
        $order = $conversion->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        if ($order->getPaymentState() === OrderPaymentStates::STATE_CANCELLED || $order->getState() === OrderInterface::STATE_CANCELLED) {
            $conversion->addLogMessage('The conversion was disqualified because either the payment state or order state was "cancelled"');

            $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_DISQUALIFY);

            return;
        }

        if (!in_array($order->getPaymentState(), [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_AUTHORIZED], true)) {
            $nextProcessingAt = $this->calculateNextProcessingAt($conversion);
            $conversion->addLogMessage(sprintf(
                'The conversion does not qualify for further processing yet because the order is not paid. The next time this conversion will be processed it set to %s',
                $nextProcessingAt->format('Y-m-d H:i'),
            ));
            $conversion->setNextProcessingAt($nextProcessingAt);

            return;
        }

        $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_QUALIFY);
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
