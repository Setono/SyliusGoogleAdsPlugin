<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_THROW_ON_ERROR;
use Setono\SyliusGoogleAdsPlugin\Model\Consent;
use Webmozart\Assert\Assert;

final class ConsentType extends Type
{
    final public const NAME = 'google_ads_consent';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    /**
     * @param mixed $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Consent) {
            throw ConversionException::conversionFailedInvalidType($value, 'json', [Consent::class]);
        }

        try {
            return json_encode($value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
        } catch (\JsonException $e) {
            /** @psalm-suppress TooManyArguments */
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Consent
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, Consent::class, ['string']);
        }

        try {
            $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            Assert::isArray($data);

            /** @psalm-suppress MixedArgumentTypeCoercion */
            return Consent::fromArray($data);
        } catch (\JsonException|\InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @deprecated
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        /** @psalm-suppress DeprecatedMethod */
        return !$platform->hasNativeJsonType();
    }
}
