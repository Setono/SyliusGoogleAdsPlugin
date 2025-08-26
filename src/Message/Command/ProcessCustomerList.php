<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Message\Command;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;

/**
 * @experimental
 */
final class ProcessCustomerList implements CommandInterface
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
