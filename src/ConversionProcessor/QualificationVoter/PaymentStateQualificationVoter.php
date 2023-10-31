<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor\QualificationVoter;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Webmozart\Assert\Assert;

final class PaymentStateQualificationVoter implements QualificationVoterInterface
{
    public function vote(ConversionInterface $conversion): Vote
    {
        /** @var OrderInterface $order */
        $order = $conversion->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        if ($order->getPaymentState() === OrderPaymentStates::STATE_CANCELLED) {
            return Vote::disqualify('The conversion was disqualified because the payment was cancelled');
        }

        if (in_array($order->getPaymentState(), [OrderPaymentStates::STATE_PAID, OrderPaymentStates::STATE_AUTHORIZED], true)) {
            return Vote::qualify(sprintf('The conversion was qualified because the payment was %s', (string) $order->getPaymentState()));
        }

        return Vote::abstain('The conversion does not qualify for further processing yet because the order is not paid');
    }
}
