<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Client\Resource;

use Google\Ads\GoogleAds\Lib\V19\GoogleAdsClient;
use Google\Ads\GoogleAds\V19\Resources\GoogleAdsField;
use Google\Ads\GoogleAds\V19\Services\GoogleAdsRow;
use Google\Ads\GoogleAds\V19\Services\SearchGoogleAdsFieldsRequest;
use Google\Protobuf\Internal\Message;
use Setono\SyliusGoogleAdsPlugin\Client\ClientInterface;
use function Symfony\Component\String\u;
use Webmozart\Assert\Assert;

/**
 * @experimental
 *
 * @template T of Message
 */
abstract class AbstractResource
{
    /** @var array<string, list<string>> */
    private static array $fieldsCache = [];

    public function __construct(
        protected readonly ClientInterface $client,
        protected readonly GoogleAdsClient $googleAdsClient,
    ) {
    }

    /**
     * @return T|null
     */
    public function get(string $resourceName): ?object
    {
        $resourceType = static::getResourceType();
        $fields = $this->getSelectableFields($resourceType);

        if ([] === $fields) {
            throw new \RuntimeException(sprintf('No selectable fields found for resource type: %s', $resourceType));
        }

        $results = $this->client->search(sprintf(
            "SELECT %s FROM %s WHERE %s.resource_name = '%s'",
            implode(', ', array_map(static fn (string $field) => sprintf('%s.%s', $resourceType, $field), $fields)),
            $resourceType,
            $resourceType,
            $resourceName,
        ));

        foreach ($results as $result) {
            $obj = static::getResourceFromGoogleAdsRow($result);
            if (null !== $obj) {
                return $obj;
            }
        }

        return null;
    }

    public function createOrUpdate(?string $resourceName, callable $create, callable $update): string
    {
        if (!$this instanceof ReadableResourceInterface) {
            throw new \RuntimeException(sprintf('The resource must implement %s', ReadableResourceInterface::class));
        }

        if (!$this instanceof UpdatableResourceInterface) {
            throw new \RuntimeException(sprintf('The resource must implement %s', UpdatableResourceInterface::class));
        }

        $obj = null;

        if (null !== $resourceName) {
            $obj = $this->get($resourceName);
        }

        if (null === $obj) {
            /** @psalm-suppress UnsafeInstantiation */
            $obj = new (static::getResourceClass());

            $create($obj);
            $update($obj);

            $resourceName = $this->create($obj);
        } else {
            $update($obj);
            $resourceName = $this->update($obj);
        }

        return $resourceName;
    }

    /**
     * @return class-string<T>
     */
    abstract protected static function getResourceClass(): string;

    /**
     * Get the resource type name (e.g., "user_list", "user_list_customer_type")
     */
    protected static function getResourceType(): string
    {
        $classNameParts = explode('\\', static::getResourceClass());
        $className = end($classNameParts);

        return u($className)->snake()->toString();
    }

    /**
     * @return T|null
     */
    protected static function getResourceFromGoogleAdsRow(GoogleAdsRow $row): ?object
    {
        $method = sprintf('get%s', str_replace('_', '', ucwords(static::getResourceType(), '_')));
        $obj = $row->{$method}();
        Assert::nullOrIsInstanceOf($obj, static::getResourceClass());

        return $obj;
    }

    /**
     * Get selectable fields for a resource type using Google Ads Field Service
     *
     * @return list<string>
     */
    private function getSelectableFields(string $resourceType): array
    {
        // todo cache this with Symfony cache
        if (isset(self::$fieldsCache[$resourceType])) {
            return self::$fieldsCache[$resourceType];
        }

        /** @var iterable<GoogleAdsField> $fieldResponse */
        $fieldResponse = $this->googleAdsClient
            ->getGoogleAdsFieldServiceClient()
            ->searchGoogleAdsFields(
                SearchGoogleAdsFieldsRequest::build(sprintf("SELECT name WHERE name LIKE '%s.%%' AND selectable = true", $resourceType)),
            )
        ;

        $fields = [];
        foreach ($fieldResponse as $field) {
            $fieldName = $field->getName();
            $prefix = $resourceType . '.';
            if (str_starts_with($fieldName, $prefix)) {
                // Remove the resource prefix to get just the field name
                $fieldName = substr($fieldName, strlen($prefix));

                // Skip nested fields for simplicity (those with dots)
                if (!str_contains($fieldName, '.')) {
                    $fields[] = $fieldName;
                }
            }
        }

        self::$fieldsCache[$resourceType] = $fields;

        return $fields;
    }

    protected function handleMutateResponse(object $response): string
    {
        if (!method_exists($response, 'getResults')) {
            throw new \RuntimeException('The response does not have a results method');
        }

        /** @var mixed $results */
        $results = $response->getResults();
        Assert::isInstanceOf($results, \Traversable::class);

        $results = iterator_to_array($results, false);
        Assert::count($results, 1);

        $result = $results[0];
        Assert::object($result);
        if (!method_exists($result, 'getResourceName')) {
            throw new \RuntimeException('The result does not have a resource name method');
        }

        /** @var mixed $resourceName */
        $resourceName = $result->getResourceName();
        Assert::string($resourceName);

        return $resourceName;
    }
}
