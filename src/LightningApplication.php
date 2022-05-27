<?php


namespace Sflightning\Runtime;

use Psr\Container\ContainerInterface;
use Sflightning\Contracts\Event\EventFactoryInterface;
use Sflightning\Runtime\Bridge\SymfonyHttpBridge;
use Sflightning\Runtime\Internal\ApplicationNotBootedException;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\TerminableInterface;

/**
 * @author Baptiste CONTRERAS <https://github.com/BaptisteContreras>
 */
class LightningApplication
{

    /**         Properties         **/

    /** @var Server */
    private $server;

    /** @var HttpKernelInterface */
    private $application;

    /** @var bool */
    private $booted;


    /**         Constructor         **/

    public function __construct(Server $server) {
        $this->server = $server;
        $this->booted = false;
    }

    /**         Methods         **/

    public function boot(HttpKernelInterface $applicationKernel): void
    {
        $this->application = $applicationKernel;

        $this->server->on('request', [$this, 'handleHttpRequest']);

        if ($this->application instanceof Kernel) {
            // We can do more thing with that kind of kernel
            // Our goal is to set up some hooks on the Swoole lifecycle for the application
            // It provides a prettier way to interact with Swoole. (I hope)

            // Lets boot our application to have access to the container...
            $this->application->boot();

            /** @var ContainerInterface $container */
            $container = $this->application->getContainer();

            /** @var EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $container->get('event_dispatcher');

            // We need the Lightning EventFactory to create events
            // Without that, we can't create our hooks
            /** @var ?EventFactoryInterface $lightningEventFactory */
            $lightningEventFactory = $container->has(EventFactoryInterface::class) ? $container->get(EventFactoryInterface::class) : null;

            // Now that we have the event dispatcher and factory, lets set up some useful hooks
            if ($eventDispatcher && $lightningEventFactory) {
                $this->server->on('start', function (Server $server) use ($eventDispatcher, $lightningEventFactory) {
                    $event = $lightningEventFactory->createServerStartEvent($server);
                    $eventDispatcher->dispatch($event, $event->getName());
                });

                $this->server->on('shutdown', function (Server $server) use ($eventDispatcher, $lightningEventFactory) {
                    $event = $lightningEventFactory->createServerShutdownEvent($server);
                    $eventDispatcher->dispatch($event, $event->getName());
                });

                $this->server->on('task', function (Server $server) use ($eventDispatcher, $lightningEventFactory) {
                    $event = $lightningEventFactory->createTaskEvent($server);
                    $eventDispatcher->dispatch($event, $event->getName());
                });

                $this->server->on('finish', function (Server $server) use ($eventDispatcher, $lightningEventFactory) {
                    $event = $lightningEventFactory->createFinishEvent($server);
                    $eventDispatcher->dispatch($event, $event->getName());
                });

                $this->server->on('workerStart', function (Server $server) use ($eventDispatcher, $lightningEventFactory) {
                    $event = $lightningEventFactory->createWorkerStartEvent($server);
                    $eventDispatcher->dispatch($event, $event->getName());
                });

                $this->server->on('workerStop', function (Server $server) use ($eventDispatcher, $lightningEventFactory) {
                    $event = $lightningEventFactory->createWorkerStartEvent($server);
                    $eventDispatcher->dispatch($event, $event->getName());
                });
            }
        }

        $this->booted = true;
    }

    public function start(): void
    {
        if (!$this->booted) {
            throw new ApplicationNotBootedException();
        }

        $this->server->start();
    }

    public function handleHttpRequest(Request $request, Response  $response): void
    {
        $sfRequest = SymfonyHttpBridge::convertSwooleRequest($request);

        $sfResponse = $this->application->handle($sfRequest);
        SymfonyHttpBridge::reflectSymfonyResponse($sfResponse, $response);

        if ($this->application instanceof TerminableInterface) {
            $this->application->terminate($sfRequest, $sfResponse);
        }
    }

    public function getServer(): Server
    {
        return $this->server;
    }

}