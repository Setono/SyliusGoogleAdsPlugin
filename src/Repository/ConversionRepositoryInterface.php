<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<ConversionInterface>
 */
interface ConversionRepositoryInterface extends RepositoryInterface
{
    /**
     * This will set the process identifier on $max number of _ready_ conversions
     */
    public function updateReadyWithProcessIdentifier(string $processIdentifier, int $max = 1000): void;

    /**
     * @return array<array-key, ConversionInterface>
     */
    public function findReadyByProcessIdentifier(string $processIdentifier): array;

    /**
     * Returns conversions that should be checked, i.e. pending
     *
     * @param int $maxChecks the maximum number of times a conversion should be checked
     *
     * @return array<array-key, ConversionInterface>
     */
    public function findPending(int $maxChecks): array;
}
