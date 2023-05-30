<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Command;

use Setono\SyliusGoogleAdsPlugin\Message\Command\ProcessConversion;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProcessReadyConversionsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-google-ads:process-ready-conversions';

    protected static $defaultDescription = 'Process ready conversions';

    public function __construct(
        private readonly ConversionRepositoryInterface $conversionRepository,
        private readonly MessageBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $i = 0;

        $processIdentifier = bin2hex(random_bytes(8));
        $this->conversionRepository->updateReadyWithProcessIdentifier($processIdentifier);

        $conversions = $this->conversionRepository->findReadyByProcessIdentifier($processIdentifier);
        foreach ($conversions as $conversion) {
            ++$i;

            $this->commandBus->dispatch(new ProcessConversion($conversion));
        }

        $io->success(sprintf('Processed %d conversions', $i));

        return 0;
    }
}
