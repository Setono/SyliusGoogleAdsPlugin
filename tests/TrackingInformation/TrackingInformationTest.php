<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGoogleAdsPlugin\TrackingInformation;

use PHPUnit\Framework\TestCase;
use Setono\SyliusGoogleAdsPlugin\Model\Conversion;
use Setono\SyliusGoogleAdsPlugin\TrackingInformation\TrackingInformation;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Setono\SyliusGoogleAdsPlugin\TrackingInformation\TrackingInformation
 */
final class TrackingInformationTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created(): void
    {
        $trackingInformation = new TrackingInformation('gclid', 'gbraid', 'wbraid');

        self::assertSame('gclid', $trackingInformation->gclid);
        self::assertSame('gbraid', $trackingInformation->gbraid);
        self::assertSame('wbraid', $trackingInformation->wbraid);
    }

    /**
     * @test
     */
    public function it_json_encodes(): void
    {
        $trackingInformation = new TrackingInformation('gclid', 'gbraid', 'wbraid');

        self::assertSame(
            '{"gclid":"gclid","gbraid":"gbraid","wbraid":"wbraid"}',
            json_encode($trackingInformation, \JSON_THROW_ON_ERROR),
        );
    }

    /**
     * @test
     */
    public function it_creates_from_json(): void
    {
        $trackingInformation = TrackingInformation::fromJson('{"gclid":"gclid","gbraid":"gbraid","wbraid":"wbraid"}');

        self::assertSame('gclid', $trackingInformation->gclid);
        self::assertSame('gbraid', $trackingInformation->gbraid);
        self::assertSame('wbraid', $trackingInformation->wbraid);
    }

    /**
     * @test
     */
    public function it_creates_from_request(): void
    {
        $request = new Request([
            'gclid' => 'gclid',
            'gbraid' => 'gbraid',
            'wbraid' => 'wbraid',
        ]);
        $trackingInformation = TrackingInformation::fromRequest($request);

        self::assertSame('gclid', $trackingInformation->gclid);
        self::assertSame('gbraid', $trackingInformation->gbraid);
        self::assertSame('wbraid', $trackingInformation->wbraid);
    }

    /**
     * @test
     */
    public function it_assigns_information_to_conversion(): void
    {
        $conversion = new Conversion();
        $trackingInformation = new TrackingInformation('gclid', 'gbraid', 'wbraid');
        $trackingInformation->assignToConversion($conversion);

        self::assertSame('gclid', $conversion->getGclid());
        self::assertSame('gbraid', $conversion->getGbraid());
        self::assertSame('wbraid', $conversion->getWbraid());
    }
}
