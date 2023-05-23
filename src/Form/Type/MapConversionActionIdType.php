<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class MapConversionActionIdType extends AbstractResourceType
{
    public const STEP = 'map_conversion_action_id';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) {
            $connection = $event->getData();
            Assert::isInstanceOf($connection, ConnectionInterface::class);

            $event->getForm()->add('connectionMappings', ConnectionMappingCollectionType::class, [
                'connection' => $connection,
                'label' => false,
                'step' => self::STEP,
            ]);
        });
    }
}
