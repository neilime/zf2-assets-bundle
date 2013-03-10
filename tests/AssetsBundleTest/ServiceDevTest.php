<?php
namespace AssetsBundleTest;
class ServiceTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var array
	 */
	private $configuration = array(
		'asset_bundle' => array(
			'production' => false,
			'basePath' => '/',
			'cachePath' => '@zfRootPath/AssetsBundleTest/_files/cache',
			'assetsPath' => '@zfRootPath/AssetsBundleTest/_files/assets',
			'assets' => array(
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
	 * @var \AssetsBundle\Service\Service
	 */
	private $service;

	private $routeMatch;

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

        $this->routeMatch = new \Zend\Mvc\Router\RouteMatch(array('controller' => 'index','action' => 'test-media'));

        //Define service
		$oServiceFactory = new \AssetsBundle\Factory\ServiceFactory();
        $this->service = $oServiceFactory->createService($oServiceManager)
        ->setRenderer(new \Zend\View\Renderer\PhpRenderer())
        ->setControllerName($this->routeMatch->getParam('controller'))
        ->setActionName($this->routeMatch->getParam('action'));
    }

	public function testRenderAssetsWithMedias(){
		$sCacheExpectedPath = __DIR__.'/_files/dev-cache-expected';

		$aCssFiles = array(
			'css_test-media.css',
			'css_test.css',
			'dev_ebcddd147f42ba536510ab2d0f1a5069.css'
		);

		$aLessFiles = array(
			'dev_ebcddd147f42ba536510ab2d0f1a5069.less'
		);

		$aJsFiles = array(
			'js_test.js'
		);

		//Empty cache directory
		$this->emptyCacheDirectory();

		//Render assets
		$this->assertInstanceOf('AssetsBundle\Service\Service',$this->service->renderAssets());

		//Css cache files
		foreach($aCssFiles as $sCssFile){
			$this->assertFileExists($this->service->getCachePath().$sCssFile);
			$this->assertEquals(
				file_get_contents($this->service->getCachePath().$sCssFile),
				file_get_contents($sCacheExpectedPath.'/'.$sCssFile)
			);
		}

		//Less cache files
		foreach($aLessFiles as $sLessFile){
			$this->assertFileExists($this->service->getCachePath().'/'.$sLessFile);
			$this->assertEquals(
				file_get_contents($this->service->getCachePath().'/'.$sLessFile),
				file_get_contents($sCacheExpectedPath.'/'.$sLessFile)
			);
		}

		//Js cache files
		foreach($aJsFiles as $sJsFile){
			$this->assertFileExists($this->service->getCachePath().'/'.$sJsFile);
			$this->assertEquals(
				file_get_contents($this->service->getCachePath().'/'.$sJsFile),
				file_get_contents($sCacheExpectedPath.'/'.$sJsFile)
			);
		}

		//Empty cache directory
		$this->emptyCacheDirectory();
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