<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor\QualificationVoter;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class OrderStateQualificationVoter implements QualificationVoterInterface
{
    public function vote(ConversionInterface $conversion): Vote
    {
        /** @var OrderInterface $order */
        $order = $conversion->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        if ($order->getState() === OrderInterface::STATE_CANCELLED) {
            return Vote::disqualify('The conversion was disqualified because the order was cancelled');
        }

        return Vote::qualify();
    }
}
