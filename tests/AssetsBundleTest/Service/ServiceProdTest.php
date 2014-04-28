<?php

namespace AssetsBundleTest\Service;

class ServiceProdTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var array
     */
    private $configuration = array(
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
                                'img',
                                '@zfRootPath/_files/fonts',
                                '@zfRootPath/_files/images'
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
                                'less/bootstrap/less/bootstrap.less'
                            ),
                            'media' => array(
                                'less/bootstrap/fonts'
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
    protected function setUp() {
        $oServiceManager = \AssetsBundleTest\Bootstrap::getServiceManager();

        $aConfiguration = $oServiceManager->get('Config');
        unset($aConfiguration['assets_bundle']['assets']);

        $bAllowOverride = $oServiceManager->getAllowOverride();
        if (!$bAllowOverride) {
            $oServiceManager->setAllowOverride(true);
        }

        $oServiceManager->setService('Config', \Zend\Stdlib\ArrayUtils::merge($aConfiguration, $this->configuration))->setAllowOverride($bAllowOverride);

        //Rebuild AssetsBundle service options
        $oServiceOptionsFactory = new \AssetsBundle\Factory\ServiceOptionsFactory();
        $oServiceManager->setService('AssetsBundleServiceOptions', $oServiceOptionsFactory->createService($oServiceManager));

        //Define service
        $oServiceFactory = new \AssetsBundle\Factory\ServiceFactory();
        $this->routeMatch = new \Zend\Mvc\Router\RouteMatch(array('controller' => 'test-module\index-controller', 'action' => 'index'));
        $this->service = $oServiceFactory->createService($oServiceManager);
        $this->service->getOptions()->setRenderer(new \Zend\View\Renderer\PhpRenderer())
                ->setModuleName(current(explode('\\', $this->routeMatch->getParam('controller'))))
                ->setControllerName($this->routeMatch->getParam('controller'))
                ->setActionName($this->routeMatch->getParam('action'));
    }

    public function testService() {
        //Test service instance
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service);

        //Test cache path
        $this->assertEquals(realpath(dirname(__DIR__) . '/../_files/cache'), $this->service->getOptions()->getCachePath());

        //Test production
        $this->assertTrue($this->service->getOptions()->isProduction());
    }

    public function testSetRoute() {
        //Module
        $this->assertInstanceOf('AssetsBundle\Service\ServiceOptions', $this->service->getOptions()->setModuleName(current(explode('\\', $this->routeMatch->getParam('controller')))));
        $this->assertEquals('test-module', $this->service->getOptions()->getModuleName());

        //Controller
        $this->assertInstanceOf('AssetsBundle\Service\ServiceOptions', $this->service->getOptions()->setControllerName($this->routeMatch->getParam('controller')));
        $this->assertEquals('test-module\index-controller', $this->service->getOptions()->getControllerName());

        //Action
        $this->assertInstanceOf('AssetsBundle\Service\ServiceOptions', $this->service->getOptions()->setActionName($this->routeMatch->getParam('action')));
        $this->assertEquals('index', $this->service->getOptions()->getActionName());
    }

    public function testRenderSimpleAssets() {

        $sCacheName = $this->service->getOptions()->getCacheFileName();
        $sCssFile = $sCacheName . '.css';
        $sJsFile = $sCacheName . '.js';

        //Empty cache directory
        $this->emptyCacheDirectory();

        //Initialize MvcEvent
        $oMvcEvent = new \Zend\Mvc\MvcEvent();
        $oMvcEvent
                ->setApplication(\Zend\Mvc\Application::init(\AssetsBundleTest\Bootstrap::getConfig()))
                //Reset route match
                ->setRouteMatch(new \Zend\Mvc\Router\RouteMatch(array()))
                //Reset request
                ->setRequest(new \Zend\Http\Request());

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($oMvcEvent));

        //Check assets content
        $this->assertAssetCacheContent(array($sCssFile, $sJsFile));

        //Retrieve assets last modified date
        $this->assertNotEquals($iLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sCssFile), false);

        sleep(1);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($oMvcEvent));

        //Check if assets are not rendered again
        $this->assertEquals($iLastModified, filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sCssFile));

        //Remove js cache file
        unlink($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sCssFile);

        sleep(1);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($oMvcEvent));

        //Check if assets has been rendered this time
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sCssFile), false);
        $this->assertGreaterThan($iLastModified, $iNewLastModified);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($oMvcEvent));

        //Check if assets are not rendered again
        $this->assertEquals($iLastModified = $iNewLastModified, filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sCssFile));

        //Empty cache directory
        $this->emptyCacheDirectory();
    }

    public function testRenderAssetsWithMedias() {
        $this->assertInstanceOf('AssetsBundle\Service\ServiceOptions', $this->service->getOptions()->setActionName('test-media'));
        $this->assertEquals('test-media', $this->service->getOptions()->getActionName());

        $sCacheName = $this->service->getOptions()->getCacheFileName();

        $sCssFile = $sCacheName . '.css';
        $sJsFile = $sCacheName . '.js';

        //Empty cache directory
        $this->emptyCacheDirectory();

        //Initialize MvcEvent
        $oMvcEvent = new \Zend\Mvc\MvcEvent();
        $oMvcEvent
                ->setApplication(\Zend\Mvc\Application::init(\AssetsBundleTest\Bootstrap::getConfig()))
                //Reset route match
                ->setRouteMatch(new \Zend\Mvc\Router\RouteMatch(array()))
                //Reset request
                ->setRequest(new \Zend\Http\Request());

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($oMvcEvent));

        //Check assets content
        $this->assertAssetCacheContent(array($sCssFile, $sJsFile));

        //Media cache files
        #Fonts
        $this->assertFileExists($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/fonts')) . '/fontawesome-webfont.eot');
        $this->assertFileExists($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/fonts')) . DIRECTORY_SEPARATOR . 'fontawesome-webfont.ttf');
        $this->assertFileExists($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/fonts')) . DIRECTORY_SEPARATOR . 'fontawesome-webfont.woff');

        #Images
        $this->assertFileExists($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/images')) . '/test-media.png');
        $this->assertFileExists($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/images')) . '/test-media.jpg');
        $this->assertFileExists($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/images')) . '/test-media.gif');

        #Subfolders
        $this->assertFileExists($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/images/subfolder')) . '/test-sub-media.jpg');

        //Check optimisation
        //Gd2 compression
        $this->assertTrue(function_exists('imagecreatefromstring'), 'Function "imagecreatefromstring" must exits for tests');

        //Sizes
        $this->assertGreaterThan(filesize($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/images')) . '/test-media.png'), filesize(getcwd() . '/_files/images/test-media.png'));
        $this->assertGreaterThan(filesize($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/images')) . '/test-media.jpg'), filesize(getcwd() . '/_files/images/test-media.jpg'));
        $this->assertGreaterThan(filesize($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . md5(realpath(getcwd() . '/_files/images')) . '/test-media.gif'), filesize(getcwd() . '/_files/images/test-media.gif'));

        //Empty cache directory
        $this->emptyCacheDirectory();
    }

    public function testRenderMixins() {
        $this->assertInstanceOf('AssetsBundle\Service\ServiceOptions', $this->service->getOptions()->setActionName('test-mixins'));
        $this->assertEquals('test-mixins', $this->service->getOptions()->getActionName());

        //Test Cache file name
        $this->assertEquals($this->service->getOptions()->getCacheFileName(), md5(current(explode('\\', $this->routeMatch->getParam('controller'))) . $this->routeMatch->getParam('controller') . $this->service->getOptions()->getActionName()));

        //Empty cache directory
        $this->emptyCacheDirectory();

        //Initialize MvcEvent
        $oMvcEvent = new \Zend\Mvc\MvcEvent();
        $oMvcEvent
                ->setApplication(\Zend\Mvc\Application::init(\AssetsBundleTest\Bootstrap::getConfig()))
                //Reset route match
                ->setRouteMatch(new \Zend\Mvc\Router\RouteMatch(array()))
                //Reset request
                ->setRequest(new \Zend\Http\Request());

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($oMvcEvent));

        //Check assets content
        $this->assertAssetCacheContent(array($this->service->getOptions()->getCacheFileName() . '.css'));

        //Empty cache directory
        $this->emptyCacheDirectory();
    }

    public function testRenderTestAssetsFromUrl() {
        $this->assertInstanceOf('AssetsBundle\Service\ServiceOptions', $this->service->getOptions()->setActionName('test-assets-from-url'));
        $this->assertEquals('test-assets-from-url', $this->service->getOptions()->getActionName());

        //Test Cache file name
        $this->assertEquals(
                $this->service->getOptions()->getCacheFileName(), md5(current(explode('\\', $this->routeMatch->getParam('controller'))) . $this->routeMatch->getParam('controller') . $this->service->getOptions()->getActionName())
        );

        //Empty cache directory
        $this->emptyCacheDirectory();

        $this->assertTrue(extension_loaded('openssl'), 'Open SSL must be available for tests');

        //Test external files contents
        $this->assertStringEqualsFile(
                dirname(__DIR__) . '/../_files/prod-cache-expected/mootools.js', str_replace(PHP_EOL, "\n", file_get_contents('https://raw.github.com/neilime/zf2-assets-bundle/master/tests/AssetsBundleTest/_files/assets/js/mootools.js'))
        );

        $this->assertStringEqualsFile(
                dirname(__DIR__) . '/../_files/prod-cache-expected/bootstrap.css', str_replace(PHP_EOL, "\n", file_get_contents('https://raw.github.com/neilime/zf2-assets-bundle/master/tests/AssetsBundleTest/_files/assets/css/bootstrap.css'))
        );
        //Initialize MvcEvent
        $oMvcEvent = new \Zend\Mvc\MvcEvent();
        $oMvcEvent
                ->setApplication(\Zend\Mvc\Application::init(\AssetsBundleTest\Bootstrap::getConfig()))
                //Reset route match
                ->setRouteMatch(new \Zend\Mvc\Router\RouteMatch(array()))
                //Reset request
                ->setRequest(new \Zend\Http\Request());

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($oMvcEvent));

        //Check assets content
        $this->assertAssetCacheContent(array($this->service->getOptions()->getCacheFileName() . '.js'));
        $this->assertAssetCacheContent(array($this->service->getOptions()->getCacheFileName() . '.css'));

        //Empty cache directory
        $this->emptyCacheDirectory();
    }

    public function testRenderTestHugeAssets() {
        $this->assertInstanceOf('AssetsBundle\Service\ServiceOptions', $this->service->getOptions()->setActionName('test-huge-assets'));
        $this->assertEquals('test-huge-assets', $this->service->getOptions()->getActionName());

        //Empty cache directory
        $this->emptyCacheDirectory();

        //Initialize MvcEvent
        $oMvcEvent = new \Zend\Mvc\MvcEvent();
        $oMvcEvent
                ->setApplication(\Zend\Mvc\Application::init(\AssetsBundleTest\Bootstrap::getConfig()))
                //Reset route match
                ->setRouteMatch(new \Zend\Mvc\Router\RouteMatch(array()))
                //Reset request
                ->setRequest(new \Zend\Http\Request());

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($oMvcEvent));

        //Check assets content
        $this->assertAssetCacheContent(array($this->service->getOptions()->getCacheFileName() . '.css'));

        //Empty cache directory
        $this->emptyCacheDirectory();
    }

    /**
     * @param array $aAssetsFiles
     */
    protected function assertAssetCacheContent(array $aAssetsFiles) {
        $sCacheExpectedPath = dirname(__DIR__) . '/../_files/prod-cache-expected';
        foreach ($aAssetsFiles as $sAssetFile) {
            $this->assertFileExists($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sAssetFile);
            $this->assertStringEqualsFile(
                    $sCacheExpectedPath . DIRECTORY_SEPARATOR . $sAssetFile, preg_replace('/cache\/([0-9a-f]{32})\//', 'cache/encrypted-file-tree/', file_get_contents($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sAssetFile))
            );
        }
    }

    protected function emptyCacheDirectory() {
        //Empty cache directory except .gitignore
        foreach (new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($this->service->getOptions()->getCachePath(), \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        ) as $oFileinfo) {
            if ($oFileinfo->isDir()) {
                rmdir($oFileinfo->getRealPath());
            } elseif ($oFileinfo->getBasename() !== '.gitignore') {
                unlink($oFileinfo->getRealPath());
            }
        }
    }

}
