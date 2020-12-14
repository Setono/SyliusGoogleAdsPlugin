<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\KeyGenerator;

use Sylius\Component\Channel\Model\ChannelInterface;

final class KeyGenerator implements KeyGeneratorInterface
{
    private string $salt;

    public function __construct(string $salt)
    {
        $this->salt = $salt;
    }

    public function generate(ChannelInterface $channel): string
    {
        return hash('sha256', $channel->getId() . $this->salt);
    }

    public function check(ChannelInterface $channel, string $key): bool
    {
        return $this->generate($channel) === $key;
    }
}
