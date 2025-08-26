<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Uploader;

use Doctrine\Persistence\ManagerRegistry;
use Google\Ads\GoogleAds\V19\Common\Consent;
use Google\Ads\GoogleAds\V19\Common\CustomerMatchUserListMetadata;
use Google\Ads\GoogleAds\V19\Common\UserData;
use Google\Ads\GoogleAds\V19\Enums\ConsentStatusEnum\ConsentStatus;
use Google\Ads\GoogleAds\V19\Enums\OfflineUserDataJobTypeEnum\OfflineUserDataJobType;
use Google\Ads\GoogleAds\V19\Resources\OfflineUserDataJob;
use Google\Ads\GoogleAds\V19\Services\OfflineUserDataJobOperation;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusGoogleAdsPlugin\Client\ClientInterface;
use Setono\SyliusGoogleAdsPlugin\DataProvider\CustomerDataProviderInterface;
use Setono\SyliusGoogleAdsPlugin\Factory\ClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Model\CustomerListInterface;
use Setono\SyliusGoogleAdsPlugin\Resolver\UserIdentifier\UserIdentifierResolverInterface;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final class CustomerUploader implements CustomerUploaderInterface
{
    use ORMTrait;

    private const BATCH_SIZE = 10000; // Google recommends up to 100k identifiers, but we use smaller batches for memory efficiency

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly ClientFactoryInterface $clientFactory,
        private readonly CustomerDataProviderInterface $customerDataProvider,
        private readonly UserIdentifierResolverInterface $userIdentifierResolver,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function upload(CustomerListInterface $customerList): void
    {
        if (!$customerList->isEnabled()) {
            return;
        }

        $channel = $customerList->getChannel();
        Assert::notNull($channel, 'Customer list must have a channel');

        $resourceName = $customerList->getResourceName();
        Assert::notNull($resourceName, 'Customer list must have a resource name');

        $client = $this->clientFactory->createFromChannel($channel);

        $userIdentifiers = [];
        foreach ($this->customerDataProvider->getCustomers($customerList) as $customer) {
            $userIdentifiers[] = $this->userIdentifierResolver->getUserIdentifiers($customer);

            // Process in batches to avoid memory issues
            if (count($userIdentifiers) >= self::BATCH_SIZE) {
                $this->uploadIdentifierBatch($client, $resourceName, $userIdentifiers);
                $userIdentifiers = [];
            }
        }

        // Process remaining identifiers
        if ([] !== $userIdentifiers) {
            $this->uploadIdentifierBatch($client, $resourceName, $userIdentifiers);
        }
    }

    /**
     * @param list<array> $userIdentifiersList
     */
    private function uploadIdentifierBatch(
        ClientInterface $client,
        string $customerListResourceName,
        array $userIdentifiersList,
    ): void {
        // Create offline user data job
        $customerMatchMetadata = new CustomerMatchUserListMetadata([
            'user_list' => $customerListResourceName,
        ]);

        // Add consent information (assuming consent is granted for this implementation)
        // In production, you should determine consent based on user preferences and privacy laws
        $consent = new Consent([
            'ad_user_data' => ConsentStatus::GRANTED,
            'ad_personalization' => ConsentStatus::GRANTED,
        ]);
        $customerMatchMetadata->setConsent($consent);

        $offlineUserDataJob = new OfflineUserDataJob([
            'type' => OfflineUserDataJobType::CUSTOMER_MATCH_USER_LIST,
            'customer_match_user_list_metadata' => $customerMatchMetadata,
        ]);

        // Create the job
        $resourceName = $client->offlineUserDataJobs()->create($offlineUserDataJob);

        // Prepare user data operations
        $operations = [];
        foreach ($userIdentifiersList as $userIdentifiers) {
            $operations[] = new OfflineUserDataJobOperation([
                'create' => new UserData([
                    'user_identifiers' => $userIdentifiers,
                ]),
            ]);
        }

        if ([] === $operations) {
            return;
        }

        // Add operations to the job
        $client->offlineUserDataJobs()->addOperations($resourceName, $operations);

        // Run the job
        $client->offlineUserDataJobs()->run($resourceName);
    }
}
