<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ConversionRepositoryInterface extends RepositoryInterface
{
    /**
     * If the $since argument is given, this method returns conversion since that date, however, if it isn't given
     * it returns conversion since three days ago. This is because this is what Google recommends
     */
    public function findReadyByChannelQueryBuilder(ChannelInterface $channel, \DateTimeInterface $since = null): QueryBuilder;

    /**
     * The default for $since is 3 days as the method above
     *
     * @return ConversionInterface[]
     */
    public function findPending(\DateTimeInterface $since = null): array;
}
