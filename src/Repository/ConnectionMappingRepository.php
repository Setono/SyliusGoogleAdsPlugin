<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Webmozart\Assert\Assert;

class ConnectionMappingRepository extends EntityRepository implements ConnectionMappingRepositoryInterface
{
    public function findEnabledByChannel(ChannelInterface $channel): array
    {
        $res = $this->createQueryBuilder('o')
            ->select('o, c')
            ->join('o.connection', 'c')
            ->andWhere('o.channel = :channel')
            ->andWhere('c.enabled = true')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;

        Assert::allIsInstanceOf($res, ConnectionMappingInterface::class);

        return $res;
    }
}
