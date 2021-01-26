<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Safe\DateTimeImmutable;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

class ConversionRepository extends EntityRepository implements ConversionRepositoryInterface
{
    public function findReadyByChannelQueryBuilder(ChannelInterface $channel, \DateTimeInterface $since = null): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state = :state')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.createdAt >= :since')
            ->setParameter('state', ConversionInterface::STATE_READY)
            ->setParameter('channel', $channel)
            ->setParameter('since', $since ?? new DateTimeImmutable('-3 days'))
        ;
    }
}
