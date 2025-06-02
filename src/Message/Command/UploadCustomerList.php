<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Message\Command;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;

/**
 * @experimental
 * todo should be called when a customer list is created/updated
 */
final class UploadCustomerList implements CommandInterface
{
    public readonly int $customerList;

    public function __construct(int|CustomerListInterface $customerList)
    {
        if ($customerList instanceof CustomerListInterface) {
            $customerList = (int) $customerList->getId();
        }

        $this->customerList = $customerList;
    }
}
