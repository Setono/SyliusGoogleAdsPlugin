<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Doctrine\ORM;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionActionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionActionRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Webmozart\Assert\Assert;

class ConversionActionRepository extends EntityRepository implements ConversionActionRepositoryInterface
{
    public function findEnabledByChannelAndCategory(ChannelInterface $channel, string $category): array
    {
        $res = $this->createQueryBuilder('o')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->andWhere('o.category = :category')
            ->setParameter('channel', $channel)
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult()
        ;

        Assert::allIsInstanceOf($res, ConversionActionInterface::class);

        return $res;
    }

    public function findChannels(): array
    {
        $channels = [];

        /** @var ConversionActionInterface[] $conversionActions */
        $conversionActions = $this->createQueryBuilder('o')
            ->select('o,c')
            ->join('o.channels', 'c')
            ->andWhere('o.enabled = true')
            ->getQuery()
            ->getResult()
        ;

        foreach ($conversionActions as $conversionAction) {
            foreach ($conversionAction->getChannels() as $channel) {
                $channels[(string) $channel->getId()] = $channel;
            }
        }

        return $channels;
    }
}
