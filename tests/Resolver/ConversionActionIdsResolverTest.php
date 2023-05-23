<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGoogleAdsPlugin\Resolver;

use PHPUnit\Framework\TestCase;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactory;
use Setono\SyliusGoogleAdsPlugin\Resolver\ConversionActionId;
use Setono\SyliusGoogleAdsPlugin\Resolver\ConversionActionIdsResolver;
use Tests\Setono\SyliusGoogleAdsPlugin\LiveTestTrait;

/**
 * @covers \Setono\SyliusGoogleAdsPlugin\Resolver\ConversionActionIdsResolver
 */
final class ConversionActionIdsResolverTest extends TestCase
{
    use LiveTestTrait;

    /**
     * @test
     */
    public function it_resolves(): void
    {
        if (!self::isLive()) {
            $this->markTestSkipped('This is a live test and skipped because we are not running live tests');
        }

        $resolver = new ConversionActionIdsResolver(new GoogleAdsClientFactory());
        $conversionActionIds = $resolver->getConversionActionIdsFromConnectionMapping(self::createConnectionMapping());

        foreach ($conversionActionIds as $conversionActionId) {
            self::assertInstanceOf(ConversionActionId::class, $conversionActionId);
        }
    }
}
