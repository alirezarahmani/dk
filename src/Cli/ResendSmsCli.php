<?php

declare(strict_types=1);

namespace Digikala\Cli;

use Digikala\Cli\DigikalaCommand;
use Digikala\Repository\Persistence\NotificationRepository;
use Digikala\Services\NotificationService;
use Digikala\Services\QueueService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResentSmsCli
 * @package SocialNetwork\Infrastructure\Cli
 */
class ResendSmsCli extends DigikalaCommand
{
    protected function configure()
    {
        $this
            ->setName('resend:msg')
            ->setDescription('resend unsent sms');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var QueueService $queue */
        $queue = $this->container->get(QueueService::class);
        /** @var NotificationService $notification */
        $notification = $this->container->get(NotificationService::class);
        $results = $queue->getAndRemove(NotificationRepository::QUEUE, 10);
        foreach ($results as $result) {
            $notification->resend($result['id']);
        }
        $output->write('done');
    }
}
