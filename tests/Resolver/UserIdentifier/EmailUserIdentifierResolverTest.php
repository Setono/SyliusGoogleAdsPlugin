<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Tests\Resolver\UserIdentifier;

use Google\Ads\GoogleAds\V19\Common\UserIdentifier;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier\EmailUserIdentifierResolver;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @covers \Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier\EmailUserIdentifierResolver
 */
final class EmailUserIdentifierResolverTest extends TestCase
{
    use ProphecyTrait;

    private EmailUserIdentifierResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new EmailUserIdentifierResolver();
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_customer_has_no_email(): void
    {
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmailCanonical()->willReturn(null);

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function it_returns_hashed_email_identifier(): void
    {
        $email = 'john.doe@example.com';
        $expectedHash = hash('sha256', 'john.doe@example.com');

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmailCanonical()->willReturn($email);

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertCount(1, $result);
        self::assertInstanceOf(UserIdentifier::class, $result[0]);
        self::assertSame($expectedHash, $result[0]->getHashedEmail());
    }

    /**
     * @test
     */
    public function it_properly_normalizes_and_hashes_email(): void
    {
        $email = ' John.Doe@Example.COM ';
        // Expected: lowercase, spaces removed, then SHA-256 hashed
        $expectedHash = hash('sha256', 'john.doe@example.com');

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmailCanonical()->willReturn($email);

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertCount(1, $result);
        self::assertSame($expectedHash, $result[0]->getHashedEmail());
    }

    /**
     * @test
     */
    public function it_handles_email_with_intermediate_spaces(): void
    {
        $email = 'john doe@example.com';
        // For emails, intermediate spaces should be removed
        $expectedHash = hash('sha256', 'johndoe@example.com');

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmailCanonical()->willReturn($email);

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertCount(1, $result);
        self::assertSame($expectedHash, $result[0]->getHashedEmail());
    }

    /**
     * @test
     */
    public function it_handles_empty_string_email(): void
    {
        // The current implementation creates a hash even for empty strings
        // This generates the SHA-256 hash of an empty string
        $expectedHash = hash('sha256', '');

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmailCanonical()->willReturn('');

        $result = $this->resolver->getUserIdentifiers($customer->reveal());

        self::assertCount(1, $result);
        self::assertSame($expectedHash, $result[0]->getHashedEmail());
    }
}
