<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier;

/**
 * @experimental
 */
abstract class AbstractUserIdentifierResolver implements UserIdentifierResolverInterface
{
    /**
     * @param string $value the value to normalize and hash
     * @param bool $trimIntermediateSpaces if true, removes leading, trailing, and intermediate spaces from the string before hashing. If false, only removes leading and trailing spaces from the string before hashing.
     *
     * @return string the normalized and hashed value
     */
    protected static function normalizeAndHash(string $value, bool $trimIntermediateSpaces): string
    {
        // Normalizes by first converting all characters to lowercase, then trimming spaces.
        $normalized = strtolower($value);
        if ($trimIntermediateSpaces === true) {
            // Removes leading, trailing, and intermediate spaces.
            $normalized = str_replace(' ', '', $normalized);
        } else {
            // Removes only leading and trailing spaces.
            $normalized = trim($normalized);
        }

        return hash('sha256', $normalized);
    }
}
