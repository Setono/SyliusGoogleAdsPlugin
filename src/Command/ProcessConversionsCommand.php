<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Command;

use Setono\SyliusGoogleAdsPlugin\Message\Command\ProcessConversion;
use Setono\SyliusGoogleAdsPlugin\Provider\ConversionIdProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProcessConversionsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-google-ads:process-conversions';

    protected static $defaultDescription = 'Process conversions';

    public function __construct(
        private readonly ConversionIdProviderInterface $conversionIdProvider,
        private readonly MessageBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $i = 0;

        foreach ($this->conversionIdProvider->getConversionIds() as $conversion) {
            $this->commandBus->dispatch(new ProcessConversion($conversion));
            ++$i;
        }

        (new SymfonyStyle($input, $output))->success(sprintf('%d conversions dispatched', $i));

        return 0;
    }
}
