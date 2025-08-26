<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @experimental
 */
final class CustomerCountryResolver implements CustomerCountryResolverInterface
{
    use ORMTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        /** @var class-string<OrderInterface> $orderClass */
        private readonly string $orderClass,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function getCountryCode(CustomerInterface $customer): ?string
    {
        $qb = $this->getManager($this->orderClass)->createQueryBuilder();
        $qb->select('ba.countryCode')
            ->from($this->orderClass, 'o')
            ->innerJoin('o.billingAddress', 'ba')
            ->where('o.customer = :customer')
            ->andWhere('o.checkoutCompletedAt IS NOT NULL')
            ->andWhere('ba.countryCode IS NOT NULL')
            ->orderBy('o.checkoutCompletedAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('customer', $customer);

        try {
            /** @var string $result */
            $result = $qb->getQuery()->getSingleScalarResult();

            return $result;
        } catch (NoResultException) {
            return null;
        }
    }
}
