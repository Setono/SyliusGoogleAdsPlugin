<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Provisioner;

/**
 * @experimental
 */
interface CustomerListProvisionerInterface
{
    /**
     * Sets up the customer list functionality. MUST be idempotent
     */
    public function provision(): void;
}
