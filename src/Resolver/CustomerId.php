<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

final class CustomerId
{
    public function __construct(public readonly string $label, public readonly int $managerId, public readonly int $customerId)
    {
    }
}
