<?php


namespace Sflightning\Runtime;

/**
 * @author Baptiste CONTRERAS <https://github.com/BaptisteContreras>
 *
 * @internal
 */
class ApplicationNotBootedException extends \RuntimeException
{
    /**         Constructor         **/

    public function __construct()
    {
        parent::__construct('The application is not booted');
    }
}