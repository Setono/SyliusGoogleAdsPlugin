<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Form\Type;

use Setono\SyliusGoogleAdsPlugin\Model\ConnectionMappingInterface;
use Setono\SyliusGoogleAdsPlugin\Resolver\ConversionActionId;
use Setono\SyliusGoogleAdsPlugin\Resolver\ConversionActionIdsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

/**
 * @extends AbstractType<int>
 */
final class ConversionActionIdChoiceType extends AbstractType
{
    public function __construct(private readonly ConversionActionIdsResolverInterface $conversionActionIdsResolver)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('connection_mapping')
            ->setAllowedTypes('connection_mapping', ConnectionMappingInterface::class)
            ->setDefaults([
                'choices' => function (Options $options): array {
                    $connectionMapping = $options->offsetGet('connection_mapping');
                    Assert::isInstanceOf($connectionMapping, ConnectionMappingInterface::class);

                    return $this->conversionActionIdsResolver->getConversionActionIdsFromConnectionMapping($connectionMapping);
                },
                'choice_value' => static fn (mixed $conversionActionId): ?int => match (true) {
                    null === $conversionActionId || is_int($conversionActionId) => $conversionActionId,
                    $conversionActionId instanceof ConversionActionId => $conversionActionId->id,
                    default => throw new \RuntimeException('Invalid input')
                },
                'choice_label' => static fn (ConversionActionId $conversionActionId): string => sprintf('%s (%d)', $conversionActionId->label, $conversionActionId->id),
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
