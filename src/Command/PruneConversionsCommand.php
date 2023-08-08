<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Command;

use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PruneConversionsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-google-ads:prune-conversions';

    protected static $defaultDescription = 'Prunes/removes conversions from the conversions table that are too old to be qualified for uploading to Google Ads';

    public function __construct(
        private readonly ConversionRepositoryInterface $conversionRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        (new SymfonyStyle($input, $output))->success(sprintf(
            '%d conversions pruned/removed',
            $this->conversionRepository->prune(),
        ));

        return 0;
    }
}
