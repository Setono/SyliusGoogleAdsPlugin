<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGoogleAdsPlugin\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Setono\SyliusGoogleAdsPlugin\DependencyInjection\Configuration;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

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
                'driver' => SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
                'resources' => [
                    'conversion' => [
                        'classes' => [
                            'model' => 'Setono\SyliusGoogleAdsPlugin\Model\Conversion',
                            'controller' => 'Sylius\Bundle\ResourceBundle\Controller\ResourceController',
                            'repository' => 'Setono\SyliusGoogleAdsPlugin\Doctrine\ORM\ConversionRepository',
                            'factory' => 'Sylius\Component\Resource\Factory\Factory',
                            'form' => 'Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType',
                        ],
                    ],
                    'conversion_action' => [
                        'classes' => [
                            'model' => 'Setono\SyliusGoogleAdsPlugin\Model\ConversionAction',
                            'controller' => 'Sylius\Bundle\ResourceBundle\Controller\ResourceController',
                            'repository' => 'Setono\SyliusGoogleAdsPlugin\Doctrine\ORM\ConversionActionRepository',
                            'factory' => 'Sylius\Component\Resource\Factory\Factory',
                            'form' => 'Setono\SyliusGoogleAdsPlugin\Form\Type\ConversionActionType',
                        ],
                    ],
                ],
                'salt' => '%kernel.secret%',
            ]
        );
    }
}
