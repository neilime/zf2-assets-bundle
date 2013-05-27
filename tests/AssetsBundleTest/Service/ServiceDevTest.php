<?php
namespace AssetsBundleTest\Service;
class ServiceTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var array
	 */
	private $configuration = array(
		'asset_bundle' => array(
			'production' => false,
			'assets' => array(
				'css' => array(
					'css/test.css',
					'css/full-dir'
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
					),
					'test-assets-from-url' => array(
						'js' => array(
							'https://raw.github.com/neilime/zf2-assets-bundle/master/tests/AssetsBundleTest/_files/assets/js/mootools.js'
						),
						'css' => array(
							'https://raw.github.com/neilime/zf2-assets-bundle/master/tests/AssetsBundleTest/_files/assets/css/bootstrap.css'
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
        $this->routeMatch = new \Zend\Mvc\Router\RouteMatch(array('controller' => 'index','action' => 'index'));
        $this->createService();
    }

    /**
     * @param boolean $bAddNewLess
     */
    protected function createService(array $aAssetsConfiguration = null){
    	$oServiceManager = \AssetsBundleTest\Bootstrap::getServiceManager();

    	$aConfiguration = $oServiceManager->get('Config');
    	unset($aConfiguration['asset_bundle']['assets']);

    	$bAllowOverride = $oServiceManager->getAllowOverride();
    	if(!$bAllowOverride)$oServiceManager->setAllowOverride(true);

    	$aNewConfig = \Zend\Stdlib\ArrayUtils::merge($aConfiguration,$this->configuration);
    	if($aAssetsConfiguration)$aNewConfig['asset_bundle']['assets'] = $aAssetsConfiguration;
    	$oServiceManager->setService('Config',$aNewConfig)->setAllowOverride($bAllowOverride);

    	//Define service
    	$oServiceFactory = new \AssetsBundle\Factory\ServiceFactory();
    	$this->service = $oServiceFactory->createService($oServiceManager)
    		->setRenderer(new \Zend\View\Renderer\PhpRenderer())
    		->setControllerName($this->routeMatch->getParam('controller'))
    		->setActionName($this->routeMatch->getParam('action'));
    }

    public function testRenderSimpleAssets(){
		//Empty cache directory
		$this->emptyCacheDirectory();

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Css cache files
		$this->assertAssetCacheContent(array(
			'css_test.css',
			'dev_2ccf05a7ef21d1b5cf2cf0ab67167023.css',
			'css_full-dir_full-dir.css'
		));

		//Less cache files
		$sLessFile = 'dev_2ccf05a7ef21d1b5cf2cf0ab67167023.less';
		$this->assertAssetCacheContent(array($sLessFile));

		//Js cache files
		$this->assertAssetCacheContent(array('js_test.js'));

		//Retrieve assets last modified date
		$this->assertNotEquals($iLastModified = filemtime($this->service->getCachePath().$sLessFile),false);

		sleep(1);

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets are not rendered again
		$this->assertNotEquals($iNewLastModified = filemtime($this->service->getCachePath().$sLessFile),false);
		$this->assertEquals($iLastModified,$iNewLastModified);

		sleep(1);

		//Change configuration
		$aAssetsConfiguration = $this->configuration['asset_bundle']['assets'];
		$aAssetsConfiguration['less'][] = 'less/new.less';
		$this->createService($aAssetsConfiguration);

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets has been rendered this time
		$iLastModified = $iNewLastModified;
		$this->assertNotEquals($iNewLastModified = filemtime($this->service->getCachePath().$sLessFile),false);
		$this->assertGreaterThan($iLastModified,$iNewLastModified);

		sleep(1);

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets are not rendered again
		$iLastModified = $iNewLastModified;
		$this->assertNotEquals($iNewLastModified = filemtime($this->service->getCachePath().$sLessFile),false);
		$this->assertEquals($iLastModified,$iNewLastModified);

		//Update less assets
		file_put_contents($this->service->getAssetsPath().'less/test.less',file_get_contents($this->service->getAssetsPath().'less/test.less'));

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets has been rendered this time
		$this->assertNotEquals($iNewLastModified = filemtime($this->service->getCachePath().$sLessFile),false);
		$this->assertGreaterThan($iLastModified,$iNewLastModified);

		sleep(1);

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets are not rendered again
		$iLastModified = $iNewLastModified;
		$this->assertNotEquals($iNewLastModified = filemtime($this->service->getCachePath().$sLessFile),false);
		$this->assertEquals($iLastModified,$iNewLastModified);

		//Update less import assets
		file_put_contents($this->service->getAssetsPath().'less/import.less',file_get_contents($this->service->getAssetsPath().'less/import.less'));

		sleep(1);

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets are not rendered again
		$iLastModified = $iNewLastModified;
		$this->assertNotEquals($iNewLastModified = filemtime($this->service->getCachePath().$sLessFile),false);
		$this->assertGreaterThan($iLastModified,$iNewLastModified);

		sleep(1);

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Check if assets has been rendered this time
		$this->assertNotEquals($iNewLastModified = filemtime($this->service->getCachePath().$sLessFile),false);
		$this->assertGreaterThan($iLastModified,$iNewLastModified);

		//Empty cache directory
		$this->emptyCacheDirectory();
    }

	public function testRenderAssetsWithMedias(){
		//Change action name
		$this->routeMatch->setParam('action','test-media');
		$this->service->setActionName($this->routeMatch->getParam('action'));

		//Empty cache directory
		$this->emptyCacheDirectory();

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Css cache files
		$this->assertAssetCacheContent(array(
			'css_test-media.css',
			'css_test.css',
			'dev_ebcddd147f42ba536510ab2d0f1a5069.css',
			'css_full-dir_full-dir.css'
		));

		//Less cache files
		$this->assertAssetCacheContent(array('dev_ebcddd147f42ba536510ab2d0f1a5069.less'));

		//Js cache files
		$this->assertAssetCacheContent(array('js_test.js'));

		//Empty cache directory
		$this->emptyCacheDirectory();
    }

    public function testRenderTestAssetsFromUrl(){
    	//Change action name
    	$this->routeMatch->setParam('action','test-assets-from-url');
    	$this->service->setActionName($this->routeMatch->getParam('action'));

    	//Empty cache directory
    	$this->emptyCacheDirectory();

    	//Render assets
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

    	//Empty cache directory
    	$this->emptyCacheDirectory();
    }

   /**
     * @expectedException InvalidArgumentException
     */
    public function testGetFilterWithWrongAssetType(){
    	$this->service->getFilter('wrong');

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testHasFilterWithWrongAssetType(){
    	$this->service->hasFilter('wrong');
    }

    public function testRenderWithoutAssetsPath(){

    	//Change assets config
    	$this->createService(array('css' => array(
    		$this->service->getAssetsPath().'css/test.css'
    	)));

    	//Unset assets path config
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->setAssetsPath(null));

    	//Render assets
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());
    }

    /**
     * @param array $aAssetsFiles
     */
    protected function assertAssetCacheContent(array $aAssetsFiles){
    	$sCacheExpectedPath = __DIR__.'/../_files/dev-cache-expected';
    	foreach($aAssetsFiles as $sAssetFile){
    		$this->assertFileEquals(
	    		$sCacheExpectedPath.DIRECTORY_SEPARATOR.$sAssetFile,
	    		$this->service->getCachePath().$sAssetFile
	    	);
    	}
    }

    protected function emptyCacheDirectory(){
    	//Empty cache directory except .gitignore
    	foreach(new \RecursiveIteratorIterator(
    		new \RecursiveDirectoryIterator($this->service->getCachePath(), \RecursiveDirectoryIterator::SKIP_DOTS),
    		\RecursiveIteratorIterator::CHILD_FIRST
    	) as $oFileinfo){
    		if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
    		elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
    	}
    }
}