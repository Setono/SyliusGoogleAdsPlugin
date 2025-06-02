<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Message\Handler;

use Setono\SyliusGoogleAdsPlugin\Message\Command\UploadCustomerList;
use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\CustomerListRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\Uploader\CustomerListUploaderInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

/**
 * @experimental
 */
final class UploadCustomerListHandler
{
    public function __construct(
        private readonly CustomerListRepositoryInterface $customerListRepository,
        private readonly CustomerListUploaderInterface $customerListUploader,
    ) {
    }

    public function __invoke(UploadCustomerList $message): void
    {
        $customerList = $this->customerListRepository->find($message->customerList);
        if (!$customerList instanceof CustomerListInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf('A customer list with id %d does not exist', $message->customerList));
        }

        $this->customerListUploader->upload($customerList);
    }
}
