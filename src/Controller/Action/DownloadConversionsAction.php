<?php

declare(strict_types=1);

namespace Setono\SyliusGoogleAdsPlugin\Controller\Action;

use function Safe\fopen;
use function Safe\fputcsv;
use function Safe\sprintf;
use Setono\SyliusGoogleAdsPlugin\KeyGenerator\KeyGeneratorInterface;
use Setono\SyliusGoogleAdsPlugin\Model\ConversionInterface;
use Setono\SyliusGoogleAdsPlugin\Repository\ConversionRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DownloadConversionsAction
{
    private ChannelContextInterface $channelContext;

    private ConversionRepositoryInterface $conversionRepository;

    private KeyGeneratorInterface $keyGenerator;

    public function __construct(
        ChannelContextInterface $channelContext,
        ConversionRepositoryInterface $conversionRepository,
        KeyGeneratorInterface $keyGenerator
    ) {
        $this->channelContext = $channelContext;
        $this->conversionRepository = $conversionRepository;
        $this->keyGenerator = $keyGenerator;
    }

    public function __invoke(Request $request, string $key): Response
    {
        $channel = $this->channelContext->getChannel();
        if (!$this->keyGenerator->check($channel, $key)) {
            throw new NotFoundHttpException('The page you are looking for does not exist'); // todo throw an unauthorized exception?
        }

        $qb = $this->conversionRepository->findByChannelQueryBuilder($channel);
        $manager = $qb->getEntityManager();
        $iterableResult = $qb->getQuery()->iterate();

        $response = new StreamedResponse(function () use ($manager, $iterableResult): void {
            $output = fopen('php://output', 'wb');

            fputcsv($output, [sprintf('Parameters:TimeZone=%s', date_default_timezone_get())]);
            fputcsv($output, ['Google Click ID', 'Conversion Name', 'Conversion Time', 'Conversion Value', 'Conversion Currency']);

            foreach ($iterableResult as $row) {
                /** @var ConversionInterface $conversion */
                $conversion = $row[0];

                $createdAt = $conversion->getCreatedAt();
                if (null === $createdAt) {
                    throw new \LogicException(sprintf(
                        'The created at timestamp on the conversion with id %s is null. This should not be possible.',
                        $conversion->getId()
                    ));
                }

                fputcsv($output, [
                    $conversion->getGoogleClickId(), $conversion->getName(), $createdAt->format('Y-m-d\TH:i:s'),
                    self::formatValue((int) $conversion->getValue()), $conversion->getCurrencyCode(),
                ]);

                $manager->detach($row[0]);

                flush();
            }

            flush();
        }, 200, [
            'Content-Type' => 'text/csv',
        ]);

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            self::generateFilename($channel)
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    private static function formatValue(int $value): float
    {
        return round($value / 100, 2);
    }

    private static function generateFilename(ChannelInterface $channel): string
    {
        return 'conversions---' . mb_strtolower((string) $channel->getCode()) . '.csv';
    }
}
