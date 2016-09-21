<?php

namespace AssetsBundleTest\Controller;

class ToolsControllerTest extends \Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase
{

    /**
     * @var array
     */
    protected $originalConfiguration;

    /**
     * @var array
     */
    protected $configuration = array(
        'assets_bundle' => array(
            'production' => true,
            'recursiveSearch' => true,
            'assets' => array(
                'css' => array(
                    'css/test.css',
                    'css/test.php'
                ),
                'less' => array('less/test.less'),
                'js' => array('js/test.js'),
                'test-module' => array(
                    'test-module\index-controller' => array(
                        'test-media' => array(
                            'css' => array('css/test-media.css'),
                            'less' => array('less/test-media.less'),
                            'media' => array(
                                '@zfRootPath/_files/fonts',
                                '@zfRootPath/_files/images'
                            )
                        ),
                        'test-mixins' => array(
                            'less' => array(
                                'less/test-mixins.less',
                                'less/test-mixins-use.less'
                            )
                        )
                    ),
                    'test-module\index-controller-with-assets' => array(
                        'css' => array('css/full-dir/full-dir.css'),
                    )
                ),
                'test-module-with-assets' => array(
                    'css' => array('css/full-dir/full-dir.css'),
                )
            )
        )
    );

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $this->setApplicationConfig(\AssetsBundleTest\Bootstrap::getConfig());
        parent::setUp();
        
        // Create tmp directory
        if (!is_dir($sTmpDirectory = dirname(__DIR__) . '/../_files/tmp')) {
            mkdir($sTmpDirectory);
        }

        //Retrieve service locator
        $oServiceLocator = $this->getApplicationServiceLocator()->setAllowOverride(true);

        //Store original configuration
        $aConfiguration = $this->originalConfiguration = $oServiceLocator->get('Config');

        //Override configuration
        unset($aConfiguration['assets_bundle']['assets']);
        $oServiceLocator->setService('Config', $this->configuration = \Zend\Stdlib\ArrayUtils::merge($aConfiguration, $this->configuration));

        //Rebuild AssetsBundle service options
        $oServiceLocator->setService('AssetsBundleServiceOptions', $oServiceLocator->create('AssetsBundleServiceOptions'));

        //Retrieve event manager
        $oEventManager = $this->getApplication()->getEventManager();

        //Remove AssetsBundle service events
        $oServiceLocator->get('AssetsBundleService')->detach($oEventManager);

        //Rebuild AssetsBundle service
        $oServiceLocator->setService('AssetsBundleService', $oServiceLocator->create('AssetsBundleService')->attach($oEventManager));

