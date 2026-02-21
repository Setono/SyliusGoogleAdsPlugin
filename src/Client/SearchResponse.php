<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client;

use Google\Ads\GoogleAds\Lib\V20\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\V20\Services\GoogleAdsRow;
use Google\ApiCore\ServerStream;
use Webmozart\Assert\Assert;

/**
 * @experimental
 *
 * @implements \IteratorAggregate<array-key, GoogleAdsRow>
 */
final class SearchResponse implements \IteratorAggregate
{
    private function __construct(private readonly GoogleAdsServerStreamDecorator $serverStream)
    {
    }

    /**
     * @throws \InvalidArgumentException if $serverStream is not an instance of Google\Ads\GoogleAds\Lib\V20\GoogleAdsServerStreamDecorator
     */
    public static function fromServerStream(ServerStream $serverStream): self
    {
        Assert::isInstanceOf($serverStream, GoogleAdsServerStreamDecorator::class);

        return new self($serverStream);
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return \Generator<array-key, GoogleAdsRow>
     */
    public function getIterator(): \Generator
    {
        /** @phpstan-ignore generator.keyType, generator.valueType */
        yield from $this->serverStream->iterateAllElements();
    }
}
