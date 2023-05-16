<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Resolver;

use Google\Ads\GoogleAds\Lib\V13\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\V13\Resources\CustomerClient;
use Google\Ads\GoogleAds\V13\Services\CustomerServiceClient;
use Google\Ads\GoogleAds\V13\Services\GoogleAdsRow;
use Setono\SyliusGoogleAdsPlugin\Builder\GoogleAdsClientBuilder;
use Setono\SyliusGoogleAdsPlugin\Builder\OAuth2TokenBuilder;
use Setono\SyliusGoogleAdsPlugin\Model\ConnectionInterface;
use function Symfony\Component\String\u;

final class CustomerIdsResolver implements CustomerIdsResolverInterface
{
    public function __construct(
        private readonly OAuth2TokenBuilder $oauth2TokenBuilder,
        private readonly GoogleAdsClientBuilder $googleAdsClientBuilder,
    ) {
    }

    public function getCustomerIdsFromConnection(ConnectionInterface $connection): array
    {
        $oauthCredentials = $this->oauth2TokenBuilder->fromConnection($connection)->build();
        $client = $this->googleAdsClientBuilder->fromConnectionAndOAuthCredentials($connection, $oauthCredentials)->build();

        $rootCustomerIds = [];
        $customersResponse = $client->getCustomerServiceClient()->listAccessibleCustomers();

        foreach ($customersResponse->getResourceNames() as $customerResourceName) {
            $rootCustomerIds[] = (int) CustomerServiceClient::parseName($customerResourceName)['customer_id'];
        }

        $hierarchies = [];

        // Constructs a map of account hierarchies.
        foreach ($rootCustomerIds as $rootCustomerId) {
            $customerClientToHierarchy = $this->createCustomerClientToHierarchy($rootCustomerId, $connection);
            if (null !== $customerClientToHierarchy) {
                $hierarchies += $customerClientToHierarchy;
            }
        }

        /** @var list<CustomerClient> $customerClients */
        $customerClients = self::flatten($hierarchies);

        /** @var list<CustomerId> $customerIds */
        $customerIds = [];

        foreach ($customerClients as $customerClient) {
            $id = $customerClient->getId();
            $customerIds[$id] = new CustomerId($customerClient->getDescriptiveName(), $id);
        }

        $customerIds = array_values($customerIds);

        usort($customerIds, static function (CustomerId $customerId1, CustomerId $customerId2): int {
            return u($customerId1->label)->lower() <=> u($customerId2->label)->lower();
        });

        return $customerIds;
    }

    /**
     * Creates a map between a customer client and each of its managers' mappings.
     *
     * @param int $rootCustomerId the ID of the customer at the root of the tree
     *
     * @return array|null a map between a customer client and each of its managers' mappings if the
     *     account hierarchy can be retrieved. If the account hierarchy cannot be retrieved, returns
     *     null
     */
    private function createCustomerClientToHierarchy(int $rootCustomerId, ConnectionInterface $connection): ?array
    {
        // Creates a GoogleAdsClient with the specified login customer ID. See
        // https://developers.google.com/google-ads/api/docs/concepts/call-structure#cid for more
        // information.
        // Generate a refreshable OAuth2 credential for authentication.
        $oauthCredentials = $this->oauth2TokenBuilder->fromConnection($connection)->build();
        // Construct a Google Ads client configured from a properties file and the
        // OAuth2 credentials above.
        $client = $this->googleAdsClientBuilder->fromConnectionAndOAuthCredentials($connection, $oauthCredentials)->withLoginCustomerId($rootCustomerId)->build();

        // Creates the Google Ads Service client.
        $googleAdsServiceClient = $client->getGoogleAdsServiceClient();
        // Creates a query that retrieves all child accounts of the manager specified in search
        // calls below.
        $query = 'SELECT customer_client.client_customer, customer_client.level,'
            . ' customer_client.manager, customer_client.descriptive_name,'
            . ' customer_client.currency_code, customer_client.time_zone,'
            . ' customer_client.id FROM customer_client WHERE customer_client.level <= 1';

        $rootCustomerClient = null;
        // Adds the root customer ID to the list of IDs to be processed.
        $managerCustomerIdsToSearch = [$rootCustomerId];

        // Performs a breadth-first search algorithm to build an associative array mapping
        // managers to their child accounts ($customerIdsToChildAccounts).
        $customerIdsToChildAccounts = [];

        while (!empty($managerCustomerIdsToSearch)) {
            $customerIdToSearch = array_shift($managerCustomerIdsToSearch);
            // Issues a search request by specifying page size.
            /** @var GoogleAdsServerStreamDecorator $stream */
            $stream = $googleAdsServiceClient->searchStream(
                $customerIdToSearch,
                $query,
            );

            // Iterates over all elements to get all customer clients under the specified customer's
            // hierarchy.
            /** @var GoogleAdsRow $googleAdsRow */
            foreach ($stream->iterateAllElements() as $googleAdsRow) {
                $customerClient = $googleAdsRow->getCustomerClient();
                if (null === $customerClient) {
                    continue;
                }

                // Gets the CustomerClient object for the root customer in the tree.
                if ($customerClient->getId() === $rootCustomerId) {
                    $rootCustomerClient = $customerClient;
                }

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

        return null === $rootCustomerClient ? null
            : [$rootCustomerClient->getId() => $customerIdsToChildAccounts];
    }

    private static function flatten(array $array): array
    {
        $return = [];

        array_walk_recursive($array, static function ($a) use (&$return) { $return[] = $a; });

        return $return;
    }
}
