<?php

namespace AssetsBundleTest\Controller;

class JsCustomControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase
{

    /**
     * @var array
     */
    private $configuration = array(
        'assets_bundle' => array(
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
                        ),
                    ),
                ),
            ),
        ),
    );

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $this->setApplicationConfig(\AssetsBundleTest\Bootstrap::getConfig());
        parent::setUp();

        $oServiceLocator = $this->getApplicationServiceLocator();

        $aConfiguration = $oServiceLocator->get('Config');
        unset($aConfiguration['assets_bundle']['assets']);

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration, $this->configuration);
        $bAllowOverride = $oServiceLocator->getAllowOverride();
        if (!$bAllowOverride) {
            $oServiceLocator->setAllowOverride(true);
        }
        $oServiceLocator->setService('Config', $this->configuration)->setAllowOverride($bAllowOverride);

        // Empty cache and processed directories
        $oServiceLocator->get('AssetsBundleToolsService')->emptyCache(false);
    }

    public function testTestActionInProduction()
    {
        $this->dispatch('/test');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('AssetsBundleTest');
        $this->assertControllerName('AssetsBundleTest\Controller\Test');
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('test');
        $this->assertEquals('/jscustom/AssetsBundleTest%5CController%5CTest/test', $this->getResponse()->getContent());
    }

    public function testTestActionInDevelopment()
    {
        $oServiceLocator = $this->getApplicationServiceLocator();

        $aConfiguration = $oServiceLocator->get('Config');
        unset($aConfiguration['assets_bundle']['assets']);

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration, $this->configuration);
        $this->configuration['assets_bundle']['production'] = false;
        $bAllowOverride = $oServiceLocator->getAllowOverride();
        if (!$bAllowOverride) {
            $oServiceLocator->setAllowOverride(true);
        }
        $oServiceLocator->setService('Config', $this->configuration)->setAllowOverride($bAllowOverride);

        //Retrieve assets bundle service
        $oAssetsBundleService = $this->getApplicationServiceLocator()->get('AssetsBundleService');
        $oAssetsBundleService->getOptions()->setProduction(false);
        $this->assertFalse($oAssetsBundleService->getOptions()->isProduction());

        $this->dispatch('/test');
        $this->assertResponseStatusCode(200, $this->getResponse()->getContent());
        $this->assertModuleName('AssetsBundleTest');
        $this->assertControllerName('AssetsBundleTest\Controller\Test');
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('test');

        $this->assertEquals(print_r(array(
            '/cache/_files/assets/js/jscustom.js',
            '/cache/_files/assets/js/jscustom.php'
                        ), true), preg_replace('/\?[0-9]*/', '', $this->getResponse()->getContent()));


        $this->assertFileExists($oAssetsBundleService->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . '_files/assets/js/jscustom.js');
        $this->assertFileExists($oAssetsBundleService->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . '_files/assets/js/jscustom.php');
    }

    public function testFileErrorActionInDevelopment()
    {
        $oServiceLocator = $this->getApplicationServiceLocator();

        $aConfiguration = $oServiceLocator->get('Config');
        unset($aConfiguration['assets_bundle']['assets']);

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration, $this->configuration);
        $this->configuration['assets_bundle']['production'] = false;
        $bAllowOverride = $oServiceLocator->getAllowOverride();
        if (!$bAllowOverride) {
            $oServiceLocator->setAllowOverride(true);
        }
        $oServiceLocator->setService('Config', $this->configuration)->setAllowOverride($bAllowOverride);

        $this->dispatch('/file-error');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('AssetsBundleTest');
        $this->assertControllerName('AssetsBundleTest\Controller\Test');
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('fileError');
    }

    public function testEmptyActionInDevelopment()
    {
        $oServiceLocator = $this->getApplicationServiceLocator();

        $aConfiguration = $oServiceLocator->get('Config');
        unset($aConfiguration['assets_bundle']['assets']);

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration, $this->configuration);
        $this->configuration['assets_bundle']['production'] = false;
        $bAllowOverride = $oServiceLocator->getAllowOverride();
        if (!$bAllowOverride) {
            $oServiceLocator->setAllowOverride(true);
        }
        $oServiceLocator->setService('Config', $this->configuration)->setAllowOverride($bAllowOverride);

        $this->dispatch('/empty');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('AssetsBundleTest');
        $this->assertControllerName('AssetsBundleTest\Controller\Test');
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('empty');
    }

    public function testJsCustomAction()
    {
        $this->dispatch('/jscustom/AssetsBundleTest%5CController%5CTest/test');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('AssetsBundleTest');
        $this->assertControllerName('AssetsBundleTest\Controller\Test');
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('jscustom/definition');

        $this->assertResponseHeaderContains('content-type', 'text/javascript');

        $sCacheExpectedPath = dirname(__DIR__) . '/../_files/expected/cache/prod';
        $this->assertStringEqualsFile($sCacheExpectedPath . '/jscustom.js', $this->getResponse()->getContent());
    }

    /**
     * Assert response status code
     * @param int $code
     */
    public function assertResponseStatusCode($code)
    {
        if ($this->useConsoleRequest) {
            if (!in_array($code, array(0, 1))) {
                throw new \PHPUnit_Framework_ExpectationFailedException(
                'Console status code assert value must be O (valid) or 1 (error)'
                );
            }
        }
        $match = $this->getResponseStatusCode();

        $sMessage = func_num_args() > 1 ? func_get_arg(1) : null;

        if ($code != $match) {
            throw new \PHPUnit_Framework_ExpectationFailedException(sprintf(
                    'Failed asserting response code "%s", actual status code is "%s"' . ($sMessage ? ' : ' . $sMessage : ''), $code, $match
            ));
        }
        $this->assertEquals($code, $match, $sMessage);
    }

    public function tearDown()
    {
        // Empty cache and processed directories
        \AssetsBundleTest\Bootstrap::getServiceManager()->get('AssetsBundleToolsService')->emptyCache(false);
        parent::tearDown();
    }
}
