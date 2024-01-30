<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Model;

/**
 * This class maps the consent fields from the Google Ads API.
 * True = Granted
 * False = Denied
 * Null = Not specified
 */
final class Consent implements \JsonSerializable
{
    public function __construct(
        private ?bool $adUserData = null,
        private ?bool $adPersonalization = null,
    ) {
    }

    /**
     * @param array{adUserData?: bool, adPersonalization?: bool} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['adUserData'] ?? null,
            $data['adPersonalization'] ?? null,
        );
    }

    public function getAdUserData(): ?bool
    {
        return $this->adUserData;
    }

    public function grantAdUserData(): void
    {
        $this->adUserData = true;
    }

    public function denyAdUserData(): void
    {
        $this->adUserData = false;
    }

    public function getAdPersonalization(): ?bool
    {
        return $this->adPersonalization;
    }

    public function grantAdPersonalization(): void
    {
        $this->adPersonalization = true;
    }

    public function denyAdPersonalization(): void
    {
        $this->adPersonalization = false;
    }

    public function jsonSerialize(): array
    {
        return [
            'adUserData' => $this->adUserData,
            'adPersonalization' => $this->adPersonalization,
        ];
    }
}
