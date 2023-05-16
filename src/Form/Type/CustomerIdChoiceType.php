<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use Setono\SyliusGoogleAdsPlugin\Resolver\CustomerId;
use Setono\SyliusGoogleAdsPlugin\Resolver\CustomerIdsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerIdChoiceType extends AbstractType
{
    public function __construct(private readonly CustomerIdsResolverInterface $customerIdsResolver)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('connection')
            ->setAllowedTypes('connection', ConnectionInterface::class)
            ->setDefaults([
                'choices' => fn (Options $options): array => $this->customerIdsResolver->getCustomerIdsFromConnection($options['connection']),
                'choice_value' => static function ($customerId): ?int {
                    if(null === $customerId) {
                        return null;
                    }

                    if(is_int($customerId)) {
                        return $customerId;
                    }

                    return $customerId->customerId;
                },
                'choice_label' => static function (CustomerId $customerId): string {
                    return sprintf('%s (%d)', $customerId->label, $customerId->customerId);
                },
                'choice_translation_domain' => false,
        ])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_google_ads_customer_id_choice';
    }
}
