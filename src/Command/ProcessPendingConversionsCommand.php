<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Command;

use Doctrine\Persistence\ObjectManager;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\StateResolver\StateResolverInterface;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProcessPendingConversionsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-google-ads:process-pending-conversions';

    protected static $defaultDescription = 'Processes all pending conversions where an order is related';

    public function __construct(
        private readonly ConversionRepositoryInterface $conversionRepository,
        private readonly StateResolverInterface $stateResolver,
        private readonly ObjectManager $manager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $i = 0;

        $conversions = $this->conversionRepository->findPending();
        foreach ($conversions as $conversion) {
            ++$i;

            $conversion->setState($this->stateResolver->resolve($conversion));

            if ($i % 100 === 0) {
                $this->manager->flush();
            }
        }

        $this->manager->flush();

        $io->success(sprintf('Processed %d conversions', $i));

        return 0;
    }
}
