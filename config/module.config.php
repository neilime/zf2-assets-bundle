<?php

return array(
    'router' => array('routes' => array(
            'jscustom' => array(
                'type' => 'literal',
                'options' => array('route' => '/jscustom'),
                'may_terminate' => true,
                'child_routes' => array(
                    'definition' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/:controller/:js_action',
                            'contraints' => array('controller' => '[a-zA-Z][a-zA-Z0-9_-]*', 'js_action' => '[a-zA-Z][a-zA-Z0-9_-]*'),
                            'defaults' => array('action' => 'jscustom')
                        )
                    )
                )
            )
        )),
    'controllers' => array(
        'invokables' => array(
            'AssetsBundle\Controller\Tools' => 'AssetsBundle\Controller\ToolsController'
        )
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'render-assets' => array(
                    'options' => array(
                        'route' => 'render',
                        'defaults' => array(
                            'controller' => 'AssetsBundle\Controller\Tools',
                            'action' => 'renderAssets'
                        )
                    )
                ),
                'empty-cache' => array(
                    'options' => array(
                        'route' => 'empty',
                        'defaults' => array(
                            'controller' => 'AssetsBundle\Controller\Tools',
                            'action' => 'emptyCache'
                        )
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'LessFilter' => '\AssetsBundle\Factory\Filter\LessFilterFactory',
            'CssFilter' => '\AssetsBundle\Factory\Filter\CssFilterFactory',
            'JsFilter' => '\AssetsBundle\Factory\Filter\JsFilterFactory',
            'PngFilter' => '\AssetsBundle\Factory\Filter\PngFilterFactory',
            'JpegFilter' => '\AssetsBundle\Factory\Filter\JpegFilterFactory',
            'GifFilter' => '\AssetsBundle\Factory\Filter\GifFilterFactory',
            'AssetsBundleService' => '\AssetsBundle\Factory\ServiceFactory',
            'JsCustomStrategy' => '\AssetsBundle\Factory\JsCustomStrategyFactory',
            'JsCustomRenderer' => '\AssetsBundle\Factory\JsCustomRendererFactory'
        )
    ),
    'asset_bundle' => array(
        'production' => true, // Application environment (Developpement => false)
        'lastModifiedTime' => null, // Arbitrary last modified time in production
        'cachePath' => '@zfRootPath/public/cache', // Cache directory absolute path
        'configCachePath' => '@zfRootPath/data/cache', // Configuration cache directory absolute path
        'assetsPath' => '@zfRootPath/public', // Assets directory absolute path (allows you to define relative path for assets config)
        'baseUrl' => null, // Base URL of the application
        'cacheUrl' => '@zfBaseUrl/cache/', // Cache directory base url
        'mediaExt' => array('jpeg', 'jpg', 'png', 'gif', 'cur', 'ttf', 'eot', 'svg', 'woff'), // Put here all media extensions to be cached
        'recursiveSearch' => false, // Allows search for matching assets in required folder and its subfolders
        'filters' => array(
            \AssetsBundle\Service\Service::ASSET_LESS => 'LessFilter',
            \AssetsBundle\Service\Service::ASSET_CSS => 'CssFilter',
            \AssetsBundle\Service\Service::ASSET_JS => 'JsFilter',
            'png' => 'PngFilter',
            'jpg' => 'JpegFilter', 'jpeg' => 'JpegFilter',
            'gif' => 'GifFilter'
        )
    ),
    'view_manager' => array('strategies' => array('JsCustomStrategy'))
);
