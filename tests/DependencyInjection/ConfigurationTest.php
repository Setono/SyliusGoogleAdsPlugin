<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Setono\SyliusGoogleAdsPlugin\DependencyInjection\Configuration;

/**
 * See examples of tests and configuration options here: https://github.com/SymfonyTest/SymfonyConfigTest
 */
final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    /**
     * @test
     */
    public function it_is_invalid_if_the_storage_configuration_is_invalid(): void
    {
        $this->assertConfigurationIsInvalid([
            [
                'storage' => 'invalid',
            ],
        ]);
    }
}
