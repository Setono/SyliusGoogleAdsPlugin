<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Twig;

use Setono\SyliusGoogleAdsPlugin\KeyGenerator\KeyGeneratorInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class KeyGeneratorExtension extends AbstractExtension
{
    private KeyGeneratorInterface $keyGenerator;

    public function __construct(KeyGeneratorInterface $keyGenerator)
    {
        $this->keyGenerator = $keyGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_google_ads_generate_key', [$this, 'generateKey']),
        ];
    }

    public function generateKey(ChannelInterface $channel): string
    {
        return $this->keyGenerator->generate($channel);
    }
}
