<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ConversionRepository extends EntityRepository implements ConversionRepositoryInterface
{
    public function createPreQualifiedConversionQueryBuilder(string $alias = 'o'): QueryBuilder
    {
        return $this->createQueryBuilder($alias)
            ->andWhere(sprintf('%s.processing = false', $alias))
            ->andWhere(sprintf('%s.nextProcessingAt is not null', $alias))
            ->andWhere(sprintf('%s.nextProcessingAt <= :now', $alias))
            ->setParameter('now', new \DateTimeImmutable())
        ;
    }

    public function prune(): int
    {
        $threshold = (new \DateTimeImmutable())->sub(new \DateInterval('P30D'));

        return (int) $this->createQueryBuilder('o')
            ->delete()
            ->andWhere('o.createdAt <= :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute()
        ;
    }
}
