<?php
return array(
	'asset_bundle' => array(
		'basePath' => '/',
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
		)
	)),
	'controllers' => array(
		'invokables' => array(
			'AssetsBundleTest\Controller\Test' => 'AssetsBundleTest\Controller\TestController',
		)
	)
);