<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Command;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\StateResolver\StateResolverInterface;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProcessPendingConversionsCommand extends Command
{
    use ORMManagerTrait;

    protected static $defaultName = 'setono:sylius-google-ads:process-pending-conversions';

    protected static $defaultDescription = 'Process pending conversions';

    public function __construct(
        private readonly ConversionRepositoryInterface $conversionRepository,
        private readonly StateResolverInterface $stateResolver,
        ManagerRegistry $managerRegistry,
        private readonly int $maxChecks = 10,
        private readonly int $initialNextCheckDelay = 300,
    ) {
        parent::__construct();

        $this->managerRegistry = $managerRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $i = 0;

        $conversions = $this->conversionRepository->findPending($this->maxChecks);
        $manager = null;
        foreach ($conversions as $conversion) {
            ++$i;
            $manager = $this->getManager($conversion);

            $now = new \DateTimeImmutable();
            $conversion->setState($this->stateResolver->resolve($conversion));
            $conversion->setNextCheckAt($this->getNextCheckAt($conversion->getChecks()));
            $conversion->setLastCheckedAt($now);
            $conversion->incrementChecks();

            if ($i % 100 === 0) {
                $manager->flush();
            }
        }

        $manager?->flush();

        $io->success(sprintf('Processed %d conversions', $i));

        return 0;
    }

    private function getNextCheckAt(int $checks): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->add(new \DateInterval(sprintf('PT%dS', $this->initialNextCheckDelay * 2 ** $checks)));
    }
}
