<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier;

use Setono\CompositeCompilerPass\CompositeService;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @internal
 *
 * @extends CompositeService<UserIdentifierResolverInterface>
 */
final class CompositeUserIdentifierResolver extends CompositeService implements UserIdentifierResolverInterface
{
    public function getUserIdentifiers(CustomerInterface $customer): array
    {
        $identifiers = [];

        foreach ($this->services as $service) {
            $identifiers[] = $service->getUserIdentifiers($customer);
        }

        return array_merge(...$identifiers);
    }
}
