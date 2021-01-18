<?php

namespace IVRank;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig(): array
    {
        return require \BASE_PATH . '/src/module.config.php';
    }
}
