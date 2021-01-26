<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Command;

use Doctrine\Persistence\ManagerRegistry;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\StateResolver\StateResolverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $conversions = $this->conversionRepository->findPending();
        foreach ($conversions as $conversion) {
            $conversion->setState($this->stateResolver->resolve($conversion));
        }

        if (isset($conversion)) {
            $manager = $this->managerRegistry->getManagerForClass(get_class($conversion));
            if (null !== $manager) {
                $manager->flush();
            }
        }

        return 0;
    }
}
