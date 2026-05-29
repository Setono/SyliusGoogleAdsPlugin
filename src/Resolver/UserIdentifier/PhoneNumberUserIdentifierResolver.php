<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberException;
use Brick\PhoneNumber\PhoneNumberFormat;
use Google\Ads\GoogleAds\V24\Common\UserIdentifier;
use Setono\SyliusGoogleAdsPlugin\Resolver\CustomerCountryResolverInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @experimental
 * Resolves phone number user identifiers for Google Ads Customer Match.
 *
 * Uses the brick/phonenumber library to properly parse and format phone numbers to E.164 format
 * before hashing with SHA-256 as required by Google Ads.
 *
 * Automatically detects the customer's region from their most recent order's billing address
 * to properly parse phone numbers without country codes.
 */
final class PhoneNumberUserIdentifierResolver extends AbstractUserIdentifierResolver
{
    public function __construct(
        private readonly CustomerCountryResolverInterface $customerCountryResolver,
    ) {
    }

    public function getUserIdentifiers(CustomerInterface $customer): array
    {
        $phoneNumberString = $customer->getPhoneNumber();

        if (null === $phoneNumberString) {
            return [];
        }

        $region = $this->customerCountryResolver->getCountryCode($customer);
        $e164PhoneNumber = $this->formatToE164($phoneNumberString, $region);

        if (null === $e164PhoneNumber) {
            return [];
        }

        return [new UserIdentifier(['hashed_phone_number' => self::normalizeAndHash($e164PhoneNumber, false)])];
    }

    private function formatToE164(string $phoneNumber, ?string $region): ?string
    {
        try {
            // First, try to parse as international number (with country code)
            $parsedNumber = PhoneNumber::parse($phoneNumber);

            if ($parsedNumber->isValidNumber()) {
                return $parsedNumber->format(PhoneNumberFormat::E164);
            }
        } catch (PhoneNumberException) {
            // Try with the detected region from billing address
            if (null !== $region) {
                try {
                    $parsedNumber = PhoneNumber::parse($phoneNumber, $region);

                    if ($parsedNumber->isValidNumber()) {
                        return $parsedNumber->format(PhoneNumberFormat::E164);
                    }
                } catch (PhoneNumberException) {
                    // Region parsing failed
                }
            }
        }

        return null;
    }
}
