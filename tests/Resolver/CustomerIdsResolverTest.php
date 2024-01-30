<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Tests\Resolver;

use PHPUnit\Framework\TestCase;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactory;
use Setono\SyliusGoogleAdsPlugin\Resolver\CustomerId;
use Setono\SyliusGoogleAdsPlugin\Resolver\CustomerIdsResolver;
use Setono\SyliusGoogleAdsPlugin\Tests\LiveTestTrait;

/**
 * @covers \Setono\SyliusGoogleAdsPlugin\Resolver\CustomerIdsResolver
 */
final class CustomerIdsResolverTest extends TestCase
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

        $connection = self::createConnection();

        $resolver = new CustomerIdsResolver(new GoogleAdsClientFactory());
        $customerIds = $resolver->getCustomerIdsFromConnection($connection);

        foreach ($customerIds as $customerId) {
            self::assertInstanceOf(CustomerId::class, $customerId);
        }
    }
}
