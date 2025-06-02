<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Processor\CustomerList;

use Setono\SyliusGoogleAdsPlugin\Message\Command\ProcessCustomerList;
use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Setono\SyliusGoogleAdsPlugin\Provisioner\CustomerListProvisionerInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\CustomerListRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\Uploader\CustomerListUploaderInterface;
use Setono\SyliusGoogleAdsPlugin\Uploader\CustomerUploaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @experimental
 */
final class CustomerListProcessor implements CustomerListProcessorInterface
{
    public function __construct(
        private readonly CustomerListProvisionerInterface $customerListProvisioner,
        private readonly CustomerListRepositoryInterface $customerListRepository,
        private readonly MessageBusInterface $commandBus,
        private readonly CustomerListUploaderInterface $customerListUploader,
        private readonly CustomerUploaderInterface $customerUploader,
    ) {
    }

    public function process(CustomerListInterface $customerList = null): void
    {
        null === $customerList ? $this->processAll() : $this->processOne($customerList);
    }

    private function processAll(): void
    {
        $this->customerListProvisioner->provision();

        foreach ($this->customerListRepository->findEnabled() as $l) {
            $this->commandBus->dispatch(new ProcessCustomerList($l));
        }
    }

    private function processOne(CustomerListInterface $customerList): void
    {
        if (!$customerList->isEnabled()) {
            return;
        }

        $this->customerListUploader->upload($customerList);
        $this->customerUploader->upload($customerList);
    }
}
