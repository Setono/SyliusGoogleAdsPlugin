<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\DependencyInjection;

use Setono\SyliusGoogleAdsPlugin\ConversionProcessor\ConversionProcessorInterface;
use Setono\SyliusGoogleAdsPlugin\ConversionProcessor\QualificationVoter\QualificationVoterInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusGoogleAdsExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{resources: array<string, mixed>} $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->registerForAutoconfiguration(ConversionProcessorInterface::class)
            ->addTag('setono_sylius_google_ads.conversion_processor')
        ;

        $container->registerForAutoconfiguration(QualificationVoterInterface::class)
            ->addTag('setono_sylius_google_ads.qualification_voter')
        ;

        $loader->load('services.xml');

        $this->registerResources(
            'setono_sylius_google_ads',
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            $config['resources'],
            $container,
        );
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('framework', [
            'workflows' => ConversionWorkflow::getConfig(),
        ]);
    }
}
