<?php

namespace AssetsBundleTest\Service;

class ServiceDevTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var array
     */
    private $configuration = array(
        'asset_bundle' => array(
            'production' => false,
            'assets' => array(
                'test-module' => array(
                    'css' => array(
                        '@zfRootPath/AssetsBundleTest/_files/assets/css/test.css',
                        '@zfRootPath/AssetsBundleTest/_files/assets/css/full-dir'
                    ),
                    'less' => array('@zfRootPath/AssetsBundleTest/_files/assets/less/test.less'),
                    'js' => array('@zfRootPath/AssetsBundleTest/_files/assets/js/test.js'),
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
                        'test-uncached-media' => array(
                            'less' => array('less/test-media.less'),
                        )
                    )
                ),
                'without-assets-path-module' => array(
                    'css' => array('@zfRootPath/AssetsBundleTest/_files/assets/css/test-media-without-assets-path.css'),
                    'less' => array('@zfRootPath/AssetsBundleTest/_files/assets/less/test-media.less'),
                    'media' => array(
                        '@zfRootPath/AssetsBundleTest/_files/fonts',
                        '@zfRootPath/AssetsBundleTest/_files/images'
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
        $this->routeMatch = new \Zend\Mvc\Router\RouteMatch(array('controller' => 'test-module\index-controller', 'action' => 'index'));
        $this->createService();
    }

    /**
     * @param array $aAssetsConfiguration
     */
    protected function createService(array $aAssetsConfiguration = null) {
        $oServiceManager = \AssetsBundleTest\Bootstrap::getServiceManager();

        $aConfiguration = $oServiceManager->get('Config');
        unset($aConfiguration['asset_bundle']['assets']);

        $bAllowOverride = $oServiceManager->getAllowOverride();
        if (!$bAllowOverride)
            $oServiceManager->setAllowOverride(true);

        $aNewConfig = \Zend\Stdlib\ArrayUtils::merge($aConfiguration, $this->configuration);
        if ($aAssetsConfiguration)
            $aNewConfig['asset_bundle']['assets'] = $aAssetsConfiguration;
        $oServiceManager->setService('Config', $aNewConfig)->setAllowOverride($bAllowOverride);

        //Define service
        $oServiceFactory = new \AssetsBundle\Factory\ServiceFactory();
        $this->service = $oServiceFactory->createService($oServiceManager);
        $this->service->getOptions()
                ->setRenderer(new \Zend\View\Renderer\PhpRenderer())
                ->setModuleName(current(explode('\\', $this->routeMatch->getParam('controller'))))
                ->setControllerName($this->routeMatch->getParam('controller'))
                ->setActionName($this->routeMatch->getParam('action'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFactoryWithWrongFilters() {
        \AssetsBundle\Service\Service::factory(array('filters' => 'wrong'));
    }

    public function testFactoryWithCustomRendererToStrategy() {
        \AssetsBundle\Service\Service::factory(array('rendererToStrategy' => array('zendviewrendererphprenderer' => new \AssetsBundle\View\Strategy\ViewHelperStrategy())));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFactoryWithWrongRendererToStrategy() {
        \AssetsBundle\Service\Service::factory(array('rendererToStrategy' => 'wrong'));
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetOptionsUndefined() {
        $oService = new \AssetsBundle\Service\Service();
        $oService->getOptions();
    }

    public function testService() {
        //Test service instance
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service);

        //Test cache path
        $this->assertEquals(realpath(__DIR__ . '/../_files/cache') . DIRECTORY_SEPARATOR, $this->service->getOptions()->getCachePath());

        //Test assets configuration
        $this->assertTrue($this->service->moduleHasAssetConfiguration('test-module'));
        $this->assertFalse($this->service->moduleHasAssetConfiguration('wrong-module'));

        $this->assertTrue($this->service->controllerHasAssetConfiguration('test-module\index-controller'));
        $this->assertFalse($this->service->controllerHasAssetConfiguration('wrong-controller'));

        $this->assertTrue($this->service->actionHasAssetConfiguration('test-media'));
        $this->assertFalse($this->service->actionHasAssetConfiguration('wrong-action'));
    }

    public function testAssetTypeExists() {
        $this->assertFalse($this->service->assetTypeExists('wrong'));
    }

    public function testRenderSimpleAssets() {
        //Empty cache directory
        $this->emptyCacheDirectory();

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Css cache files
        $this->assertAssetCacheContent(array(
            'css_test.css',
            'dev_' . $this->service->getCacheFileName() . '.css',
            'css_full-dir_full-dir.css'
        ));

        //Less cache files
        $sLessFile = 'dev_' . $this->service->getCacheFileName() . '.less';
        $this->assertAssetCacheContent(array($sLessFile));

        //Js cache files
        $this->assertAssetCacheContent(array('js_test.js'));

        //Retrieve assets last modified date
        $this->assertNotEquals($iLastModified = filemtime($this->service->getOptions()->getCachePath() . $sLessFile), false);

        sleep(1);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Check if assets are not rendered again
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . $sLessFile), false);
        $this->assertEquals($iLastModified, $iNewLastModified);

        sleep(1);

        //Change configuration
        $aAssetsConfiguration = $this->configuration['asset_bundle']['assets'];
        $aAssetsConfiguration['less'][] = 'less/new.less';
        $this->createService($aAssetsConfiguration);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Check if assets has been rendered this time
        $iLastModified = $iNewLastModified;
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . $sLessFile), false);
        $this->assertGreaterThan($iLastModified, $iNewLastModified);

        sleep(1);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Check if assets are not rendered again
        $iLastModified = $iNewLastModified;
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . $sLessFile), false);
        $this->assertEquals($iLastModified, $iNewLastModified);

        //Update less assets
        file_put_contents($this->service->getOptions()->getAssetsPath() . 'less/test.less', file_get_contents($this->service->getOptions()->getAssetsPath() . 'less/test.less'));

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Check if assets has been rendered this time
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . $sLessFile), false);
        $this->assertGreaterThan($iLastModified, $iNewLastModified);

        sleep(1);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Check if assets are not rendered again
        $iLastModified = $iNewLastModified;
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . $sLessFile), false);
        $this->assertEquals($iLastModified, $iNewLastModified);

        //Update less import assets
        file_put_contents($this->service->getOptions()->getAssetsPath() . 'less/import.less', file_get_contents($this->service->getOptions()->getAssetsPath() . 'less/import.less'));

        sleep(1);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Check if assets are not rendered again
        $iLastModified = $iNewLastModified;
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . $sLessFile), false);
        $this->assertGreaterThan($iLastModified, $iNewLastModified);

        sleep(1);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Check if assets has been rendered this time
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . $sLessFile), false);
        $this->assertGreaterThan($iLastModified, $iNewLastModified);

        //Empty cache directory
        $this->emptyCacheDirectory();
    }

    public function testRenderAssetsWithMedias() {
        //Change action name
        $this->routeMatch->setParam('action', 'test-media');
        $this->service->getOptions()->setActionName($this->routeMatch->getParam('action'));

        //Empty cache directory
        $this->emptyCacheDirectory();

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Css cache files
        $this->assertAssetCacheContent(array(
            'css_test-media.css',
            'css_test.css',
            'dev_' . $this->service->getCacheFileName() . '.css',
            'css_full-dir_full-dir.css'
        ));

        //Less cache files
        $this->assertAssetCacheContent(array('dev_' . $this->service->getCacheFileName() . '.less'));

        //Js cache files
        $this->assertAssetCacheContent(array('js_test.js'));

        //Empty cache directory
        $this->emptyCacheDirectory();
    }

    public function testRenderAssetsWithMediasWithoutAssetsPath() {
        $sAssetsPath = $this->service->getOptions()->getAssetsPath();
        $this->service->getOptions()->setAssetsPath(null);

        //Change action name
        $this->service->getOptions()->setModuleName('without-assets-path-module');

        //Empty cache directory
        $this->emptyCacheDirectory();

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Css cache files
        $this->assertAssetCacheContent(array(
            '_AssetsBundleTest__files_assets_css_test-media-without-assets-path.css'
        ));

        //Less cache files
        $this->assertAssetCacheContent(array('dev_' . $this->service->getCacheFileName() . '.less'));

        //Empty cache directory
        $this->emptyCacheDirectory();

        $this->service->getOptions()->setAssetsPath($sAssetsPath);
    }

    /**
     * @expectedException LogicException
     */
    public function testRenderAssetsWithUncachedMedias() {
        //Change action name
        $this->routeMatch->setParam('action', 'test-uncached-media');
        $this->service->getOptions()->setActionName($this->routeMatch->getParam('action'));

        //Empty cache directory
        $this->emptyCacheDirectory();

        //Render assets
        $this->service->renderAssets();
    }

    public function testRenderTestAssetsFromUrl() {
        //Change action name
        $this->routeMatch->setParam('action', 'test-assets-from-url');
        $this->service->getOptions()->setActionName($this->routeMatch->getParam('action'));

        //Empty cache directory
        $this->emptyCacheDirectory();

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());

        //Empty cache directory
        $this->emptyCacheDirectory();
    }

    public function testRenderWithoutAssetsPath() {

        //Change assets config
        $this->createService(array('css' => array(
                $this->service->getOptions()->getAssetsPath() . 'css/test.css'
        )));

        //Unset assets path config
        $this->assertInstanceOf('AssetsBundle\Service\ServiceOptions', $this->service->getOptions()->setAssetsPath(null));

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets());
    }

    /**
     * @param array $aAssetsFiles
     */
    protected function assertAssetCacheContent(array $aAssetsFiles) {
        $sCacheExpectedPath = __DIR__ . '/../_files/dev-cache-expected';
        foreach ($aAssetsFiles as $sAssetFile) {
            $this->assertFileExists($this->service->getOptions()->getCachePath() . $sAssetFile);
            file_put_contents($sCacheExpectedPath . DIRECTORY_SEPARATOR . $sAssetFile, preg_replace('/' . \AssetsBundle\Service\Service::sanitizeFileName(preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', getcwd()), '/')) . '/', '/current-dir/', file_get_contents($this->service->getOptions()->getCachePath() . $sAssetFile)));
            $this->assertStringEqualsFile(
                    $sCacheExpectedPath . DIRECTORY_SEPARATOR . $sAssetFile, preg_replace('/' . \AssetsBundle\Service\Service::sanitizeFileName(preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', getcwd()), '/')) . '/', '/current-dir/', file_get_contents($this->service->getOptions()->getCachePath() . $sAssetFile))
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
