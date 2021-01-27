<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Command;

use Doctrine\Persistence\ManagerRegistry;
use function Safe\sprintf;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\StateResolver\StateResolverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProcessPendingConversionsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-google-ads:process-pending-conversions';

    private ConversionRepositoryInterface $conversionRepository;

    private StateResolverInterface $stateResolver;

    private ManagerRegistry $managerRegistry;

    public function __construct(
        ConversionRepositoryInterface $conversionRepository,
        StateResolverInterface $stateResolver,
        ManagerRegistry $managerRegistry
    ) {
        parent::__construct();

        $this->conversionRepository = $conversionRepository;
        $this->stateResolver = $stateResolver;
        $this->managerRegistry = $managerRegistry;
    }

    protected function configure(): void
    {
        $this->setDescription('Processes all pending conversions where an order is related');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $i = 0;

        $conversions = $this->conversionRepository->findPending();
        foreach ($conversions as $conversion) {
            $conversion->setState($this->stateResolver->resolve($conversion));

            ++$i;
        }

        if (isset($conversion)) {
            /**
             * Supressing until this issue is fixed: https://github.com/vimeo/psalm/issues/5110
             *
             * @psalm-suppress MixedArgument
             */
            $manager = $this->managerRegistry->getManagerForClass(get_class($conversion));
            if (null !== $manager) {
                $manager->flush();
            }
        }

        $io->success(sprintf('Processed %d conversions', $i));

        return 0;
    }
}
