<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\DependencyInjection;

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

        $container->setParameter('setono_sylius_google_ads.salt', $config['salt']);

        $loader->load('services.xml');

        $this->registerResources('setono_sylius_google_ads', $config['driver'], $config['resources'], $container);
    }
}
