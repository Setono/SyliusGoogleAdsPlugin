<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

final class CustomerId
{
    public function __construct(
        public readonly string $label,
        public readonly string $managerId,
        public readonly string $customerId,
    ) {
    }
}
