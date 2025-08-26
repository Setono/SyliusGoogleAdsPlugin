<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
class CustomerListRepository extends EntityRepository implements CustomerListRepositoryInterface
{
    public function findEnabled(): array
    {
        $objs = $this->findBy([
            'enabled' => true,
        ]);

        Assert::allIsInstanceOf($objs, CustomerListInterface::class);

        return $objs;
    }

    public function findPendingForUpload(): array
    {
        $objs = $this->createQueryBuilder('o')
            ->orWhere('o.uploadedAt is null')
            ->orWhere('o.uploadedAt < o.updatedAt')
            ->getQuery()
            ->getResult()
        ;

        Assert::isArray($objs);
        Assert::isList($objs);
        Assert::allIsInstanceOf($objs, CustomerListInterface::class);

        return $objs;
    }

    public function hasAtLeastOneForChannel(ChannelInterface $channel): bool
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->andWhere('o.channel = :channel')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getSingleScalarResult() > 0
        ;
    }
}
