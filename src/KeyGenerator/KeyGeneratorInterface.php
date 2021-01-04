<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\KeyGenerator;

use Sylius\Component\Channel\Model\ChannelInterface;

interface KeyGeneratorInterface
{
    public function generate(ChannelInterface $channel): string;

    public function check(ChannelInterface $channel, string $key): bool;
}
