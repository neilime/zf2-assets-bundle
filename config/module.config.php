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
            'CssMinifier' => __NAMESPACE__.'\Service\Minifier\CssMinifier',
        	'JsMinifier' => __NAMESPACE__.'\Service\Minifier\JsMinifier',
        	'AssetsBundleService' => function(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
            	$aConfiguration = $oServiceLocator->get('Configuration');
            	$oService = new \Neilime\AssetsBundle\Service\Service($aConfiguration['asset_bundle']);
            	return $oService->setMinifiers(array(
            		\Neilime\AssetsBundle\Service\Service::ASSET_CSS => $oServiceLocator->get('CssMinifier'),
            		\Neilime\AssetsBundle\Service\Service::ASSET_JS => $oServiceLocator->get('JsMinifier')
            	));
            },
            'ViewJsCustomStrategy' => function(){
            	return new \Neilime\AssetsBundle\View\Strategy\JsCustomStrategy(new \Neilime\AssetsBundle\View\Renderer\JsRenderer());
            }
        ),
        'invoqua'
    ),
    'asset_bundle' => array(
		'production' => true,//Define here environnemt (Developpement set false)
    	'cachePath' => '/public/assets/cache/',//absolute path to the cache directory
    	'webCachePath' => '@zfBaseUrl/cache/',//cache directory base url
    	'baseUrl' => '@zfBaseUrl',
    	'imgExt' => array('png','gif','cur'),//Put here all image extensions to be cached
    	'rendererToStrategy' => array(
            'Zend\View\Renderer\PhpRenderer'  => 'AssetsBundle\View\Strategy\ViewHelperStrategy',
            'Zend\View\Renderer\FeedRenderer' => 'AssetsBundle\View\Strategy\NoneStrategy',
            'Zend\View\Renderer\JsonRenderer' => 'AssetsBundle\View\Strategy\NoneStrategy'
        )
    ),
    'view_manager' => array('strategies' => array('ViewJsCustomStrategy'))
);
