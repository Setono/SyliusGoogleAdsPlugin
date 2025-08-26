<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Google\Ads\GoogleAds\V19\Enums\UserListCustomerTypeCategoryEnum\UserListCustomerTypeCategory;
use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @experimental
 */
final class CustomerListType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'sylius.ui.name',
            ])
            ->add('membershipLifespan', IntegerType::class, [
                'label' => 'setono_sylius_google_ads.form.customer_list.membership_lifespan',
            ])
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'sylius.ui.channel',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'setono_sylius_google_ads.form.channel_placeholder',
            ])
        ;

        // Add event listener to conditionally make customerTypeCategory readonly
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $customerList = $event->getData();
            $form = $event->getForm();

            if (!$customerList instanceof CustomerListInterface) {
                return;
            }

            $isReadonly = null !== $customerList->getCustomerTypeCategory();

            $form->add('customerTypeCategory', ChoiceType::class, [
                'label' => 'setono_sylius_google_ads.form.customer_list.customer_type_category',
                'choices' => $this->getCustomerTypeCategoryChoices(),
                'placeholder' => 'setono_sylius_google_ads.form.customer_list.customer_type_category_placeholder',
                'required' => false,
                'disabled' => $isReadonly,
                'help' => 'setono_sylius_google_ads.form.customer_list.customer_type_category_help',
            ]);
        });
    }

    /**
     * @return array<string, int>
     */
    private function getCustomerTypeCategoryChoices(): array
    {
        $constants = (new \ReflectionClass(UserListCustomerTypeCategory::class))->getConstants();

        $choices = [];

        /** @var mixed $value */
        foreach ($constants as $constant => $value) {
            $choices[sprintf('setono_sylius_google_ads.form.customer_list.customer_type_categories.%s', strtolower($constant))] = (int) $value;
        }

        return $choices;
    }
}
