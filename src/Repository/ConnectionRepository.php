<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ConnectionRepository extends EntityRepository implements ConnectionRepositoryInterface
{
    public function hasAny(): bool
    {
        return (int) $this->createQueryBuilder('o')
                ->select('COUNT(o)')
                ->getQuery()
                ->getSingleScalarResult() > 0
        ;
    }
}
