<?php

namespace AssetsBundleTest\Controller;

class JsCustomControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase {

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
                        )
                    )
                )
            )
        )
    );

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp() {
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
    }

    public function testTestActionInProduction() {
        $this->dispatch('/test');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('AssetsBundleTest');
        $this->assertControllerName('AssetsBundleTest\Controller\Test');
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('test');
        $this->assertEquals('/jscustom/AssetsBundleTest%5CController%5CTest/test', $this->getResponse()->getContent());
    }

    public function testTestActionInDevelopment() {
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

    public function testFileErrorActionInDevelopment() {
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

    public function testEmptyActionInDevelopment() {
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

    public function testJsCustomAction() {
        $this->dispatch('/jscustom/AssetsBundleTest%5CController%5CTest/test');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('AssetsBundleTest');
        $this->assertControllerName('AssetsBundleTest\Controller\Test');
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('jscustom/definition');

        $this->assertResponseHeaderContains('content-type', 'text/javascript');
        $this->assertStringEqualsFile(
                dirname(__DIR__) . '/../_files/prod-cache-expected/jscustom.js', str_replace(PHP_EOL, "\n", $this->getResponse()->getContent())
        );
    }

    public function testJsCustomActionWithException() {
        $this->dispatch('/jscustom/AssetsBundleTest%5CController%5CTest/exception');
        $this->assertResponseStatusCode(500);
        $this->assertModuleName('AssetsBundleTest');
        $this->assertControllerName('AssetsBundleTest\Controller\Test');
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('jscustom/definition');

        $this->assertResponseHeaderContains('content-type', 'text/javascript');
        $this->assertEquals('', $this->getResponse()->getContent());
    }

    protected function emptyCacheDirectory() {
        //Empty cache directory except .gitignore
        foreach (new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($this->getApplicationServiceLocator()->get('AssetsBundleService')->getOptions()->getCachePath(), \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        ) as $oFileinfo) {
            if ($oFileinfo->isDir()) {
                rmdir($oFileinfo->getRealPath());
            } elseif ($oFileinfo->getBasename() !== '.gitignore') {
                unlink($oFileinfo->getRealPath());
            }
        }
    }

    protected function emptyProcessedDirectory() {
        //Empty processed directory except .gitignore
        foreach (new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($this->getApplicationServiceLocator()->get('AssetsBundleService')->getOptions()->getProcessedDirPath() . DIRECTORY_SEPARATOR . 'lessc', \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        ) as $oFileinfo) {
            if ($oFileinfo->isDir()) {
                rmdir($oFileinfo->getRealPath());
            } elseif ($oFileinfo->getBasename() !== '.gitignore') {
                unlink($oFileinfo->getRealPath());
            }
        }
        foreach (new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($this->getApplicationServiceLocator()->get('AssetsBundleService')->getOptions()->getProcessedDirPath() . DIRECTORY_SEPARATOR . 'config', \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        ) as $oFileinfo) {
            if ($oFileinfo->isDir()) {
                rmdir($oFileinfo->getRealPath());
            } elseif ($oFileinfo->getBasename() !== '.gitignore') {
                unlink($oFileinfo->getRealPath());
            }
        }
    }

    /**
     * Assert response status code
     * @param int $code
     */
    public function assertResponseStatusCode($code) {
        if ($this->useConsoleRequest) {
            if (!in_array($code, array(0, 1))) {
                throw new PHPUnit_Framework_ExpectationFailedException(
                'Console status code assert value must be O (valid) or 1 (error)'
                );
            }
        }
        $match = $this->getResponseStatusCode();
        if ($code != $match) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                    'Failed asserting response code "%s", actual status code is "%s"', $code, $match
            ));
        }
        $this->assertEquals($code, $match, func_num_args() > 1 ? func_get_arg(1) : null);
    }

    public function tearDown() {
        //Empty cache directory
        $this->emptyCacheDirectory();
        //Empty processed directory
        $this->emptyProcessedDirectory();
        parent::tearDown();
    }

}
