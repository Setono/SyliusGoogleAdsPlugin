<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Google\Ads\GoogleAds\Lib\V15\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\V15\Services\Client\CustomerServiceClient;
use Google\Ads\GoogleAds\V15\Services\Client\GoogleAdsServiceClient;
use Google\Ads\GoogleAds\V15\Services\GoogleAdsRow;
use Google\Ads\GoogleAds\V15\Services\ListAccessibleCustomersRequest;
use Google\Ads\GoogleAds\V15\Services\SearchGoogleAdsStreamRequest;
use Google\ApiCore\ApiException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use function Symfony\Component\String\u;
use Webmozart\Assert\Assert;

final class CustomerIdsResolver implements CustomerIdsResolverInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(private readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory)
    {
        $this->logger = new NullLogger();
    }

    public function getCustomerIdsFromConnection(ConnectionInterface $connection): array
    {
        $client = $this->googleAdsClientFactory->createFromConnection($connection);

        /** @var list<string> $rootCustomerIds */
        $rootCustomerIds = [];

        /** @var CustomerServiceClient $customerServiceClient */
        $customerServiceClient = $client->getCustomerServiceClient();
        Assert::isInstanceOf($customerServiceClient, CustomerServiceClient::class);

        try {
            $customersResponse = $customerServiceClient->listAccessibleCustomers(new ListAccessibleCustomersRequest());
        } catch (ApiException $e) {
            $this->logger->error('An error occurred while trying the API call "listAccessibleCustomers"');

            throw $e;
        }

        foreach ($customersResponse->getResourceNames() as $customerResourceName) {
            Assert::string($customerResourceName);
            $rootCustomerIds[] = (string) CustomerServiceClient::parseName($customerResourceName)['customer_id'];
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
    private function getChildAccounts(string $rootCustomerId, ConnectionInterface $connection): \Generator
    {
        $client = $this->googleAdsClientFactory->createFromConnection($connection, $rootCustomerId);

        /** @var GoogleAdsServiceClient $googleAdsServiceClient */
        $googleAdsServiceClient = $client->getGoogleAdsServiceClient();
        Assert::isInstanceOf($googleAdsServiceClient, GoogleAdsServiceClient::class);

        $query = "SELECT customer_client.client_customer, customer_client.descriptive_name, customer_client.id FROM customer_client WHERE customer_client.hidden = FALSE AND customer_client.test_account = FALSE AND customer_client.status = 'ENABLED'";

        // Adds the root customer ID to the list of IDs to be processed.
        $managerCustomerIdsToSearch = [$rootCustomerId];

        // Performs a breadth-first search algorithm to build an associative array mapping
        // managers to their child accounts ($customerIdsToChildAccounts).
        $customerIdsToChildAccounts = [];

        while (!empty($managerCustomerIdsToSearch)) {
            $customerIdToSearch = array_shift($managerCustomerIdsToSearch);
            Assert::string($customerIdToSearch);

            try {
                /** @var GoogleAdsServerStreamDecorator $stream */
                $stream = $googleAdsServiceClient->searchStream(SearchGoogleAdsStreamRequest::build($customerIdToSearch, $query));
            } catch (ApiException $e) {
                $this->logger->error(sprintf(
                    'An error occurred while trying to run the query "%s" with customer id "%s": %s',
                    $query,
                    $customerIdToSearch,
                    $e->getMessage(),
                ));

                continue;
            }

            /** @var GoogleAdsRow $googleAdsRow */
            foreach ($stream->iterateAllElements() as $googleAdsRow) {
                $customerClient = $googleAdsRow->getCustomerClient();
                if (null === $customerClient) {
                    continue;
                }

                yield new CustomerId($customerClient->getDescriptiveName(), $rootCustomerId, (string) $customerClient->getId());

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

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
