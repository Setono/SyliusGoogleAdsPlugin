<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ConnectionType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'setono_sylius_google_ads.form.connection.name',
                'attr' => [
                    'placeholder' => 'setono_sylius_google_ads.form.connection.name_placeholder',
                ],
            ])
            ->add('developerToken', TextType::class, [
                'label' => 'setono_sylius_google_ads.form.connection.developer_token',
            ])
            ->add('clientId', TextType::class, [
                'label' => 'setono_sylius_google_ads.form.connection.client_id',
            ])
            ->add('clientSecret', TextType::class, [
                'label' => 'setono_sylius_google_ads.form.connection.client_secret',
            ])
            ->add('accessToken', TextType::class, [
                'label' => 'setono_sylius_google_ads.form.connection.access_token',
                'disabled' => true,
                'required' => false,
                'help' => 'setono_sylius_google_ads.form.connection.access_token_help',
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.enabled',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_google_ads_connection';
    }
}
