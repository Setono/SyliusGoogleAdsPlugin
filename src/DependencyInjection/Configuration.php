<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\DependencyInjection;

use Setono\SyliusGoogleAdsPlugin\Doctrine\ORM\ConversionActionRepository;
use Setono\SyliusGoogleAdsPlugin\Doctrine\ORM\ConversionRepository;
use Setono\SyliusGoogleAdsPlugin\Form\Type\ConversionActionType;
use Setono\SyliusGoogleAdsPlugin\Model\Conversion;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionAction;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionActionInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_google_ads');

        $rootNode = $treeBuilder->getRootNode();

        /** @psalm-suppress MixedMethodCall,PossiblyNullReference,PossiblyUndefinedMethod */
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')
                    ->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('salt')
                    ->info('The salt is used to generate the keys for the URLs used for downloading conversions. It is a good idea to set this value so it is independent of the kernel.secret.')
                    ->example('l0ng$tringth4t1$n0te4$y2guess')
                    ->defaultValue('%kernel.secret%')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('default_conversion_states')
                    ->info('A list of default states on conversions based on conversion action category')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode(ConversionActionInterface::CATEGORY_ADD_TO_CART)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_BEGIN_CHECKOUT)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_BOOK_APPOINTMENT)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_CONTACT)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_GET_DIRECTIONS)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_OTHER)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_OUTBOUND_CLICK)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_PAGE_VIEW)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_PURCHASE)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_REQUEST_QUOTE)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_SIGN_UP)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_SUBMIT_LEAD_FORM)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                        ->scalarNode(ConversionActionInterface::CATEGORY_SUBSCRIBE)
                            ->defaultValue(ConversionInterface::STATE_READY)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        /** @psalm-suppress MixedMethodCall,PossiblyNullReference,PossiblyUndefinedMethod */
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('conversion')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Conversion::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ConversionRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(DefaultResourceType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('conversion_action')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ConversionAction::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ConversionActionRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(ConversionActionType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
