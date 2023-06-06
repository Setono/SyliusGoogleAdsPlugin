<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\ConversionProcessor;

use Google\Ads\GoogleAds\Util\V13\ResourceNames;
use Google\Ads\GoogleAds\V13\Services\ClickConversion;
use Setono\SyliusGoogleAdsPlugin\Exception\WrongOrderTypeException;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Model\OrderInterface;
use Setono\SyliusGoogleAdsPlugin\Workflow\ConversionWorkflow;
use Webmozart\Assert\Assert;

final class ConversionProcessor extends AbstractConversionProcessor
{
    public function process(ConversionInterface $conversion): void
    {
        if (!$this->workflow->can($conversion, ConversionWorkflow::TRANSITION_UPLOAD_CONVERSION)) {
            throw new \RuntimeException(sprintf(
                'Cannot complete the transition "%s". State was: "%s"',
                ConversionWorkflow::TRANSITION_UPLOAD_CONVERSION,
                $conversion->getState(),
            ));
        }

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

        /** @var OrderInterface $order */
        $order = $conversion->getOrder();
        WrongOrderTypeException::assert($order);

        $client = $this->googleAdsClientFactory->createFromConnection($connection, $managerId);

        $clickConversion = new ClickConversion([
            'conversion_action' => ResourceNames::forConversionAction(
                (string) $customerId,
                (string) $connectionMapping->getConversionActionId(),
            ),
            'conversion_value' => round((int) $conversion->getValue() / 100, 2),
            'conversion_date_time' => $conversion->getCreatedAt()?->format('Y-m-d H:i:sP'),
            'currency_code' => $conversion->getCurrencyCode(),
            'order_id' => $order->getId(),
            'gclid' => $conversion->getGoogleClickId(),
        ]);

        $conversionUploadServiceClient = $client->getConversionUploadServiceClient();

        $response = $conversionUploadServiceClient->uploadClickConversions(
            (string) $customerId,
            [$clickConversion],
            true, // notice that we only add one operation so in practice it's not a partial error, but just an error
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
