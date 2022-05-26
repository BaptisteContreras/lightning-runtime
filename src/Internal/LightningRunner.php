<?php


namespace Sflightning\Runtime;


use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

/**
 * @author Baptiste CONTRERAS <https://github.com/BaptisteContreras>
 *
 * @internal
 */
class LightningRunner implements RunnerInterface
{

    /**         Properties         **/

    /** @var LightningApplication */
    private $lightningApplication;

    /** @var HttpKernelInterface */
    private $applicationKernel;

    /**         Constructor         **/

    public function __construct(LightningApplication $lightningApplication, HttpKernelInterface $applicationKernel)
    {
        $this->lightningApplication = $lightningApplication;
        $this->applicationKernel = $applicationKernel;
    }

    /**         Methods         **/

    public function run(): int
    {
        $this->lightningApplication->boot($this->applicationKernel);

        $this->lightningApplication->start();

        return 0;
    }

}