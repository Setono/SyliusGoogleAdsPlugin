<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier;

use Google\Ads\GoogleAds\V20\Common\UserIdentifier;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @experimental
 */
final class EmailUserIdentifierResolver extends AbstractUserIdentifierResolver
{
    public function getUserIdentifiers(CustomerInterface $customer): array
    {
        $email = $customer->getEmailCanonical();

        return null === $email ? [] : [new UserIdentifier(['hashed_email' => self::normalizeAndHash($email, true)])];
    }
}
