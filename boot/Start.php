<?php

declare(strict_types=1);

namespace Boot;

use ArrayObject;
use Assert\Assertion;
use Digikala\Lib\Notifications\Sms;
use Digikala\Repository\Persistence\NotificationRepository;
use Digikala\Services\NotificationService;
use Digikala\Services\QueueService;
use Digikala\Services\RedisService;
use Exception;
use SplFileInfo;
use Digikala\Services\MemcachedService;
use Digikala\Storage\MemcachedCacheStorage;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class Boot
 * @package Boot
 */
class Start
{
    const CONSOLE_APPLICATION = 'console_application';

    private static $containerBuilder;

    private function __construct(Container $containerBuilder)
    {
        self::$containerBuilder = $containerBuilder;
    }

    public function explode(): void
    {
        if (PHP_SAPI == "cli") {
            $this->runCli(new ArgvInput(), new ConsoleOutput());
        } else {
            $this->runHttp(Request::createFromGlobals());
        }
    }

    private function runHttp(Request $request)
    {
        self::$containerBuilder->set('_request', $request);
    }

    private function runCli(InputInterface $input, OutputInterface $output): void
    {
        $application = new Application();
        $application->setAutoExit(false);
        /** @var Container $container */
        $container = self::$containerBuilder;
        $application->setCatchExceptions(false);
        foreach ($container->get(self::CONSOLE_APPLICATION)['classes'] as $class) {
            $application->add(new $class($container));
        }
        try {
            $application->run($input, $output);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private static function loadConsoleApplications(string $appPath): array
    {
        $classes = [];
        $dir = $appPath . '/src/Cli';
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('no valid directory');
        }
        $finder = new Finder();

        foreach ($finder->files()->name('*Cli.php')->in($dir) as $file) {
            /**
             * @var SplFileInfo $file
             */
            $className = 'Digikala\\Cli\\'.substr($file->getRelativePathname(), 0, -4);
            $reflection = new \ReflectionClass($className);
            if ($reflection->isInstantiable()) {
                $classes[] = $className;
            }
        }
        return ['classes' => $classes];
    }

    public static function create(): self
    {
        $compiledClassName = 'MyCachedContainer';
        $cacheDir = __DIR__ . '/../cache/';
        $cachedContainerFile = "{$cacheDir}container.php";

        //create container if not exist
        if (!is_file($cachedContainerFile)) {
            $configFile = __DIR__ . '/../config/setting.yml';
            Assertion::file($configFile, ' the ' . $configFile . ' found.');
            $container = new ContainerBuilder(new ParameterBag());
            $container->register(MemcachedService::class)->setPublic(true);
            $container->register(RedisService::class)->setPublic(true);
            $container->register(MemcachedCacheStorage::class)
                ->addArgument(new Reference(MemcachedService::class))
                ->setPublic(true);
            $container->register(Sms::class)->setPublic(true);
            $container->register(NotificationRepository::class)->setPublic(true);
            $container->register(QueueService::class)
                ->addArgument(new Reference(RedisService::class))
                ->setPublic(true);
            $container->register(NotificationService::class)
                ->addArgument(new Reference(Sms::class))
                ->addArgument(new Reference(NotificationRepository::class))
                ->addArgument(new Reference(QueueService::class))
                ->setPublic(true);
            $container->compile();
            file_put_contents($cachedContainerFile, (new PhpDumper($container))->dump(['class' => $compiledClassName]));
        }

        /** @noinspection PhpIncludeInspection */
        include_once $cachedContainerFile;

        /**
         * @var Container $container
         */
        $container =  new $compiledClassName();
        $container->set(self::CONSOLE_APPLICATION, new ArrayObject(self::loadConsoleApplications(__DIR__ . '/../')));
        $request = Request::createFromGlobals();
        $container->set(Request::class, $request);
        return new self($container);
    }

    public static function getContainer(): Container
    {
        return self::$containerBuilder;
    }

    private static function addEventSubscribers(): void
    {
        //@todo : do something here.
    }
}
