<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\DependencyInjection;

use RuntimeException;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusGoogleAdsExtension extends AbstractResourceExtension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('setono_sylius_google_ads.use_analytics_plugin', $config['use_analytics_plugin']);

        $bundles = $container->hasParameter('kernel.bundles') ? $container->getParameter('kernel.bundles') : [];
        if ($config['use_analytics_plugin'] && !isset($bundles['SetonoSyliusAnalyticsPlugin'])) {
            throw new RuntimeException('You have indicated that you want to use the Setono Sylius Analytics Plugin, but the plugin is not enabled in the current environment. Add the plugin to config/bundles.php or set the config option "use_analytics_plugin" to false');
        }

        $loader->load('services.xml');

        $this->registerResources('setono_sylius_google_ads', $config['driver'], $config['resources'], $container);
    }
}
