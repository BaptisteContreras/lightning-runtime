<?php


namespace Sflightning\Runtime;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

/**
 * @author Baptiste CONTRERAS <https://github.com/BaptisteContreras>
 */
class LightningRuntime extends SymfonyRuntime
{

    /**         Properties         **/

    /** @var LightningApplication */
    private $lightningApplication;


    /**         Constructor         **/

    public function __construct(array $options)
    {
        $this->lightningApplication = ApplicationFactory::createLightningApplication($options);

        parent::__construct($options);
    }

    /**         Methods         **/

    public function getRunner(?object $application): RunnerInterface
    {
        if ($application instanceof  HttpKernelInterface) {
            return (new LightningRunner($this->lightningApplication, $application));
        }

        throw new InvalidApplicationException($application);
    }
}