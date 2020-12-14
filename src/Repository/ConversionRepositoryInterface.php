<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ConversionRepositoryInterface extends RepositoryInterface
{
    /**
     * If the $since argument is given, this method returns conversion since that date, however, if it isn't given
     * it returns conversion since three days ago. This is because this is what Google recommends
     */
    public function findByChannelQueryBuilder(ChannelInterface $channel, \DateTimeInterface $since = null): QueryBuilder;
}
