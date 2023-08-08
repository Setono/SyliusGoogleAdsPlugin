<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<ConversionInterface>
 */
interface ConversionRepositoryInterface extends RepositoryInterface
{
    public function createPreQualifiedConversionQueryBuilder(string $alias = 'o'): QueryBuilder;

    /**
     * Use this method to prune the conversions table
     *
     * @return int the number of conversions pruned/removed
     */
    public function prune(): int;
}
