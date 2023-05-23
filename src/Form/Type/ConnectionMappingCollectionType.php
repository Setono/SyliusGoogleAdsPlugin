<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<array<array-key, ConnectionMappingInterface>>
 */
final class ConnectionMappingCollectionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('step', null)
            ->setAllowedValues('step', [null, MapCustomerIdType::STEP, MapConversionActionIdType::STEP])
            ->setRequired('connection')
            ->setAllowedTypes('connection', ConnectionInterface::class)
            ->setDefaults([
                'allow_add' => static function (Options $options): bool {
                    return MapConversionActionIdType::STEP !== $options['step'];
                },
                'allow_delete' => static function (Options $options): bool {
                    return MapConversionActionIdType::STEP !== $options['step'];
                },
                'by_reference' => false,
                'error_bubbling' => false,
                'entry_type' => ConnectionMappingType::class,
                'entry_options' => static function (Options $options): array {
                    return [
                        'connection' => $options['connection'],
                        'step' => $options['step'],
                    ];
                },
            ])
        ;
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_google_ads_connection_mapping_collection';
    }
}
