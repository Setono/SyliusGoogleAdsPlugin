<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Message\Handler;

use Setono\SyliusGoogleAdsPlugin\Message\Command\ProcessCustomerList;
use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Setono\SyliusGoogleAdsPlugin\Processor\CustomerList\CustomerListProcessorInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\CustomerListRepositoryInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

/**
 * @experimental
 */
final class ProcessCustomerListHandler
{
    public function __construct(
        private readonly CustomerListRepositoryInterface $customerListRepository,
        private readonly CustomerListProcessorInterface $customerListProcessor,
    ) {
    }

    public function __invoke(ProcessCustomerList $message): void
    {
        $customerList = $this->customerListRepository->find($message->customerList);
        if (!$customerList instanceof CustomerListInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf('A customer list with id %d does not exist', $message->customerList));
        }

        $this->customerListProcessor->process($customerList);
    }
}
