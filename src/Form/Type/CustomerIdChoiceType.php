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
use Webmozart\Assert\Assert;

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
                'choices' => function (Options $options): array {
                    $connection = $options->offsetGet('connection');
                    Assert::isInstanceOf($connection, ConnectionInterface::class);

                    return $this->customerIdsResolver->getCustomerIdsFromConnection($connection);
                },
                'choice_value' => static fn (mixed $customerId): ?string => match (true) {
                    null === $customerId || is_string($customerId) => $customerId,
                    $customerId instanceof CustomerId => $customerId->customerId,
                    default => throw new \RuntimeException('Invalid input')
                },
                'choice_label' => static fn (CustomerId $customerId): string => sprintf('%s (%d)', $customerId->label, $customerId->customerId),
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
