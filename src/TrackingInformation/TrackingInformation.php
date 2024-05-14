<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\TrackingInformation;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class TrackingInformation implements \JsonSerializable
{
    public function __construct(
        public readonly ?string $gclid,
        public readonly ?string $gbraid,
        public readonly ?string $wbraid,
    ) {
        if (null === $gclid && null === $gbraid && null === $wbraid) {
            throw new \InvalidArgumentException('At least one of the tracking parameters must be set');
        }
    }

    /**
     * Will read the tracking information from the query parameters, if they are present
     *
     * @throws \InvalidArgumentException if none of the tracking parameters are present in the request
     */
    public static function fromQuery(Request $request): self
    {
        /** @var array<string, string> $data */
        $data = [];

        foreach (['gclid', 'gbraid', 'wbraid'] as $param) {
            $val = $request->query->get($param);
            if (!is_string($val) || '' === $val) {
                continue;
            }

            $data[$param] = $val;
        }

        return self::fromArray($data);
    }

    /**
     * @throws \InvalidArgumentException if the cookie does not exist or is corrupt
     * @throws \JsonException if the JSON data is corrupt
     */
    public static function fromCookie(Request $request, string $cookieName): self
    {
        $value = $request->cookies->get($cookieName);
        Assert::stringNotEmpty($value);

        $json = base64_decode($value, true);
        if (false === $json) {
            throw new \InvalidArgumentException(sprintf(
                'The tracking information cookie was present, but the data was corrupt. The encoded data was: "%s"',
                $value,
            ));
        }

        $data = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);

        Assert::isArray($data);

        return self::fromArray($data);
    }

    /**
     * @throws \InvalidArgumentException if the data is invalid
     */
    public static function fromArray(array $data): self
    {
        Assert::allString($data);

        return new self($data['gclid'] ?? null, $data['gbraid'] ?? null, $data['wbraid'] ?? null);
    }

    public function toCookie(string $cookieName, int|string|\DateTimeInterface $expires): Cookie
    {
        return Cookie::create(
            name: $cookieName,
            value: base64_encode(json_encode($this, \JSON_THROW_ON_ERROR)),
            expire: $expires,
            secure: false,
            httpOnly: false,
        );
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'gclid' => $this->gclid,
            'gbraid' => $this->gbraid,
            'wbraid' => $this->wbraid,
        ]);
    }

    public function assignToConversion(ConversionInterface $conversion): void
    {
        $conversion->setGclid($this->gclid);
        $conversion->setGbraid($this->gbraid);
        $conversion->setWbraid($this->wbraid);
    }
}
