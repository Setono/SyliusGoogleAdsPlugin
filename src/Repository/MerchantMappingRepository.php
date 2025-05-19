<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Repository;

use Setono\SyliusGoogleAdsPlugin\Model\MerchantMappingInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Webmozart\Assert\Assert;

class MerchantMappingRepository extends EntityRepository implements MerchantMappingRepositoryInterface
{
    public function findOneByChannel(ChannelInterface $channel): ?MerchantMappingInterface
    {
        $obj = $this->findOneBy(['channel' => $channel]);

        Assert::nullOrIsInstanceOf($obj, MerchantMappingInterface::class);

        return $obj;
    }
}
