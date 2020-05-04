<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Setono\SyliusGoogleAdsPlugin\Model\Conversion;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ConversionType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('category', ChoiceType::class, [
                'label' => 'setono_sylius_google_ads.ui.category',
                'attr' => [
                    'placeholder' => 'setono_sylius_google_ads.ui.category_placeholder',
                ],
                'choices' => Conversion::getCategories(),
                'choice_label' => static function (string $choice): string {
                    return 'setono_sylius_google_ads.ui.category_choices.' . $choice;
                },
                'preferred_choices' => [
                    ConversionInterface::CATEGORY_PURCHASE,
                ],
            ])
            ->add('conversionId', TextType::class, [
                'label' => 'setono_sylius_google_ads.ui.conversion_id',
                'attr' => [
                    'placeholder' => 'setono_sylius_google_ads.ui.conversion_id_placeholder',
                ],
            ])
            ->add('conversionLabel', TextType::class, [
                'label' => 'setono_sylius_google_ads.ui.conversion_label',
                'attr' => [
                    'placeholder' => 'setono_sylius_google_ads.ui.conversion_label_placeholder',
                ],
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.enabled',
            ])
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.product.channels',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_google_ads_conversion';
    }
}
