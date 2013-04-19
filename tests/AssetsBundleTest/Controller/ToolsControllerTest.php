<?php
namespace AssetsBundleTest\Controller;
use AssetsBundle\Controller\ToolsController;

class ToolsControllerTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var array
	 */
	private $configuration = array(
		'asset_bundle' => array(
			'production' => true,
			'basePath' => '/',
			'cachePath' => '@zfRootPath/AssetsBundleTest/_files/cache',
			'assetsPath' => '@zfRootPath/AssetsBundleTest/_files/assets',
			'assets' => array(
				'css' => array(
					'css/test.css',
					'css/css.php'
				),
				'less' => array('less/test.less'),
				'js' => array('js/test.js'),
				'index' => array(
					'test-media' => array(
						'css' => array('css/test-media.css'),
						'less' => array('less/test-media.less'),
						'media' => array(
							'@zfRootPath/AssetsBundleTest/_files/fonts',
							'@zfRootPath/AssetsBundleTest/_files/images'
						)
					),
					'test-mixins' => array(
						'less' => array(
							'less/test-mixins.less',
							'less/test-mixins-use.less'
						)
					)
				)
			)
		)
	);

	/**
	 * @var \AssetsBundle\Controller\ToolsController
	 */
	protected $controller;

	/**
	 * @var \Zend\Http\Request
	 */
	protected $request;

	/**
	 * @var \Zend\Mvc\Router\RouteMatch
	 */
	protected $routeMatch;

	/**
	 * @var \Zend\Mvc\MvcEvent
	 */
	protected $event;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
    protected function setUp(){
        $oServiceManager = \AssetsBundleTest\Bootstrap::getServiceManager();

        $aConfiguration = $oServiceManager->get('Config');
        unset($aConfiguration['asset_bundle']['assets']);

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration,$this->configuration);
        $bAllowOverride = $oServiceManager->getAllowOverride();
        if(!$bAllowOverride)$oServiceManager->setAllowOverride(true);
        $oServiceManager->setService('Config',$this->configuration)->setAllowOverride($bAllowOverride);

        $this->controller = new \AssetsBundle\Controller\ToolsController();
        $this->request = new \Zend\Http\Request();
        $this->routeMatch = new \Zend\Mvc\Router\RouteMatch(array('controller' => 'tools'));
        $this->event = new \Zend\Mvc\MvcEvent();
        $this->event
        	->setRouter(\Zend\Mvc\Router\Http\TreeRouteStack::factory(isset($this->configuration['router'])?$this->configuration['router']:array()))
        	->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($oServiceManager);
    }

   	public function testRenderAssets(){
    	$this->routeMatch->setParam('action', 'renderassets');
    	$this->controller->dispatch($this->request);
    	$this->assertEquals(200, $this->controller->getResponse()->getStatusCode());

    	$oAssetsBundleService = $this->controller->getServiceLocator()->get('AssetsBundleService');

    	//Test service instance
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$oAssetsBundleService);

    	$sCacheExpectedPath = dirname(__DIR__).'/_files/prod-cache-expected';

    	//Test cache files
    	foreach(array(
    		$oAssetsBundleService->getCacheFileName('index',\AssetsBundle\Service\Service::NO_ACTION),
    		$oAssetsBundleService->getCacheFileName('index','test-media'),
    		$oAssetsBundleService->getCacheFileName(\AssetsBundle\Service\Service::NO_CONTROLLER,\AssetsBundle\Service\Service::NO_ACTION),
    	) as $sCacheFile){

    		//Css cache files
    		$this->assertFileExists($oAssetsBundleService->getCachePath().$sCacheFile.'.css');
    		$this->assertEquals(
    			file_get_contents($sCacheExpectedPath.'/'.$sCacheFile.'.css'),
    			file_get_contents($oAssetsBundleService->getCachePath().$sCacheFile.'.css')
    		);

    		//Less cache files
    		$this->assertFileExists($oAssetsBundleService->getCachePath().$sCacheFile.'.less');

    		$this->assertEquals(
    			file_get_contents($sCacheExpectedPath.'/'.$sCacheFile.'.less'),
    			file_get_contents($oAssetsBundleService->getCachePath().$sCacheFile.'.less')
    		);

    		//Js cache files
    		$this->assertFileExists($oAssetsBundleService->getCachePath().$sCacheFile.'.js');
    		$this->assertEquals(
    			file_get_contents($sCacheExpectedPath.'/'.$sCacheFile.'.js'),
    			file_get_contents($oAssetsBundleService->getCachePath().$sCacheFile.'.js')
    		);
    	}
    }

   	public function testEmptyCache(){
    	$this->routeMatch->setParam('action', 'emptycache');
    	$this->controller->dispatch($this->request);
    	$this->assertEquals(200, $this->controller->getResponse()->getStatusCode());

    	//Test cache directory has only .gitignore file
		$aFiles = scandir(dirname(__DIR__).'/_files/cache');
		$this->assertCount(3, $aFiles);
		$this->assertContains('.gitignore', $aFiles);
    }
}