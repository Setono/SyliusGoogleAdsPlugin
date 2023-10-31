<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor\QualificationVoter;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;

/**
 * @extends CompositeService<QualificationVoterInterface>
 *
 * NOTICE the following rules apply to the voting process:
 *
 * 1. Only one voter needs to vote 'disqualify' for the voting to be disqualified
 * 2. At least one voter needs to vote 'qualify' for the voting to be qualified
 * 3. If none of the above two conditions are met, the voting will be 'abstain'
 */
final class CompositeQualificationVoter extends CompositeService implements QualificationVoterInterface
{
    public function vote(ConversionInterface $conversion): Vote
    {
        $qualify = false;
        $reasons = [];

        foreach ($this->services as $service) {
            $vote = $service->vote($conversion);
            if ($vote->hasReasons()) {
                $reasons[] = $vote->reasons;
            }

            if ($vote->isDisqualify()) {
                return $vote;
            }

            if ($vote->isQualify()) {
                $qualify = true;
            }
        }

        $reasons = array_merge(...$reasons);

        return $qualify ? Vote::qualify($reasons) : Vote::abstain($reasons);
    }
}
