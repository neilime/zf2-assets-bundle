<?php

return array(
    'assets_bundle' => array(
        'baseUrl' => '/',
        'cachePath' => '@zfRootPath/_files/cache',
        'assetsPath' => '@zfRootPath/_files/assets',
        'processedDirPath' => '@zfRootPath/_files/processed'
    ),
    'router' => array('routes' => array(
            'test' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/test',
                    'defaults' => array('controller' => 'AssetsBundleTest\Controller\Test', 'action' => 'test')
                )
            ),
            'FileError' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/file-error',
                    'defaults' => array('controller' => 'AssetsBundleTest\Controller\Test', 'action' => 'fileError')
                )
            ),
            'Empty' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/empty',
                    'defaults' => array('controller' => 'AssetsBundleTest\Controller\Test', 'action' => 'empty')
                )
            ),
            'Exception' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/exception',
                    'defaults' => array('controller' => 'AssetsBundleTest\Controller\Test', 'action' => 'exception')
                )
            )
        )),
    'controllers' => array(
        'invokables' => array(
            'AssetsBundleTest\Controller\Test' => 'AssetsBundleTest\Controller\TestController'
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'layout/layout' => __DIR__ . '/_files/views/layout.phtml',
            'test' => __DIR__ . '/_files/views/test.phtml',
            'error' => __DIR__ . '/_files/views/error.phtml'
        )
    )
);
