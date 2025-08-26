<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Processor\CustomerList;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;

/**
 * @experimental
 */
interface CustomerListProcessorInterface
{
    /**
     * @param CustomerListInterface|null $customerList if null, all customer lists will be processed
     */
    public function process(CustomerListInterface $customerList = null): void;
}
