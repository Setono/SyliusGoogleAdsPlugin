<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor\QualificationVoter;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;

interface QualificationVoterInterface
{
    public function vote(ConversionInterface $conversion): Vote;
}
