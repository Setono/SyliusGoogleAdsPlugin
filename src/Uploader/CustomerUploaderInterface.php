<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Uploader;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;

/**
 * @experimental
 */
interface CustomerUploaderInterface
{
    /**
     * Will upload all the customers associated with the customer list to Google
     */
    public function upload(CustomerListInterface $customerList): void;
}
