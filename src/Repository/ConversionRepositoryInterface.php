<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ConversionRepositoryInterface extends RepositoryInterface
{
    public function findByChannelQueryBuilder(ChannelInterface $channel): QueryBuilder;
}
