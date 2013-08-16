<?php
namespace AssetsBundleTest\Service;
class ServiceProdTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var array
	 */
	private $configuration = array(
		'asset_bundle' => array(
			'production' => true,
			'recursiveSearch' => true,
			'assets' => array(
				'css' => array(
					'css/test.css',
					'css/css.php'
				),
				'less' => array('less/test.less'),
				'js' => array('js/test.js'),
				'test-module' => array(
					'test-module\index-controller' => array(
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
						),
						'test-assets-from-url' => array(
							'js' => array(
								'https://raw.github.com/neilime/zf2-assets-bundle/master/tests/AssetsBundleTest/_files/assets/js/mootools.js'
							),
							'css' => array(
								'https://raw.github.com/neilime/zf2-assets-bundle/master/tests/AssetsBundleTest/_files/assets/css/bootstrap.css'
							)
						),
						'test-huge-assets' => array(
							'less' => array(
								'less/bootstrap.less'
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

	/**
	 * @var \Zend\Mvc\Router\RouteMatch
	 */
	private $routeMatch;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
    protected function setUp(){
       $oServiceManager = \AssetsBundleTest\Bootstrap::getServiceManager();

    	$aConfiguration = $oServiceManager->get('Config');
    	unset($aConfiguration['asset_bundle']['assets']);

    	$bAllowOverride = $oServiceManager->getAllowOverride();
    	if(!$bAllowOverride)$oServiceManager->setAllowOverride(true);

    	$oServiceManager->setService('Config',\Zend\Stdlib\ArrayUtils::merge($aConfiguration,$this->configuration))->setAllowOverride($bAllowOverride);

    	//Define service
    	$oServiceFactory = new \AssetsBundle\Factory\ServiceFactory();
    	$this->routeMatch = new \Zend\Mvc\Router\RouteMatch(array('controller' => 'test-module\index-controller','action' => 'index'));
    	$this->service = $oServiceFactory->createService($oServiceManager);
    	$this->service->getOptions()->setRenderer(new \Zend\View\Renderer\PhpRenderer())
    	->setModuleName(current(explode('\\',$this->routeMatch->getParam('controller'))))
    	->setControllerName($this->routeMatch->getParam('controller'))
    	->setActionName($this->routeMatch->getParam('action'));
    }

   	public function testService(){
   		//Test service instance
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service);

    	//Test cache path
    	$this->assertEquals(realpath(__DIR__.'/../_files/cache').DIRECTORY_SEPARATOR, $this->service->getOptions()->getCachePath());

    	//Test assets configuration
    	$this->assertTrue($this->service->moduleHasAssetConfiguration('test-module'));
    	$this->assertFalse($this->service->moduleHasAssetConfiguration('wrong-module'));

    	$this->assertTrue($this->service->controllerHasAssetConfiguration('test-module\index-controller'));
    	$this->assertFalse($this->service->controllerHasAssetConfiguration('wrong-controller'));

    	$this->assertTrue($this->service->actionHasAssetConfiguration('test-media'));
    	$this->assertFalse($this->service->actionHasAssetConfiguration('wrong-action'));
    }

    public function testSetRoute(){
    	//Module
    	$this->assertInstanceOf('AssetsBundle\Service\ServiceOptions',$this->service->getOptions()->setModuleName(current(explode('\\',$this->routeMatch->getParam('controller')))));
    	$this->assertEquals('test-module', $this->service->getOptions()->getModuleName());

    	//Controller
    	$this->assertInstanceOf('AssetsBundle\Service\ServiceOptions',$this->service->getOptions()->setControllerName($this->routeMatch->getParam('controller')));
    	$this->assertEquals('test-module\index-controller', $this->service->getOptions()->getControllerName());

    	//Action
    	$this->assertInstanceOf('AssetsBundle\Service\ServiceOptions',$this->service->getOptions()->setActionName($this->routeMatch->getParam('action')));
    	$this->assertEquals('index', $this->service->getOptions()->getActionName());

    	//Cache file name
    	$this->assertEquals(
    		$this->service->getCacheFileName(),
    		md5(current(explode('\\',$this->routeMatch->getParam('controller'))).$this->routeMatch->getParam('controller').\AssetsBundle\Service\ServiceOptions::NO_ACTION)
    	);
    }

    public function testRenderSimpleAssets(){

		//Cache file name
		$this->assertEquals(
			$sCacheName = $this->service->getCacheFileName(),
			md5(current(explode('\\',$this->routeMatch->getParam('controller'))).$this->routeMatch->getParam('controller').\AssetsBundle\Service\ServiceOptions::NO_ACTION)
    	);

		$sCssFile = $sCacheName.'.css';
		$sLessFile = $sCacheName.'.less';
		$sJsFile = $sCacheName.'.js';

		//Empty cache directory
		$this->emptyCacheDirectory();

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check assets content
		$this->assertAssetCacheContent(array($sCssFile,$sLessFile,$sJsFile));

		//Retrieve assets last modified date
		$this->assertNotEquals($iLastModified = filemtime($this->service->getOptions()->getCachePath().$sCssFile),false);

		sleep(1);

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets are not rendered again
		$this->assertEquals($iLastModified,filemtime($this->service->getOptions()->getCachePath().$sCssFile));

		//Remove js cache file
		unlink($this->service->getOptions()->getCachePath().$sCssFile);

		sleep(1);

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets has been rendered this time
		$this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath().$sCssFile),false);
		$this->assertGreaterThan($iLastModified,$iNewLastModified);

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets are not rendered again
		$this->assertEquals($iLastModified = $iNewLastModified,filemtime($this->service->getOptions()->getCachePath().$sCssFile));

		//Empty cache directory
		$this->emptyCacheDirectory();
    }

	public function testRenderAssetsWithMedias(){
		$this->assertInstanceOf('AssetsBundle\Service\ServiceOptions',$this->service->getOptions()->setActionName('test-media'));
		$this->assertEquals('test-media', $this->service->getOptions()->getActionName());

		//Test Cache file name
		$this->assertEquals($this->service->getCacheFileName(), md5(current(explode('\\',$this->routeMatch->getParam('controller'))).$this->routeMatch->getParam('controller').'test-media'));

		$sCacheName = $this->service->getCacheFileName();

		$sCssFile = $sCacheName.'.css';
		$sLessFile = $sCacheName.'.less';
		$sJsFile = $sCacheName.'.js';

		//Empty cache directory
		$this->emptyCacheDirectory();

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check assets content
		$this->assertAssetCacheContent(array($sCssFile,$sLessFile,$sJsFile));

		//Media cache files

		#Fonts
		$this->assertFileExists($this->service->getOptions()->getCachePath().'/AssetsBundleTest/_files/fonts/fontawesome-webfont.eot');
		$this->assertFileExists($this->service->getOptions()->getCachePath().'/AssetsBundleTest/_files/fonts/fontawesome-webfont.ttf');
		$this->assertFileExists($this->service->getOptions()->getCachePath().'/AssetsBundleTest/_files/fonts/fontawesome-webfont.woff');

		#Images
		$this->assertFileExists($this->service->getOptions()->getCachePath().'/AssetsBundleTest/_files/images/test-media.gif');
		$this->assertFileExists($this->service->getOptions()->getCachePath().'/AssetsBundleTest/_files/images/test-media.png');

		#Subfolders
		$this->assertFileExists($this->service->getOptions()->getCachePath().'/AssetsBundleTest/_files/images//subfolder/test-sub-media.jpg');

		//Check optimisation

		//Gd2 compression
		if(function_exists('imagecreatefromstring')){
			//Sizes
			$this->assertGreaterThan(filesize($this->service->getOptions()->getCachePath().'/AssetsBundleTest/_files/images/test-media.png'),filesize(__DIR__.'/../_files/images/test-media.png'));
			$this->assertGreaterThan(filesize($this->service->getOptions()->getCachePath().'/AssetsBundleTest/_files/images/test-media.jpg'),filesize(__DIR__.'/../_files/images/test-media.jpg'));
			$this->assertGreaterThan(filesize($this->service->getOptions()->getCachePath().'/AssetsBundleTest/_files/images/test-media.gif'),filesize(__DIR__.'/../_files/images/test-media.gif'));
		}

		//Empty cache directory
		$this->emptyCacheDirectory();
    }

    public function testRenderMixins(){
    	$this->assertInstanceOf('AssetsBundle\Service\ServiceOptions',$this->service->getOptions()->setActionName('test-mixins'));
    	$this->assertEquals('test-mixins', $this->service->getOptions()->getActionName());

    	//Test Cache file name
    	$this->assertEquals($this->service->getCacheFileName(), md5(current(explode('\\',$this->routeMatch->getParam('controller'))).$this->routeMatch->getParam('controller').$this->service->getOptions()->getActionName()));

    	//Empty cache directory
    	$this->emptyCacheDirectory();

    	//Render assets
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

    	//Check assets content
    	$this->assertAssetCacheContent(array($this->service->getCacheFileName().'.less'));

    	//Empty cache directory
    	$this->emptyCacheDirectory();
    }

    public function testRenderTestAssetsFromUrl(){
    	$this->assertInstanceOf('AssetsBundle\Service\ServiceOptions',$this->service->getOptions()->setActionName('test-assets-from-url'));
    	$this->assertEquals('test-assets-from-url', $this->service->getOptions()->getActionName());

    	//Test Cache file name
    	$this->assertEquals(
    		$this->service->getCacheFileName(),
    		md5(current(explode('\\',$this->routeMatch->getParam('controller'))).$this->routeMatch->getParam('controller').$this->service->getOptions()->getActionName())
    	);

    	//Empty cache directory
    	$this->emptyCacheDirectory();

    	if($this->assertTrue(extension_loaded('openssl')));

    	//Test external files contents
    	$this->assertStringEqualsFile(
    		__DIR__.'/../_files/prod-cache-expected/mootools.js',
    		str_replace(PHP_EOL,"\n",file_get_contents('https://raw.github.com/neilime/zf2-assets-bundle/master/tests/AssetsBundleTest/_files/assets/js/mootools.js'))
    	);

    	$this->assertStringEqualsFile(
    		__DIR__.'/../_files/prod-cache-expected/bootstrap.css',
    		str_replace(PHP_EOL,"\n",file_get_contents('https://raw.github.com/neilime/zf2-assets-bundle/master/tests/AssetsBundleTest/_files/assets/css/bootstrap.css'))
    	);

    	//Render assets
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

    	//Check assets content
    	$this->assertAssetCacheContent(array($this->service->getCacheFileName().'.js'));
    	$this->assertAssetCacheContent(array($this->service->getCacheFileName().'.css'));

    	//Empty cache directory
    	$this->emptyCacheDirectory();
    }

    public function testRenderTestHugeAssets(){
    	$this->assertInstanceOf('AssetsBundle\Service\ServiceOptions',$this->service->getOptions()->setActionName('test-huge-assets'));
    	$this->assertEquals('test-huge-assets', $this->service->getOptions()->getActionName());

    	//Test Cache file name
    	$this->assertEquals(
    		$this->service->getCacheFileName(),
    		md5(current(explode('\\',$this->routeMatch->getParam('controller'))).$this->routeMatch->getParam('controller').$this->service->getOptions()->getActionName())
    	);

    	//Empty cache directory
    	$this->emptyCacheDirectory();

    	//Render assets
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

    	//Check assets content
    	$this->assertAssetCacheContent(array($this->service->getCacheFileName().'.less'));

    	//Empty cache directory
    	$this->emptyCacheDirectory();
    }

    /**
     * @param array $aAssetsFiles
     */
    protected function assertAssetCacheContent(array $aAssetsFiles){
    	$sCacheExpectedPath = __DIR__.'/../_files/prod-cache-expected';
    	foreach($aAssetsFiles as $sAssetFile){
    		file_put_contents($sCacheExpectedPath.DIRECTORY_SEPARATOR.$sAssetFile,
    			str_replace(PHP_EOL,"\n",file_get_contents($this->service->getOptions()->getCachePath().$sAssetFile)));
    		$this->assertStringEqualsFile(
    			$sCacheExpectedPath.DIRECTORY_SEPARATOR.$sAssetFile,
    			str_replace(PHP_EOL,"\n",file_get_contents($this->service->getOptions()->getCachePath().$sAssetFile))
    		);
    	}
    }

    protected function emptyCacheDirectory(){
    	//Empty cache directory except .gitignore
    	foreach(new \RecursiveIteratorIterator(
    		new \RecursiveDirectoryIterator($this->service->getOptions()->getCachePath(), \RecursiveDirectoryIterator::SKIP_DOTS),
    		\RecursiveIteratorIterator::CHILD_FIRST
    	) as $oFileinfo){
    		if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
    		elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
    	}
    }
}