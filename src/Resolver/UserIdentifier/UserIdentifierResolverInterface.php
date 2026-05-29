<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier;

use Google\Ads\GoogleAds\V24\Common\UserIdentifier;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @experimental
 * todo is 'resolver' the right name for this?
 */
interface UserIdentifierResolverInterface
{
    /**
     * @return list<UserIdentifier>
     */
    public function getUserIdentifiers(CustomerInterface $customer): array;
}
