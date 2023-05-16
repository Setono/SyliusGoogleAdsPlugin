<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConnectionMappingType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'label' => 'sylius.ui.channel',
                'placeholder' => '',
            ])
            ->add('customerId', CustomerIdChoiceType::class, [
                'label' => 'setono_sylius_google_ads.form.connection_mapping.customer_id',
                'connection' => $options['connection'],
                'placeholder' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('connection')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_google_ads_connection_mapping';
    }
}
