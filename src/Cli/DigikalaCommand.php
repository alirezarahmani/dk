<?php

declare(strict_types=1);

namespace Digikala\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Container;

abstract class DigikalaCommand extends Command
{
    /**
     * @var Container
     */
    protected Container $container;

    /**
     * DigikalaCommand constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct(null);
        $this->addOption('force');
        $this->container = $container;
    }
}
