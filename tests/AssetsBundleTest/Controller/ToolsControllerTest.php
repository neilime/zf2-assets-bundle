<?php
namespace AssetsBundleTest\Controller;
class ToolsControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase{
	/**
	 * @var array
	 */
	protected $originalConfiguration;

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
						)
					)
				)
			)
		)
	);

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
    public function setUp(){
        $this->setApplicationConfig(\AssetsBundleTest\Bootstrap::getConfig());
        parent::setUp();

        $oServiceLocator = $this->getApplicationServiceLocator();

        $aConfiguration = $this->originalConfiguration = $oServiceLocator->get('Config');
        unset($aConfiguration['asset_bundle']['assets']);

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration,$this->configuration);
        $bAllowOverride = $oServiceLocator->getAllowOverride();
        if(!$bAllowOverride)$oServiceLocator->setAllowOverride(true);
        $oServiceLocator->setService('Config',$this->configuration)->setAllowOverride($bAllowOverride);
    }

   	public function testRenderAssetsAction(){
    	$this->dispatch('render');
    	$this->assertResponseStatusCode(0);
    	$this->assertModuleName('AssetsBundle');
    	$this->assertControllerName('AssetsBundle\Controller\Tools');
    	$this->assertControllerClass('ToolsController');
    	$this->assertMatchedRouteName('render-assets');

    	$oAssetsBundleService = $this->getApplicationServiceLocator()->get('AssetsBundleService');

    	//Test service instance
    	$this->assertInstanceOf('AssetsBundle\Service\Service',$oAssetsBundleService);

    	$sCacheExpectedPath = dirname(__DIR__).'/_files/prod-cache-expected';

    	//Test cache files
    	foreach(array(
    		'test-module\index-controller-no_action' => $oAssetsBundleService->getCacheFileName('test-module','test-module\index-controller',\AssetsBundle\Service\ServiceOptions::NO_ACTION),
    		'test-module\index-controller-test-media' => $oAssetsBundleService->getCacheFileName('test-module','test-module\index-controller','test-media'),
    		'test-module\index-controller-test-mixins' => $oAssetsBundleService->getCacheFileName('test-module','test-module\index-controller','test-mixins'),
    		'no_controller-no_action' => $oAssetsBundleService->getCacheFileName(\AssetsBundle\Service\ServiceOptions::NO_MODULE,\AssetsBundle\Service\ServiceOptions::NO_CONTROLLER,\AssetsBundle\Service\ServiceOptions::NO_ACTION),
    	) as $sCacheFile){

    		//Css cache files
    		$this->assertStringEqualsFile(
    			$sCacheExpectedPath.DIRECTORY_SEPARATOR.$sCacheFile.'.css',
    			str_replace(PHP_EOL,"\n",file_get_contents($oAssetsBundleService->getOptions()->getCachePath().$sCacheFile.'.css'))
    		);

    		//Less cache files
    		$this->assertStringEqualsFile(
    			$sCacheExpectedPath.DIRECTORY_SEPARATOR.$sCacheFile.'.less',
    			str_replace(PHP_EOL,"\n",file_get_contents($oAssetsBundleService->getOptions()->getCachePath().$sCacheFile.'.less'))
    		);

    		//Js cache files
    		$this->assertStringEqualsFile(
    			$sCacheExpectedPath.DIRECTORY_SEPARATOR.$sCacheFile.'.js',
    			str_replace(PHP_EOL,"\n",file_get_contents($oAssetsBundleService->getOptions()->getCachePath().$sCacheFile.'.js'))
    		);
    	}
    }

    public function testRenderAssetsWithoutConfiguration(){
    	$oServiceLocator = $this->getApplicationServiceLocator();

        $aConfiguration = $oServiceLocator->get('Config');
        unset($aConfiguration['asset_bundle']);

        $bAllowOverride = $oServiceLocator->getAllowOverride();
        if(!$bAllowOverride)$oServiceLocator->setAllowOverride(true);
        $oServiceLocator->setService('Config',$aConfiguration)->setAllowOverride($bAllowOverride);

    	$this->dispatch('render');
    	$this->assertResponseStatusCode(1);
    	$this->assertModuleName('AssetsBundle');
    	$this->assertControllerName('AssetsBundle\Controller\Tools');
    	$this->assertControllerClass('ToolsController');
    	$this->assertMatchedRouteName('render-assets');
    }

    public function testRenderAssetsWithWrongConfiguration(){
    	$oServiceLocator = $this->getApplicationServiceLocator();

    	$aConfiguration = $oServiceLocator->get('Config');
    	$aConfiguration['asset_bundle']['cache_path'] = 'wrong';

    	$bAllowOverride = $oServiceLocator->getAllowOverride();
    	if(!$bAllowOverride)$oServiceLocator->setAllowOverride(true);
    	$oServiceLocator->setService('Config',$aConfiguration)->setAllowOverride($bAllowOverride);

    	$this->dispatch('render');
    	$this->assertResponseStatusCode(1);
    	$this->assertModuleName('AssetsBundle');
    	$this->assertControllerName('AssetsBundle\Controller\Tools');
    	$this->assertControllerClass('ToolsController');
    	$this->assertMatchedRouteName('render-assets');
    }


   	public function testEmptyCache(){
   		$this->dispatch('empty');
   		$this->assertResponseStatusCode(0);
   		$this->assertModuleName('AssetsBundle');
   		$this->assertControllerName('Assetsbundle\Controller\Tools');
   		$this->assertControllerClass('ToolsController');
   		$this->assertMatchedRouteName('empty-cache');

   		//Test cache directory has only .gitignore file
   		$aFiles = scandir(dirname(__DIR__).'/_files/cache');
   		$this->assertCount(3, $aFiles);
   		$this->assertContains('.gitignore', $aFiles);
    }

    public function tearDown(){
    	$oServiceLocator = $this->getApplicationServiceLocator();
    	$bAllowOverride = $oServiceLocator->getAllowOverride();
    	if(!$bAllowOverride)$oServiceLocator->setAllowOverride(true);
    	$oServiceLocator->setService('Config',$this->originalConfiguration)->setAllowOverride($bAllowOverride);
    }
}