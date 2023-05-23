<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

final class ConnectionMappingType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (MapCustomerIdType::STEP === $options['step']) {
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
                ]);
        }

        if (MapConversionActionIdType::STEP === $options['step']) {
            $builder
                ->add('channel', TextType::class, [
                    'label' => 'sylius.ui.channel',
                    'disabled' => true,
                ])
                ->add('customerId', TextType::class, [
                    'label' => 'setono_sylius_google_ads.form.connection_mapping.customer_id',
                    'disabled' => true,
                ])
            ;

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var ConnectionMappingInterface|mixed $connectionMapping */
                $connectionMapping = $event->getData();
                Assert::isInstanceOf($connectionMapping, ConnectionMappingInterface::class);

                $event->getForm()->add('conversionActionId', ConversionActionIdChoiceType::class, [
                    'label' => 'setono_sylius_google_ads.form.connection_mapping.conversion_action_id',
                    'connection_mapping' => $connectionMapping,
                ]);
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('step', null)
            ->setAllowedValues('step', [null, MapCustomerIdType::STEP, MapConversionActionIdType::STEP])
            ->setRequired('connection');
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_google_ads_connection_mapping';
    }
}
