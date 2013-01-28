<?php
namespace AssetsBundleTest;
class ServiceTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var array
	 */
	private $configuration = array(
		'asset_bundle' => array(
			'basePath' => '/',
			'cachePath' => '@zfRootPath/AssetsBundleTest/_files/cache',
			'assetsPath' => '@zfRootPath/AssetsBundleTest/_files/assets',
			'assets' => array(
				'test' => array(
					'css' => array('css/test.css'),
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
						)
					)
				)
			)

		)
	);

	/**
	 * @var \AssetsBundle\Service\Service
	 */
	private $service;

	private $routeMatch;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
    protected function setUp(){
        $oServiceManager = \AssetsBundleTest\Bootstrap::getServiceManager();

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($oServiceManager->get('Config'),$this->configuration);
        $bAllowOverride = $oServiceManager->getAllowOverride();
        if(!$bAllowOverride)$oServiceManager->setAllowOverride(true);
        $oServiceManager->setService('Config',$this->configuration)->setAllowOverride($bAllowOverride);

        //Define service
        $this->service = $oServiceManager->get('AssetsBundleService');
        $this->service->setRenderer(new \Zend\View\Renderer\PhpRenderer())->setLoadedModules(array('Test'));
        $this->routeMatch = new \Zend\Mvc\Router\RouteMatch(array('controller' => 'index','action' => 'index'));
    }

    public function testService(){
    	//Test service instance
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service);

    	//Test cache path
    	$this->assertEquals(realpath(__DIR__.'/_files/cache'), $this->service->getCachePath(), $actualClassOrObject);
    }

    public function testSetRoute(){
    	//Controller
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->setControllerName($this->routeMatch->getParam('controller')));
    	$this->assertEquals('index', $this->service->getControllerName());

    	//Action
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->setActionName($this->routeMatch->getParam('action')));
    	$this->assertEquals('index', $this->service->getActionName());
    }

    public function testRenderSimpleAssets(){
		$sCacheExpectedPath = __DIR__.'/_files/cache-expected';

		$sCssFile = '6784e1c334dfceb8f017667c0b0f6a3e.css';
		$sLessFile = '6784e1c334dfceb8f017667c0b0f6a3e.less';
		$sJsFile = '6784e1c334dfceb8f017667c0b0f6a3e.less';

    	//Empty cache directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->service->getCachePath(), \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Css cache file
		$this->assertFileExists($this->service->getCachePath().'/'.$sCssFile);
		$this->assertEquals(
			file_get_contents($this->service->getCachePath().'/'.$sCssFile),
			file_get_contents($sCacheExpectedPath.'/'.$sCssFile)
		);

		//Less cache file
		$this->assertFileExists($this->service->getCachePath().'/'.$sLessFile);
		$this->assertEquals(
			file_get_contents($this->service->getCachePath().'/'.$sLessFile),
			file_get_contents($sCacheExpectedPath.'/'.$sLessFile)
		);

		//Js cache file
		$this->assertFileExists($this->service->getCachePath().'/'.$sJsFile);
		$this->assertEquals(
			file_get_contents($this->service->getCachePath().'/'.$sJsFile),
			file_get_contents($sCacheExpectedPath.'/'.$sJsFile)
		);
    }

	public function testRenderAssetsWithMedias(){
		$sCacheExpectedPath = __DIR__.'/_files/cache-expected';

		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->setActionName('test-media'));
		$this->assertEquals('test-media', $this->service->getActionName());

		$sCssFile = 'ebcddd147f42ba536510ab2d0f1a5069.css';
		$sLessFile = 'ebcddd147f42ba536510ab2d0f1a5069.less';

    	//Empty cache directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->service->getCachePath(), \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Css cache file
		$this->assertFileExists($this->service->getCachePath().'/'.$sCssFile);
		$this->assertEquals(
			file_get_contents($this->service->getCachePath().'/'.$sCssFile),
			file_get_contents($sCacheExpectedPath.'/'.$sCssFile)
		);

		//Less cache file
		$this->assertFileExists($this->service->getCachePath().'/'.$sLessFile);
		$this->assertEquals(
			file_get_contents($this->service->getCachePath().'/'.$sLessFile),
			file_get_contents($sCacheExpectedPath.'/'.$sLessFile)
		);

		//Media cache files

		#Fonts
		$this->assertFileExists($this->service->getCachePath().'/AssetsBundleTest/_files/fonts/fontawesome-webfont.eot');
		$this->assertFileExists($this->service->getCachePath().'/AssetsBundleTest/_files/fonts/fontawesome-webfont.ttf');
		$this->assertFileExists($this->service->getCachePath().'/AssetsBundleTest/_files/fonts/fontawesome-webfont.woff');

		#Images
		$this->assertFileExists($this->service->getCachePath().'/AssetsBundleTest/_files/images/test-media.gif');
    }
}