<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\TrackingInformation;

use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class TrackingInformation implements \JsonSerializable
{
    public function __construct(public readonly ?string $gclid, public readonly ?string $gbraid, public readonly ?string $wbraid)
    {
        if (null === $gclid && null === $gbraid && null === $wbraid) {
            throw new \InvalidArgumentException('At least one of the tracking parameters must be set');
        }
    }

    /**
     * @throws \InvalidArgumentException if none of the tracking parameters are present in the request
     */
    public static function fromRequest(Request $request): self
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

        return new self($data['gclid'] ?? null, $data['gbraid'] ?? null, $data['wbraid'] ?? null);
    }

    /**
     * @throws \JsonException if the json is invalid
     * @throws \InvalidArgumentException if the decoded data is invalid
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);

        Assert::isArray($data);
        Assert::allString($data);

        return new self($data['gclid'] ?? null, $data['gbraid'] ?? null, $data['wbraid'] ?? null);
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
