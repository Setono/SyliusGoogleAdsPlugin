<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use Doctrine\Persistence\ManagerRegistry;
use Google\Ads\GoogleAds\Lib\V15\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V15\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\V15\Enums\ConversionActionCategoryEnum\ConversionActionCategory;
use Google\Ads\GoogleAds\V15\Enums\ConversionActionStatusEnum\ConversionActionStatus;
use Google\Ads\GoogleAds\V15\Enums\ConversionActionTypeEnum\ConversionActionType;
use Google\Ads\GoogleAds\V15\Enums\ResponseContentTypeEnum\ResponseContentType;
use Google\Ads\GoogleAds\V15\Resources\ConversionAction;
use Google\Ads\GoogleAds\V15\Services\Client\ConversionActionServiceClient;
use Google\Ads\GoogleAds\V15\Services\Client\GoogleAdsServiceClient;
use Google\Ads\GoogleAds\V15\Services\ConversionActionOperation;
use Google\Ads\GoogleAds\V15\Services\GoogleAdsRow;
use Google\Ads\GoogleAds\V15\Services\MutateConversionActionResult;
use Google\Ads\GoogleAds\V15\Services\MutateConversionActionsRequest;
use Google\Ads\GoogleAds\V15\Services\SearchGoogleAdsStreamRequest;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\SyliusGoogleAdsPlugin\Factory\GoogleAdsClientFactoryInterface;
use Setono\SyliusGoogleAdsPlugin\Form\Type\MapCustomerIdType;
use Setono\SyliusGoogleAdsPlugin\Repository\ConnectionRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

final class SetupMapCustomerIdAction extends AbstractSetupAction
{
    use ORMManagerTrait;

    public function __construct(
        Environment $twig,
        ConnectionRepositoryInterface $connectionRepository,
        UrlGeneratorInterface $urlGenerator,
        private readonly FormFactoryInterface $formFactory,
        ManagerRegistry $managerRegistry,
        private readonly GoogleAdsClientFactoryInterface $googleAdsClientFactory,
    ) {
        parent::__construct($twig, $connectionRepository, $urlGenerator);

        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(Request $request, int $connectionId): Response
    {
        $connection = $this->getConnection($connectionId);

        $form = $this->formFactory->create(MapCustomerIdType::class, $connection);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getManager($connection);
            $manager->flush();

            foreach ($connection->getConnectionMappings() as $connectionMapping) {
                $client = $this->googleAdsClientFactory->createFromConnection($connection, $connectionMapping->getManagerId());

                $customerId = $connectionMapping->getCustomerId();
                Assert::notNull($customerId);

                // handle the 'conversion conversion action', i.e. the conversion action where we send conversions
                $conversionActionId = $connectionMapping->getConversionActionId();

                // when a conversion action id is already set, we will fetch that conversion action from Google
                // and verify its settings. If the settings are invalid, we will null $conversionActionId which will
                // create a new conversion action below
                if (null !== $conversionActionId) {
                    $conversionAction = $this->getConversionActionById($client, $customerId, $conversionActionId);

                    // here we verify the settings of the existing conversion action
                    if (null === $conversionAction || $conversionAction->getType() !== ConversionActionType::UPLOAD_CLICKS || $conversionAction->getStatus() !== ConversionActionStatus::ENABLED) {
                        $conversionActionId = null;
                    }
                }

                // create conversion action
                if (null === $conversionActionId) {
                    $conversionActionId = $this->createConversionAction($client, $customerId, 'Conversions - Google Ads Plugin by Setono', ConversionActionType::UPLOAD_CLICKS);
                }

                $connectionMapping->setConversionActionId($conversionActionId);
            }

            $manager->flush();

            $session = $request->getSession();
            if ($session instanceof Session) {
                $session->getFlashBag()->add('success', 'setono_sylius_google_ads.customer_id_and_conversion_action_mapped');
            }

            return new RedirectResponse($this->urlGenerator->generate('setono_sylius_google_ads_admin_connection_update', [
                'id' => $connectionId,
            ]));
        }

        return new Response($this->twig->render('@SetonoSyliusGoogleAdsPlugin/connection/setup/map_customer_id.html.twig', [
            'connection' => $connection,
            'form' => $form->createView(),
        ]));
    }

    private function getConversionActionById(GoogleAdsClient $googleAdsClient, string $customerId, string $id): ?ConversionAction
    {
        /** @var GoogleAdsServiceClient $googleAdsServiceClient */
        $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();
        Assert::isInstanceOf($googleAdsServiceClient, GoogleAdsServiceClient::class);

        /** @var GoogleAdsServerStreamDecorator $stream */
        $stream = $googleAdsServiceClient->searchStream(SearchGoogleAdsStreamRequest::build(
            $customerId,
            "SELECT conversion_action.id, conversion_action.status, conversion_action.name, conversion_action.type FROM conversion_action WHERE conversion_action.id = $id",
        ));

        /** @var GoogleAdsRow $googleAdsRow */
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $conversionAction = $googleAdsRow->getConversionAction();
            if (null === $conversionAction) {
                continue;
            }

            return $conversionAction;
        }

        return null;
    }

    /**
     * Creates a conversion action and returns its id
     */
    private function createConversionAction(GoogleAdsClient $googleAdsClient, string $customerId, string $name, int $type): string
    {
        $name = sprintf('%s [%s]', $name, (new \DateTimeImmutable())->format('Y-m-d H:i'));

        $conversionAction = new ConversionAction([
            'name' => $name,
            'category' => ConversionActionCategory::PURCHASE,
            'type' => $type,
            'status' => ConversionActionStatus::ENABLED,
        ]);

        // Creates a conversion action operation.
        $conversionActionOperation = new ConversionActionOperation();
        $conversionActionOperation->setCreate($conversionAction);

        /**
         * Issues a mutate request to add the conversion action
         *
         * @var ConversionActionServiceClient $conversionActionServiceClient
         */
        $conversionActionServiceClient = $googleAdsClient->getConversionActionServiceClient();
        Assert::isInstanceOf($conversionActionServiceClient, ConversionActionServiceClient::class);

        $response = $conversionActionServiceClient->mutateConversionActions(
            MutateConversionActionsRequest::build($customerId, [$conversionActionOperation])
                ->setResponseContentType(ResponseContentType::MUTABLE_RESOURCE),
        );

        /** @var MutateConversionActionResult $result */
        foreach ($response->getResults() as $result) {
            $conversionAction = $result->getConversionAction();
            if (null === $conversionAction) {
                continue;
            }

            return (string) $conversionAction->getId();
        }

        throw new \RuntimeException('Could not create conversion action');
    }
}
