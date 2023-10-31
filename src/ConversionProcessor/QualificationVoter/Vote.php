<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor\QualificationVoter;

final class Vote
{
    final public const QUALIFY = 'qualify';

    final public const ABSTAIN = 'abstain';

    final public const DISQUALIFY = 'disqualify';

    /** @var list<string> */
    public array $reasons;

    /**
     * @param list<string>|string $reason
     */
    public function __construct(
        public readonly string $value,
        array|string $reason = [],
    ) {
        $this->reasons = (array) $reason;
    }

    /**
     * @param list<string>|string $reason
     */
    public static function qualify(array|string $reason = []): self
    {
        return new self(self::QUALIFY, $reason);
    }

    /**
     * @param list<string>|string $reason
     */
    public static function abstain(array|string $reason = []): self
    {
        return new self(self::ABSTAIN, $reason);
    }

    /**
     * @param list<string>|string $reason
     */
    public static function disqualify(array|string $reason = []): self
    {
        return new self(self::DISQUALIFY, $reason);
    }

    public function isQualify(): bool
    {
        return self::QUALIFY === $this->value;
    }

    public function isAbstain(): bool
    {
        return self::ABSTAIN === $this->value;
    }

    public function isDisqualify(): bool
    {
        return self::DISQUALIFY === $this->value;
    }

    /**
     * @psalm-assert-if-true non-empty-list $this->reasons
     */
    public function hasReasons(): bool
    {
        return [] !== $this->reasons;
    }
}
