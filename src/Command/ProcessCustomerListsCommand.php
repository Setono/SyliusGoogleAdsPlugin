<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Command;

use Setono\SyliusGoogleAdsPlugin\Processor\CustomerList\CustomerListProcessorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @experimental
 */
#[AsCommand(
    'setono:sylius-google-ads:process-customer-lists',
    'Will upload customers to customer lists inside Google Ads',
)]
final class ProcessCustomerListsCommand extends Command
{
    public function __construct(private readonly CustomerListProcessorInterface $customerListProcessor)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->customerListProcessor->process();

        return 0;
    }
}
