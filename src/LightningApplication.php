<?php


namespace Sflightning\Runtime;

use Sflightning\Runtime\Bridge\SymfonyHttpBridge;
use Sflightning\Runtime\Internal\ApplicationNotBootedException;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Symfony\Component\HttpKernel\HttpKernelInterface;
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

}