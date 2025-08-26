<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class MerchantMappingType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'label' => 'sylius.ui.channel',
                'placeholder' => 'setono_sylius_google_ads.form.channel_placeholder',
            ])
            ->add('merchantId', TextType::class, [
                'label' => 'setono_sylius_google_ads.form.merchant_mapping.merchant_id',
            ])
        ;
    }
}
