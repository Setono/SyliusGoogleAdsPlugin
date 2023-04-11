<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGoogleAdsPlugin\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Setono\SyliusGoogleAdsPlugin\DependencyInjection\Configuration;
use Setono\SyliusGoogleAdsPlugin\Doctrine\ORM\ConversionRepository;
use Setono\SyliusGoogleAdsPlugin\Form\Type\ConnectionType;
use Setono\SyliusGoogleAdsPlugin\Model\Connection;
use Setono\SyliusGoogleAdsPlugin\Model\Conversion;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Component\Resource\Factory\Factory;

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
    public function values_are_invalid_if_required_value_is_not_provided(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [], // no values at all
            ],
            [
                'resources' => [
                    'connection' => [
                        'classes' => [
                            'model' => Connection::class,
                            'controller' => ResourceController::class,
                            'factory' => Factory::class,
                            'form' => ConnectionType::class,
                        ],
                    ],
                    'conversion' => [
                        'classes' => [
                            'model' => Conversion::class,
                            'controller' => ResourceController::class,
                            'repository' => ConversionRepository::class,
                            'factory' => Factory::class,
                            'form' => DefaultResourceType::class,
                        ],
                    ],
                ],
            ],
        );
    }
}
