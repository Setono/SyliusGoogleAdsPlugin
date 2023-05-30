<?php
declare(strict_types=1);

namespace Tests\Setono\SyliusGoogleAdsPlugin\Application\Mailer;

use Psr\Log\LoggerInterface;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;

final class DefaultAdapter implements AdapterInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function send(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = [],
    ): void {
        $this->logger->warning('Notice that the mailer is not enabled. To enable the mailer, install the symfony/mailer and remove this service');
    }
}
