<?php
return array(
    'modules' => array(
        'AssetsBundle',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
           	__DIR__.'/../config/autoload/{,*.}{global,local}.php',
           	__DIR__.'/configuration.php'
        ),
        'module_paths' => array(
            'module',
            'vendor',
        )
    )
);