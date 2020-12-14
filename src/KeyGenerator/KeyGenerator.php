<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\KeyGenerator;

use Sylius\Component\Channel\Model\ChannelInterface;

final class KeyGenerator implements KeyGeneratorInterface
{
    private string $salt;

    private string $algo;

    public function __construct(string $salt, string $algo = 'sha256')
    {
        $this->salt = $salt;
        $this->algo = $algo;
    }

    public function generate(ChannelInterface $channel): string
    {
        return hash($this->algo, $channel->getId() . $this->salt);
    }

    public function check(ChannelInterface $channel, string $key): bool
    {
        return $this->generate($channel) === $key;
    }
}
