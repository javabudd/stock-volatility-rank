<?php

return [
    'modules'                 => [
        'DoctrineModule',
        'DoctrineORMModule',
        'Laminas\\I18n',
        'Laminas\\Router',
        'Laminas\\Mvc\\Console',
        'IVRank'
    ],
    'module_listener_options' => [
        'use_laminas_loader'       => false,
        'config_glob_paths'        => [
            \realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'
        ],
        'config_cache_enabled'     => false,
        'config_cache_key'         => 'application.config.cache',
        'module_map_cache_enabled' => true,
        'module_map_cache_key'     => 'application.module.cache',
        'cache_dir'                => BASE_PATH . '/data/cache/',
    ],
];
