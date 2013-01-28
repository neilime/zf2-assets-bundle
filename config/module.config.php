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
						'contraints' => array('controller' => '[a-zA-Z][a-zA-Z0-9_-]*','js_action' => '[a-zA-Z][a-zA-Z0-9_-]*'),
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
						'route'    => 'render',
						'defaults' => array(
							'controller' => 'AssetsBundle\Controller\Tools',
							'action' => 'renderassets'
						)
					)
				),
				'empty-cache' => array(
					'options' => array(
						'route'    => 'empty',
						'defaults' => array(
							'controller' => 'AssetsBundle\Controller\Tools',
							'action' => 'emptycache'
						)
					)
				)
			)
		)
	),
	'service_manager' => array(
        'factories' => array(
            'CssFilter' => '\AssetsBundle\Factory\Filter\CssFilterFactory',
        	'JsFilter' => '\AssetsBundle\Factory\Filter\JsFilterFactory',
        	'LessFilter' => '\AssetsBundle\Factory\Filter\LessFilterFactory',
        	'AssetsBundleService' => '\AssetsBundle\Factory\ServiceFactory',
            'ViewJsCustomStrategy' => function(){
            	return new \AssetsBundle\View\Strategy\JsCustomStrategy(new \AssetsBundle\View\Renderer\JsRenderer());
            }
        )
    ),
    'asset_bundle' => array(
		'production' => true,//Define here environment (Developpement => false)
    	'cachePath' => '@zfRootPath/public/cache',//cache directory absolute path
    	'assetsPath' => '@zfRootPath/public',//assets directory absolute path (allows you to define relative path for assets config)
    	'cacheUrl' => '@zfBaseUrl/assets/cache/',//cache directory base url
    	'mediaExt' => array('jpg','png','gif','cur','ttf','eot','svg','woff'),//Put here all media extensions to be cached
    	'rendererToStrategy' => array(
            'Zend\View\Renderer\PhpRenderer'  => '\AssetsBundle\View\Strategy\ViewHelperStrategy',
            'Zend\View\Renderer\FeedRenderer' => '\AssetsBundle\View\Strategy\NoneStrategy',
            'Zend\View\Renderer\JsonRenderer' => '\AssetsBundle\View\Strategy\NoneStrategy'
        )
    ),
    'view_manager' => array('strategies' => array('ViewJsCustomStrategy'))
);