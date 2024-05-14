<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Tests\TrackingInformation;

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
    public function it_creates_from_cookie(): void
    {
        $request = new Request(
            cookies: [
                'ssga_tinfo' => 'eyJnY2xpZCI6ImdjbGlkIiwiZ2JyYWlkIjoiZ2JyYWlkIiwid2JyYWlkIjoid2JyYWlkIn0=',
            ],
        );
        $trackingInformation = TrackingInformation::fromCookie($request, 'ssga_tinfo');

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
        $trackingInformation = TrackingInformation::fromQuery($request);

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
