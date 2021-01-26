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
                'default_conversion_states' => [
                    'add_to_cart' => 'ready',
                    'begin_checkout' => 'ready',
                    'book_appointment' => 'ready',
                    'contact' => 'ready',
                    'get_directions' => 'ready',
                    'other' => 'ready',
                    'outbound_click' => 'ready',
                    'page_view' => 'ready',
                    'purchase' => 'ready',
                    'request_quote' => 'ready',
                    'sign_up' => 'ready',
                    'submit_lead_form' => 'ready',
                    'subscribe' => 'ready',
                ],
            ]
        );
    }
}
