<?php

namespace Runtime\Lightning;

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