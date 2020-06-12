<?php

declare(strict_types=1);

namespace Digikala\Cli;

use Digikala\Repository\Persistence\NotificationRepository;
use Digikala\Services\NotificationService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResentSmsCli
 * @package SocialNetwork\Infrastructure\Cli
 */
class ReportSyncCli extends DigikalaCommand
{
    protected function configure()
    {
        $this
            ->setName('sync:report')
            ->setDescription('sync report');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var NotificationService $notification */
        $repository = new NotificationRepository();
        $repository->report();
        $output->write('done');
        return 1;
    }
}
