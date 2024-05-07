<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\DependencyInjection;

use Setono\SyliusGoogleAdsPlugin\ConversionProcessor\ConversionProcessorInterface;
use Setono\SyliusGoogleAdsPlugin\ConversionProcessor\QualificationVoter\QualificationVoterInterface;
use Setono\SyliusGoogleAdsPlugin\Doctrine\DBAL\Type\ConsentType;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Webmozart\Assert\Assert;

final class SetonoSyliusGoogleAdsExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{cookie_name: string, storage: string, resources: array<string, mixed>} $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->registerForAutoconfiguration(ConversionProcessorInterface::class)
            ->addTag('setono_sylius_google_ads.conversion_processor')
        ;

        $container->registerForAutoconfiguration(QualificationVoterInterface::class)
            ->addTag('setono_sylius_google_ads.qualification_voter')
        ;

        $container->setParameter('setono_sylius_google_ads.cookie_name', $config['cookie_name']);
        $container->setParameter('setono_sylius_google_ads.storage', $config['storage']);

        if ('cookie' === $config['storage']) {
            $loader->load('services/conditional/storage_cookie.xml');
        } else {
            $bundles = $container->getParameter('kernel.bundles');
            Assert::isArray($bundles);

            if (!array_key_exists('SetonoClientBundle', $bundles)) {
                throw new \RuntimeException('You need to install the SetonoClientBundle in order to use the client_metadata storage. Run "composer require setono/client-bundle" to install it. See https://github.com/Setono/client-bundle');
            }

            $loader->load('services/conditional/storage_client_metadata.xml');
        }

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
        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'types' => [
                    ConsentType::NAME => ConsentType::class,
                ],
            ],
        ]);

        $container->prependExtensionConfig('framework', [
            'workflows' => ConversionWorkflow::getConfig(),
        ]);
    }
}
