<?php
namespace AssetsBundleTest\Controller;
class JsCustomControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase{
	/**
	 * @var array
	 */
	private $configuration = array(
		'asset_bundle' => array(
			'production' => true,
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
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
    public function setUp(){
        $this->setApplicationConfig(\AssetsBundleTest\Bootstrap::getConfig());
        parent::setUp();

        $oServiceLocator = $this->getApplicationServiceLocator();

        $aConfiguration = $oServiceLocator->get('Config');
        unset($aConfiguration['asset_bundle']['assets']);

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration,$this->configuration);
        $bAllowOverride = $oServiceLocator->getAllowOverride();
        if(!$bAllowOverride)$oServiceLocator->setAllowOverride(true);
        $oServiceLocator->setService('Config',$this->configuration)->setAllowOverride($bAllowOverride);
    }

   	public function testTestActionInProduction(){
    	$this->dispatch('/test');
    	$this->assertResponseStatusCode(200);
    	$this->assertModuleName('AssetsBundleTest');
    	$this->assertControllerName('AssetsBundleTest\Controller\Test');
    	$this->assertControllerClass('TestController');
    	$this->assertMatchedRouteName('test');
    }

    public function testTestActionInDevelopment(){
    	$oServiceLocator = $this->getApplicationServiceLocator();

    	$aConfiguration = $oServiceLocator->get('Config');
    	unset($aConfiguration['asset_bundle']['assets']);

    	$this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration,$this->configuration);
    	$this->configuration['asset_bundle']['production'] = false;
    	$bAllowOverride = $oServiceLocator->getAllowOverride();
    	if(!$bAllowOverride)$oServiceLocator->setAllowOverride(true);
    	$oServiceLocator->setService('Config',$this->configuration)->setAllowOverride($bAllowOverride);

    	$this->dispatch('/test');
    	$this->assertResponseStatusCode(200);
    	$this->assertModuleName('AssetsBundleTest');
    	$this->assertControllerName('AssetsBundleTest\Controller\Test');
    	$this->assertControllerClass('TestController');
    	$this->assertMatchedRouteName('test');
    }

    public function testFileErrorActionInDevelopment(){
    	$oServiceLocator = $this->getApplicationServiceLocator();

    	$aConfiguration = $oServiceLocator->get('Config');
    	unset($aConfiguration['asset_bundle']['assets']);

    	$this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration,$this->configuration);
    	$this->configuration['asset_bundle']['production'] = false;
    	$bAllowOverride = $oServiceLocator->getAllowOverride();
    	if(!$bAllowOverride)$oServiceLocator->setAllowOverride(true);
    	$oServiceLocator->setService('Config',$this->configuration)->setAllowOverride($bAllowOverride);

    	$this->dispatch('/file-error');
    	$this->assertResponseStatusCode(200);
    	$this->assertModuleName('AssetsBundleTest');
    	$this->assertControllerName('AssetsBundleTest\Controller\Test');
    	$this->assertControllerClass('TestController');
    	$this->assertMatchedRouteName('fileError');
    }

    public function testJsCustomAction(){
    	$this->dispatch('/jscustom/AssetsBundleTest\\Controller\\Test/test');
    	$this->assertResponseStatusCode(200);
    	$this->assertModuleName('AssetsBundleTest');
    	$this->assertControllerName('AssetsBundleTest\Controller\Test');
    	$this->assertControllerClass('TestController');
    	$this->assertMatchedRouteName('jscustom/definition');

    	$this->assertResponseHeaderContains('content-type','text/javascript');
    	$this->assertEquals(
    		file_get_contents(dirname(__DIR__).'/_files/prod-cache-expected/jscustom.js'),
    		$this->getResponse()->getContent()
    	);
    }
}