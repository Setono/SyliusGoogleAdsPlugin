<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\DataProvider;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @experimental
 */
interface CustomerDataProviderInterface
{
    /**
     * Returns an iterable of _real_ customers (i.e., people who completed a purchase) eligible for the given customer list
     *
     * @return iterable<CustomerInterface>
     */
    public function getCustomers(CustomerListInterface $customerList): iterable;
}
