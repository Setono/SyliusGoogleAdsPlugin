<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use DateTimeImmutable;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Webmozart\Assert\Assert;

class ConversionRepository extends EntityRepository implements ConversionRepositoryInterface
{
    public function updateReadyWithProcessIdentifier(string $processIdentifier, int $max = 1000): void
    {
        $this->createQueryBuilder('o')
            ->update()
            ->andWhere('o.state = :state')
            ->andWhere('o.processIdentifier IS NULL')
            ->set('o.processIdentifier', ':processIdentifier')
            ->setParameter('state', ConversionInterface::STATE_READY)
            ->setParameter('processIdentifier', $processIdentifier)
            ->getQuery()
            ->execute()
        ;
    }

    public function findReadyByProcessIdentifier(string $processIdentifier): array
    {
        $res = $this->createQueryBuilder('o')
            ->andWhere('o.state = :state')
            ->andWhere('o.processIdentifier = :processIdentifier')
            ->setParameter('state', ConversionInterface::STATE_READY)
            ->setParameter('processIdentifier', $processIdentifier)
            ->getQuery()
            ->getResult()
        ;

        Assert::allIsInstanceOf($res, ConversionInterface::class);

        return $res;
    }

    public function findPending(int $maxChecks): array
    {
        $qb = $this->createQueryBuilder('o');

        $res = $qb
            ->andWhere('o.state = :state')
            ->andWhere('o.checks < :maxChecks')
            ->andWhere('o.nextCheckAt <= :now')
            ->setParameter('state', ConversionInterface::STATE_PENDING)
            ->setParameter('maxChecks', $maxChecks)
            ->setParameter('now', new DateTimeImmutable())
            ->setMaxResults(1000) // to avoid memory issues
            ->getQuery()
            ->getResult()
        ;

        Assert::allIsInstanceOf($res, ConversionInterface::class);

        return $res;
    }
}
