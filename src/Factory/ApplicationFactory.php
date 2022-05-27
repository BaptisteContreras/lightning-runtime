<?php

namespace Sflightning\Runtime\Factory;

use Sflightning\Runtime\LightningApplication;

/**
 * @author Baptiste CONTRERAS <https://github.com/BaptisteContreras>
 *
 * @internal
 */
final class ApplicationFactory
{
    /**         Methods         **/

    public static function createLightningApplication(array $serverOptions): LightningApplication
    {
        return new LightningApplication(
            (new ServerFactory($serverOptions))->createServer()
        );
    }
}