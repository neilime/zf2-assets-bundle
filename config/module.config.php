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
	'service_manager' => array(
        'factories' => array(
            'CssFilter' => '\Neilime\AssetsBundle\Factory\Filter\CssFilterFactory',
        	'JsFilter' => '\Neilime\AssetsBundle\Factory\Filter\JsFilterFactory',
        	'LessFilter' => '\Neilime\AssetsBundle\Factory\Filter\LessFilterFactory',
        	'AssetsBundleService' => '\Neilime\AssetsBundle\Factory\ServiceFactory',
            'ViewJsCustomStrategy' => function(){
            	return new \Neilime\AssetsBundle\View\Strategy\JsCustomStrategy(new \Neilime\AssetsBundle\View\Renderer\JsRenderer());
            }
        )
    ),
    'asset_bundle' => array(
		'production' => true,//Define here environment (Developpement => false)
    	'cachePath' => '@zfRootPath/public/assets/cache',//cache directory absolute path
    	'assetPath' => '@zfRootPath/public/assets',//assets directory absolute path (allows you to define relative path for assets config)
    	'cacheUrl' => '@zfBaseUrl/assets/cache/',//cache directory base url
    	'imgExt' => array('png','gif','cur'),//Put here all image extensions to be cached
    	'rendererToStrategy' => array(
            'Zend\View\Renderer\PhpRenderer'  => '\Neilime\AssetsBundle\View\Strategy\ViewHelperStrategy',
            'Zend\View\Renderer\FeedRenderer' => '\Neilime\AssetsBundle\View\Strategy\NoneStrategy',
            'Zend\View\Renderer\JsonRenderer' => '\Neilime\AssetsBundle\View\Strategy\NoneStrategy'
        )
    ),
    'view_manager' => array('strategies' => array('ViewJsCustomStrategy'))
);