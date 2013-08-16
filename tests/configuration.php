<?php
return array(
	'asset_bundle' => array(
		'baseUrl' => '/',
		'cachePath' => '@zfRootPath/AssetsBundleTest/_files/cache',
		'assetsPath' => '@zfRootPath/AssetsBundleTest/_files/assets'
	),
	'router' => array('routes' => array(
		'test' => array(
			'type' => 'Zend\Mvc\Router\Http\Literal',
			'options' => array(
				'route' => '/test',
				'defaults' => array('controller' => 'AssetsBundleTest\Controller\Test','action' => 'test')
			)
		),
		'FileError' => array(
			'type' => 'Zend\Mvc\Router\Http\Literal',
			'options' => array(
				'route' => '/file-error',
				'defaults' => array('controller' => 'AssetsBundleTest\Controller\Test','action' => 'fileError')
			)
		),
		'Empty' => array(
			'type' => 'Zend\Mvc\Router\Http\Literal',
			'options' => array(
				'route' => '/empty',
				'defaults' => array('controller' => 'AssetsBundleTest\Controller\Test','action' => 'empty')
			)
		),
		'Exception' => array(
			'type' => 'Zend\Mvc\Router\Http\Literal',
			'options' => array(
				'route' => '/exception',
				'defaults' => array('controller' => 'AssetsBundleTest\Controller\Test','action' => 'exception')
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
			'test' => __DIR__.'/AssetsBundleTest/_files/views/test.phtml',
			'error' => __DIR__.'/AssetsBundleTest/_files/views/error.phtml'
		)
	)
);