<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

class ConversionRepository extends EntityRepository implements ConversionRepositoryInterface
{
    public function findByChannelQueryBuilder(ChannelInterface $channel): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.channel = :channel')
            ->setParameter('channel', $channel)
        ;
    }
}
