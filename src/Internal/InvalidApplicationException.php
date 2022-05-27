<?php


namespace Sflightning\Runtime\Internal;


use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Baptiste CONTRERAS <https://github.com/BaptisteContreras>
 *
 * @internal
 */
class InvalidApplicationException extends \RuntimeException
{
    /**         Constructor         **/

    public function __construct($application)
    {
        parent::__construct(sprintf('%s application is not supported by this runtime. Please provide one of this list : [%s]', $application ? get_class($application) : 'null', HttpKernelInterface::class));
    }
}