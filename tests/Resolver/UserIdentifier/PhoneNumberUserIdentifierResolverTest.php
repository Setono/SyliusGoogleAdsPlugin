<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Tests\Resolver\UserIdentifier;

use Google\Ads\GoogleAds\V20\Common\UserIdentifier;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusGoogleAdsPlugin\Resolver\CustomerCountryResolverInterface;
use Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier\PhoneNumberUserIdentifierResolver;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @covers \Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier\PhoneNumberUserIdentifierResolver
 */
final class PhoneNumberUserIdentifierResolverTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $customerCountryResolver;

    private PhoneNumberUserIdentifierResolver $resolver;

    protected function setUp(): void
    {
        $this->customerCountryResolver = $this->prophesize(CustomerCountryResolverInterface::class);
        $this->resolver = new PhoneNumberUserIdentifierResolver($this->customerCountryResolver->reveal());
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_customer_has_no_phone(): void
    {
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getPhoneNumber()->willReturn(null);

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_customer_has_empty_phone(): void
    {
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getPhoneNumber()->willReturn('');

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function it_handles_international_phone_number(): void
    {
        $phoneNumber = '+12025551234'; // Valid international format (Washington DC)
        $expectedHash = hash('sha256', $phoneNumber); // Should be unchanged

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getPhoneNumber()->willReturn($phoneNumber);

        // Country resolver will still be called but result should parse as international
        $this->customerCountryResolver->getCountryCode($customer->reveal())
            ->shouldBeCalled()
            ->willReturn('US');

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertCount(1, $result);
        self::assertInstanceOf(UserIdentifier::class, $result[0]);
        self::assertSame($expectedHash, $result[0]->getHashedPhoneNumber());
    }

    /**
     * @test
     */
    public function it_handles_national_phone_number_with_country_from_resolver(): void
    {
        $phoneNumber = '2025551234'; // Valid national format (Washington DC)
        $countryCode = 'US';
        $expectedE164 = '+12025551234';
        $expectedHash = hash('sha256', $expectedE164);

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getPhoneNumber()->willReturn($phoneNumber);

        $this->customerCountryResolver->getCountryCode($customer->reveal())
            ->shouldBeCalled()
            ->willReturn($countryCode);

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertCount(1, $result);
        self::assertInstanceOf(UserIdentifier::class, $result[0]);
        self::assertSame($expectedHash, $result[0]->getHashedPhoneNumber());
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_phone_cannot_be_parsed(): void
    {
        $phoneNumber = 'invalid phone';

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getPhoneNumber()->willReturn($phoneNumber);

        $this->customerCountryResolver->getCountryCode($customer->reveal())
            ->shouldBeCalled()
            ->willReturn('US');

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_no_country_code_available(): void
    {
        $phoneNumber = '555-123-4567'; // National format but no country code

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getPhoneNumber()->willReturn($phoneNumber);

        $this->customerCountryResolver->getCountryCode($customer->reveal())
            ->shouldBeCalled()
            ->willReturn(null);

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function it_handles_formatted_international_phone_number(): void
    {
        $phoneNumber = '+1 202 555 1234'; // Formatted international number
        $expectedE164 = '+12025551234'; // Should be normalized to this
        $expectedHash = hash('sha256', $expectedE164);

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getPhoneNumber()->willReturn($phoneNumber);

        $this->customerCountryResolver->getCountryCode($customer->reveal())
            ->shouldBeCalled()
            ->willReturn('US');

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertCount(1, $result);
        self::assertInstanceOf(UserIdentifier::class, $result[0]);
        self::assertSame($expectedHash, $result[0]->getHashedPhoneNumber());
    }

    /**
     * @test
     */
    public function it_handles_german_phone_number_with_country_detection(): void
    {
        $phoneNumber = '30 12345678'; // Berlin number in national format (without leading 0)
        $countryCode = 'DE';
        $expectedE164 = '+493012345678';
        $expectedHash = hash('sha256', $expectedE164);

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getPhoneNumber()->willReturn($phoneNumber);

        $this->customerCountryResolver->getCountryCode($customer->reveal())
            ->shouldBeCalled()
            ->willReturn($countryCode);

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertCount(1, $result);
        self::assertInstanceOf(UserIdentifier::class, $result[0]);
        self::assertSame($expectedHash, $result[0]->getHashedPhoneNumber());
    }

    /**
     * @test
     */
    public function it_handles_uk_phone_number_with_country_detection(): void
    {
        $phoneNumber = '020 7031 3000'; // London number in national format
        $countryCode = 'GB';
        $expectedE164 = '+442070313000';
        $expectedHash = hash('sha256', $expectedE164);

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getPhoneNumber()->willReturn($phoneNumber);

        $this->customerCountryResolver->getCountryCode($customer->reveal())
            ->shouldBeCalled()
            ->willReturn($countryCode);

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertCount(1, $result);
        self::assertInstanceOf(UserIdentifier::class, $result[0]);
        self::assertSame($expectedHash, $result[0]->getHashedPhoneNumber());
    }
}
