<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<ConversionInterface>
 */
interface ConversionRepositoryInterface extends RepositoryInterface
{
    public function createReadyByChannelQueryBuilder(ChannelInterface $channel): QueryBuilder;

    /**
     * The default for $since is 3 days as the method above
     *
     * @return array<array-key, ConversionInterface>
     */
    public function findPending(\DateTimeInterface $since = null): array;
}
