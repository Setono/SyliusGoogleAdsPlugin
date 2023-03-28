<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusGoogleAdsExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{driver: string, resources: array<string, mixed>, salt: string, default_conversion_states: array<array-key, string>} $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('setono_sylius_google_ads.salt', $config['salt']);
        $container->setParameter('setono_sylius_google_ads.default_conversion_states', $config['default_conversion_states']);

        $loader->load('services.xml');

        $this->registerResources('setono_sylius_google_ads', $config['driver'], $config['resources'], $container);
    }
}
