<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGoogleAdsPlugin\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Setono\SyliusGoogleAdsPlugin\DependencyInjection\Configuration;
use Setono\SyliusGoogleAdsPlugin\Doctrine\ORM\ConversionActionRepository;
use Setono\SyliusGoogleAdsPlugin\Doctrine\ORM\ConversionRepository;
use Setono\SyliusGoogleAdsPlugin\Form\Type\ConnectionType;
use Setono\SyliusGoogleAdsPlugin\Form\Type\ConversionActionType;
use Setono\SyliusGoogleAdsPlugin\Model\Connection;
use Setono\SyliusGoogleAdsPlugin\Model\Conversion;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionAction;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
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
                'driver' => SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
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
                    'conversion_action' => [
                        'classes' => [
                            'model' => ConversionAction::class,
                            'controller' => ResourceController::class,
                            'repository' => ConversionActionRepository::class,
                            'factory' => Factory::class,
                            'form' => ConversionActionType::class,
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
            ],
        );
    }
}
