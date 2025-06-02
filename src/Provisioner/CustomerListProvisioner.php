<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Provisioner;

use Setono\SyliusGoogleAdsPlugin\Factory\CustomerListFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\CustomerListRepositoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @experimental
 */
final class CustomerListProvisioner implements CustomerListProvisionerInterface
{
    public function __construct(
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly CustomerListRepositoryInterface $customerListRepository,
        private readonly CustomerListFactoryInterface $customerListFactory,
    ) {
    }

    public function provision(): void
    {
        /** @var ChannelInterface $channel */
        foreach ($this->channelRepository->findBy(['enabled' => true]) as $channel) {
            if ($this->customerListRepository->hasAtLeastOneForChannel($channel)) {
                continue;
            }

            $customerList = $this->customerListFactory->createNew();
            $customerList->setName(sprintf('Customers on %s', (string) $channel->getName()));
            $customerList->setChannel($channel);

            $this->customerListRepository->add($customerList);
        }
    }
}
