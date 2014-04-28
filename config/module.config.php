<?php

return array(
    'router' => include __DIR__ . '/module.config.router.php',
    'controllers' => include __DIR__ . '/module.config.controllers.php',
    'console' => include __DIR__ . '/module.config.console.php',
    'service_manager' => include __DIR__ . '/module.config.service-manager.php',
    'assets_bundle' => include __DIR__ . '/module.config.assets-bundle.php',
    'view_manager' => include __DIR__ . '/module.config.view-manager.php'
);
