<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Doctrine\ORM;

use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

class ConversionRepository extends EntityRepository implements ConversionRepositoryInterface
{
    public function findEnabledByChannelAndCategory(ChannelInterface $channel, string $category): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->andWhere('o.category = :category')
            ->setParameter('channel', $channel)
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult()
        ;
    }
}
