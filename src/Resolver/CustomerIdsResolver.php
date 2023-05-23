<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Google\Ads\GoogleAds\Lib\V13\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\V13\Services\CustomerServiceClient;
use Google\Ads\GoogleAds\V13\Services\GoogleAdsRow;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use function Symfony\Component\String\u;
use Webmozart\Assert\Assert;

final class CustomerIdsResolver implements CustomerIdsResolverInterface
{
    public function __construct(private readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory)
    {
    }

    public function getCustomerIdsFromConnection(ConnectionInterface $connection): array
    {
        $client = $this->googleAdsClientFactory->createFromConnection($connection);

        $rootCustomerIds = [];
        $customersResponse = $client->getCustomerServiceClient()->listAccessibleCustomers();

        foreach ($customersResponse->getResourceNames() as $customerResourceName) {
            Assert::string($customerResourceName);
            $rootCustomerIds[] = (int) CustomerServiceClient::parseName($customerResourceName)['customer_id'];
        }

        $customerIds = [];

        foreach ($rootCustomerIds as $rootCustomerId) {
            foreach ($this->getChildAccounts($rootCustomerId, $connection) as $customerId) {
                $customerIds[$customerId->customerId] = $customerId;
            }
        }

        $customerIds = array_values($customerIds);

        usort($customerIds, static fn (CustomerId $customerId1, CustomerId $customerId2): int => u($customerId1->label)->lower() <=> u($customerId2->label)->lower());

        return $customerIds;
    }

    /**
     * @return \Generator<array-key, CustomerId>
     */
    private function getChildAccounts(int $rootCustomerId, ConnectionInterface $connection): \Generator
    {
        $client = $this->googleAdsClientFactory->createFromConnection($connection, $rootCustomerId);

        $googleAdsServiceClient = $client->getGoogleAdsServiceClient();
        $query = 'SELECT customer_client.client_customer, customer_client.descriptive_name, customer_client.id FROM customer_client WHERE customer_client.hidden = FALSE AND customer_client.test_account = FALSE';

        // Adds the root customer ID to the list of IDs to be processed.
        $managerCustomerIdsToSearch = [$rootCustomerId];

        // Performs a breadth-first search algorithm to build an associative array mapping
        // managers to their child accounts ($customerIdsToChildAccounts).
        $customerIdsToChildAccounts = [];

        while (!empty($managerCustomerIdsToSearch)) {
            $customerIdToSearch = array_shift($managerCustomerIdsToSearch);
            Assert::integer($customerIdToSearch);

            /** @var GoogleAdsServerStreamDecorator $stream */
            $stream = $googleAdsServiceClient->searchStream((string) $customerIdToSearch, $query);

            /** @var GoogleAdsRow $googleAdsRow */
            foreach ($stream->iterateAllElements() as $googleAdsRow) {
                $customerClient = $googleAdsRow->getCustomerClient();
                if (null === $customerClient) {
                    continue;
                }

                yield new CustomerId($customerClient->getDescriptiveName(), $rootCustomerId, (int) $customerClient->getId());

                // The steps below map parent and children accounts. Continue here so that managers
                // accounts exclude themselves from the list of their children accounts.
                if ($customerClient->getId() === $customerIdToSearch) {
                    continue;
                }

                // For all level-1 (direct child) accounts that are a manager account, the above
                // query will be run against them to create an associative array of managers to
                // their child accounts for printing the hierarchy afterwards.
                $customerIdsToChildAccounts[$customerIdToSearch][] = $customerClient;
                // Checks if the child account is a manager itself so that it can later be processed
                // and added to the map if it hasn't been already.
                if ($customerClient->getManager()) {
                    // A customer can be managed by multiple managers, so to prevent visiting
                    // the same customer multiple times, we need to check if it's already in the
                    // map.
                    $alreadyVisited = array_key_exists(
                        $customerClient->getId(),
                        $customerIdsToChildAccounts,
                    );
                    if (!$alreadyVisited && $customerClient->getLevel() === 1) {
                        $managerCustomerIdsToSearch[] = $customerClient->getId();
                    }
                }
            }
        }
    }
}
