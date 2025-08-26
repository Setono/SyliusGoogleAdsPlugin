<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @experimental
 */
interface CustomerCountryResolverInterface
{
    /**
     * Returns the country code from the customer's most recent completed order's billing address
     */
    public function getCountryCode(CustomerInterface $customer): ?string;
}