        // Empty cache and processed directories
        \AssetsBundleTest\Bootstrap::getServiceManager()->get('AssetsBundleToolsService')->emptyCache(false);
        
        
    }

    public function testRenderAssetsInProductionAction()
    {
        // Retrieve service locator
        $oServiceLocator = $this->getApplicationServiceLocator();

        $this->dispatch('render');
        $this->assertResponseStatusCode(0);
        $this->assertModuleName('AssetsBundle');
        $this->assertControllerName('AssetsBundle\Controller\Tools');
        $this->assertControllerClass('ToolsController');
        $this->assertMatchedRouteName('render-assets');

        // Retrieve AssetsBundle service
        $oAssetsBundleService = $oServiceLocator->get('AssetsBundleService');
        /* @var $oAssetsBundleService \AssetsBundle\Service\Service */
        // Test service instance
        $this->assertInstanceOf('AssetsBundle\Service\Service', $oAssetsBundleService);
        $sCacheExpectedPath = dirname(__DIR__) . '/../_files/expected/cache/prod';

        // Retrieve options
        $oOptions = $oAssetsBundleService->getOptions();

        $aCachedFiles = array(
            // "css/test.css", "css/test.php", "css/full-dir/full-dir.css" | "less/test.less" | "js/test.js"
            'test-module-index-controller-test-media' => $oOptions->setModuleName('test-module')->setControllerName('test-module\index-controller')->setActionName('test-media')->getCacheFileName(),
            //"css/test.css", "css/test.php", "css/full-dir/full-dir.css" | "less/test.less" | "js/test.js"
            'test-module-index-controller-test-mixins' => $oOptions->setModuleName('test-module')->setControllerName('test-module\index-controller')->setActionName('test-mixins')->getCacheFileName(),
            // "css/test.css", "css/test.php", "css/test-media.css" | "less/test.less", "less/test-media.less" | "js/test.js"
            'test-module-index-controller-with-assets-no_action' => $oOptions->setModuleName('test-module')->setControllerName('test-module\index-controller-with-assets')->setActionName(\AssetsBundle\Service\ServiceOptions::NO_ACTION)->getCacheFileName(),
            // "css/test.css", "css/test.php", "css/full-dir/full-dir.css" | "less/test.less" | "js/test.js"
            'test-module-with-assets-no_controller-no_action' => $oOptions->setModuleName('test-module-with-assets')->setControllerName(\AssetsBundle\Service\ServiceOptions::NO_CONTROLLER)->setActionName(\AssetsBundle\Service\ServiceOptions::NO_ACTION)->getCacheFileName(),
            // "css/test.css", "css/test.php" | "less/test.less" | "js/test.js"
            'no_module-no_controller-no_action' => $oOptions->setModuleName(\AssetsBundle\Service\ServiceOptions::NO_MODULE)->setControllerName(\AssetsBundle\Service\ServiceOptions::NO_CONTROLLER)->setActionName(\AssetsBundle\Service\ServiceOptions::NO_ACTION)->getCacheFileName(),
        );
        // Test cached files
        foreach ($aCachedFiles as $sCachePart => $sCacheFile) {
            // Css cached files
            $sCssCacheExpectedPath = $sCacheExpectedPath . DIRECTORY_SEPARATOR . $sCachePart . '.css';
            $sCssFilePath = $oAssetsBundleService->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sCacheFile . '.css';

            $sCssFileContent = preg_replace(array(
                '/' . preg_quote(getcwd(), '/') . '/',
                '/cache\/([0-9a-f]{32})\//',
                '/\?[0-9]+/',
                    ), array('/current/directory', 'cache/encrypted-file-tree/', '?timestamp'), file_get_contents($sCssFilePath));
            $sCssFilename = $sCachePart . ' - ' . $sCacheFile . '.css';
            $this->assertStringEqualsFile($sCssCacheExpectedPath, $sCssFileContent, $sCssFilename);

            // Js cache files
            $sJsCacheExpectedPath = $sCacheExpectedPath . DIRECTORY_SEPARATOR . $sCachePart . '.js';
            $sJsFilePath = $oAssetsBundleService->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sCacheFile . '.js';
            $sJsFilename = $sCachePart . ' - ' . $sCacheFile . '.js';
            $this->assertFileEquals($sJsCacheExpectedPath, $sJsFilePath, $sJsFilename);
        }
        
        // Test that tmp directory is empty
        $this->assertCount(0, array_diff(array('.','..'), scandir(dirname(__DIR__) . '/../_files/tmp')));
    }
    
    public function testRenderAssetsInDevelopmentAction()
    {
        // Retrieve service locator
        $oServiceLocator = $this->getApplicationServiceLocator();
        
         // Retrieve AssetsBundle service
        $oAssetsBundleService = $oServiceLocator->get('AssetsBundleService');
         // Retrieve options
        $oOptions = $oAssetsBundleService->getOptions();
        $oOptions->setProduction(false);

        $this->dispatch('render');
        $this->assertResponseStatusCode(0);
        $this->assertModuleName('AssetsBundle');
        $this->assertControllerName('AssetsBundle\Controller\Tools');
        $this->assertControllerClass('ToolsController');
        $this->assertMatchedRouteName('render-assets');       
        
        // Test that tmp directory is empty
        $this->assertCount(0, array_diff(array('.','..'), scandir(dirname(__DIR__) . '/../_files/tmp')));
    }

    public function testEmptyCache()
    {
        $this->dispatch('empty');
        $this->assertResponseStatusCode(0);
        $this->assertModuleName('AssetsBundle');
        $this->assertControllerName('Assetsbundle\Controller\Tools');
        $this->assertControllerClass('ToolsController');
        $this->assertMatchedRouteName('empty-cache');

        //Test cache directory has only .gitignore file
        $this->assertCount(3, $aCacheFiles = scandir(dirname(__DIR__) . '/../_files/cache'));
        $this->assertContains('.gitignore', $aCacheFiles);

        //Test less directory has only .gitignore file
        $this->assertCount(3, $aLessFiles = scandir(dirname(__DIR__) . '/../_files/processed/lessc'));
        $this->assertContains('.gitignore', $aLessFiles);

        //Test config directory has only .gitignore file
        $this->assertCount(3, $aConfigFiles = scandir(dirname(__DIR__) . '/../_files/processed/config'));
        $this->assertContains('.gitignore', $aConfigFiles);
    }

    public function tearDown()
    {
        $oServiceLocator = $this->getApplicationServiceLocator();
        $bAllowOverride = $oServiceLocator->getAllowOverride();
        if (!$bAllowOverride) {
            $oServiceLocator->setAllowOverride(true);
        }
        $oServiceLocator->setService('Config', $this->originalConfiguration)->setAllowOverride($bAllowOverride);

        // Empty cache and processed directories
        \AssetsBundleTest\Bootstrap::getServiceManager()->get('AssetsBundleToolsService')->emptyCache(false);
    }
}
