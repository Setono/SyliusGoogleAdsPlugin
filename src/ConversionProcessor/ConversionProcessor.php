<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Google\Ads\GoogleAds\Util\V15\ResourceNames;
use Google\Ads\GoogleAds\V15\Services\ClickConversion;
use Google\Ads\GoogleAds\V15\Services\Client\ConversionUploadServiceClient;
use Google\Ads\GoogleAds\V15\Services\UploadClickConversionsRequest;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusGoogleAdsPlugin\Event\PreSetClickConversionDataEvent;
use Setono\SyliusGoogleAdsPlugin\Event\PreSetClickConversionUserIdentifiersEvent;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Logger\ConversionLogger;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionMappingRepositoryInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;
use Webmozart\Assert\Assert;

final class ConversionProcessor implements ConversionProcessorInterface
{
    public function __construct(
        private readonly WorkflowInterface $workflow,
        private readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory,
        private readonly ConnectionMappingRepositoryInterface $connectionMappingRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function isEligible(ConversionInterface $conversion): bool
    {
        return $this->workflow->can($conversion, ConversionWorkflow::TRANSITION_UPLOAD_CONVERSION);
    }

    public function process(ConversionInterface $conversion): void
    {
        Assert::true($this->isEligible($conversion));

        $channel = $conversion->getChannel();
        Assert::notNull($channel);

        $connectionMapping = $this->connectionMappingRepository->findOneEnabledByChannel($channel);
        Assert::notNull($connectionMapping);

        $connection = $connectionMapping->getConnection();
        Assert::notNull($connection);

        $managerId = $connectionMapping->getManagerId();
        Assert::notNull($managerId);

        $customerId = $connectionMapping->getCustomerId();
        Assert::notNull($customerId);

        $order = $conversion->getOrder();
        Assert::notNull($order);

        $client = $this->googleAdsClientFactory->createFromConnection($connection, $managerId, new ConversionLogger($conversion));

        $createdAt = $conversion->getCreatedAt();
        Assert::notNull($createdAt);

        // Google doesn't allow daylight savings time when uploading, so we need to turn our time into UTC first
        $createdAt = \DateTimeImmutable::createFromInterface($createdAt)->setTimezone(new \DateTimeZone('UTC'));

        $preSetClickConversionDataEvent = new PreSetClickConversionDataEvent($conversion, array_filter([
            'conversion_action' => ResourceNames::forConversionAction(
                $customerId,
                (string) $connectionMapping->getConversionActionId(),
            ),
            'conversion_value' => round((int) $conversion->getValue() / 100, 2),
            'conversion_date_time' => $createdAt->format('Y-m-d H:i:sP'),
            'currency_code' => $conversion->getCurrencyCode(),
            'order_id' => $order->getId(),
            $conversion->getTrackingIdParameter() => $conversion->getTrackingId(),
        ]));

        $this->eventDispatcher->dispatch($preSetClickConversionDataEvent);

        $clickConversion = new ClickConversion($preSetClickConversionDataEvent->data);

        $preSetUserIdentifiersEvent = new PreSetClickConversionUserIdentifiersEvent($conversion);
        $this->eventDispatcher->dispatch($preSetUserIdentifiersEvent);

        if ([] !== $preSetUserIdentifiersEvent->userIdentifiers) {
            $clickConversion->setUserIdentifiers($preSetUserIdentifiersEvent->userIdentifiers);
        }

        /** @var ConversionUploadServiceClient $conversionUploadServiceClient */
        $conversionUploadServiceClient = $client->getConversionUploadServiceClient();
        Assert::isInstanceOf($conversionUploadServiceClient, ConversionUploadServiceClient::class);

        $response = $conversionUploadServiceClient->uploadClickConversions(
            UploadClickConversionsRequest::build($customerId, [$clickConversion], true),
        );

        if ($response->hasPartialFailureError()) {
            throw new \RuntimeException(sprintf(
                'Uploading the conversion failed: %s',
                (string) $response->getPartialFailureError()?->getMessage(),
            ));
        }

        $this->workflow->apply($conversion, ConversionWorkflow::TRANSITION_UPLOAD_CONVERSION);
    }
}
