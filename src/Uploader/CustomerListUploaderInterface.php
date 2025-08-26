<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Uploader;

use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;

/**
 * @experimental
 */
interface CustomerListUploaderInterface
{
    public function upload(CustomerListInterface $customerList): void;
}
