<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\DataProvider;

use Doctrine\Persistence\ManagerRegistry;
use DoctrineBatchUtils\BatchProcessing\SelectBatchIteratorAggregate;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final class CustomerDataProvider implements CustomerDataProviderInterface
{
    use ORMTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        /** @var class-string<CustomerInterface> $customerClass */
        private readonly string $customerClass,
        /** @var class-string<OrderInterface> $orderClass */
        private readonly string $orderClass,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function getCustomers(CustomerListInterface $customerList): iterable
    {
        $channel = $customerList->getChannel();
        Assert::notNull($channel, 'The customer list must have a channel');

        // Build the EXISTS subquery
        $subQb = $this->getManager($this->orderClass)
            ->createQueryBuilder()
            ->select('1')
            ->from($this->orderClass, 'o')
            ->where('o.customer = c')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.checkoutCompletedAt IS NOT NULL')
            ->andWhere('o.checkoutCompletedAt >= :checkoutThreshold')
        ;

        $expr = $this->getManager($this->customerClass)->getExpressionBuilder();

        // Build the main query
        $qb = $this->getManager($this->customerClass)
            ->createQueryBuilder()
            ->select('c')
            ->from($this->customerClass, 'c')
            ->where($expr->exists($subQb->getDQL()))
            ->setParameter('channel', $channel)
            ->setParameter('checkoutThreshold', new \DateTimeImmutable(sprintf('-%d days', (int) $customerList->getMembershipLifespan())))
        ;

        /** @var SelectBatchIteratorAggregate<array-key, CustomerInterface> $batch */
        $batch = SelectBatchIteratorAggregate::fromQuery($qb->getQuery(), 500);

        yield from $batch;
    }
}
