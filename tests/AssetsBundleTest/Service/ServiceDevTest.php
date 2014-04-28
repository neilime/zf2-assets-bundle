<?php

namespace AssetsBundleTest\Service;

class ServiceDevTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var array
     */
    private $configuration = array(
        'assets_bundle' => array(
            'production' => false,
            'assets' => array(
                'AssetsBundleTest' => array(
                    'css' => array(
                        '@zfRootPath/_files/assets/css/test.css',
                        'css/full-dir'
                    ),
                    'less' => array('@zfRootPath/_files/assets/less/test.less'),
                    'js' => array('@zfRootPath/_files/assets/js/test.js'),
                    'AssetsBundleTest\Controller\Test' => array(
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
                )
            )
        )
    );

    /**
     * @var \AssetsBundle\Service\Service
     */
    private $service;

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    private $mvcEvent;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp() {
        $oApplication = \Zend\Mvc\Application::init(\AssetsBundleTest\Bootstrap::getConfig());
        $this->mvcEvent = $oApplication->getMvcEvent()
                ->setRequest(new \Zend\Http\Request())
                ->setRouteMatch(new \Zend\Mvc\Router\RouteMatch(array('controller' => 'AssetsBundleTest\Controller\Test', 'action' => 'index')));
        $this->createService();

        //Empty cache directory
        $this->emptyCacheDirectory();
        //Empty processed directory
        $this->emptyProcessedDirectory();
    }

    /**
     * @param array $aAssetsConfiguration
     */
    protected function createService(array $aAssetsConfiguration = null) {
        $oServiceManager = \AssetsBundleTest\Bootstrap::getServiceManager()->setAllowOverride(true);

        $aConfiguration = $oServiceManager->get('Config');
        unset($aConfiguration['assets_bundle']['assets']);

        $aNewConfig = \Zend\Stdlib\ArrayUtils::merge($aConfiguration, $this->configuration);
        if ($aAssetsConfiguration) {
            $aNewConfig['assets_bundle']['assets'] = $aAssetsConfiguration;
        }

        $oServiceManager->setService('Config', $aNewConfig);

        //Rebuild AssetsBundle service options
        $oServiceOptionsFactory = new \AssetsBundle\Factory\ServiceOptionsFactory();
        $oServiceManager->setService('AssetsBundleServiceOptions', $oServiceOptionsFactory->createService($oServiceManager));

        //Define service
        $oServiceFactory = new \AssetsBundle\Factory\ServiceFactory();
        $this->service = $oServiceFactory->createService($oServiceManager);
    }

    public function testService() {
        //Test service instance
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service);

        //Test cache path
        $this->assertEquals(realpath(getcwd() . '/_files/cache'), $this->service->getOptions()->getCachePath());
    }

    public function testRenderSimpleAssets() {
        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));

        //Css cache files
        $this->assertAssetCacheContent(array(
            '_files/assets/css/test.css',
            '_files/assets/css/full-dir/full-dir.css'
        ));

        //Less cache files
        $sLessFile = 'dev_' . $this->service->getOptions()->getCacheFileName() . '.less';
        $this->assertAssetCacheContent(array($sLessFile));

        //Js cache files
        $this->assertAssetCacheContent(array('_files/assets/js/test.js'));

        //Retrieve assets last modified date
        $this->assertNotEquals($iLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sLessFile), false);

        sleep(1);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));

        //Check if assets are not rendered again
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sLessFile), false);
        $this->assertEquals($iLastModified, $iNewLastModified);

        sleep(1);

        //Change configuration
        $aAssetsConfiguration = $this->configuration['assets_bundle']['assets'];
        $aAssetsConfiguration['less'][] = 'less/new.less';
        $this->createService($aAssetsConfiguration);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));

        //Check if assets has been rendered this time
        $iLastModified = $iNewLastModified;
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sLessFile), false);
        $this->assertGreaterThan($iLastModified, $iNewLastModified);

        sleep(1);

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));

        //Check if assets are not rendered again
        $iLastModified = $iNewLastModified;
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sLessFile), false);
        $this->assertEquals($iLastModified, $iNewLastModified);

        //Update less assets last modified
        touch($this->service->getOptions()->getAssetsPath() . DIRECTORY_SEPARATOR . 'less/test.less');

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));

        //Check if assets has been rendered this time
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sLessFile), false);
        $this->assertGreaterThan($iLastModified, $iNewLastModified);

        //Clear stat cache
        clearstatcache();

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));

        //Check if assets are not rendered again
        $iLastModified = $iNewLastModified;
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sLessFile), false);
        $this->assertEquals($iLastModified, $iNewLastModified);

        //Update less import assets last modified
        sleep(1);
        touch($this->service->getOptions()->getAssetsPath() . DIRECTORY_SEPARATOR . 'less/test.less');

        //Clear stat cache
        clearstatcache();

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));

        //Check if assets has been rendered this time
        $iLastModified = $iNewLastModified;
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sLessFile), false);
        $this->assertGreaterThan($iLastModified, $iNewLastModified);

        //Clear stat cache
        clearstatcache();

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));

        //Check if assets are not rendered again
        $iLastModified = $iNewLastModified;
        $this->assertNotEquals($iNewLastModified = filemtime($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sLessFile), false);
        $this->assertEquals($iLastModified, $iNewLastModified);
    }

    public function testRenderAssetsWithMedias() {
        //Change action name
        $this->mvcEvent->getRouteMatch()->setParam('action', 'test-media');

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));

        //Css cache files
        $this->assertAssetCacheContent(array(
            '_files/assets/css/test-media.css',
            '_files/assets/css/test.css',
            '_files/assets/css/full-dir/full-dir.css'
        ));

        //Less cache files
        $this->assertAssetCacheContent(array('dev_' . $this->service->getOptions()->getCacheFileName() . '.less'));

        //Js cache files
        $this->assertAssetCacheContent(array('_files/assets/js/test.js'));
    }

    public function testRenderTestAssetsFromUrl() {
        //Change action name
        $this->mvcEvent->getRouteMatch()->setParam('action', 'test-assets-from-url');

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));
    }

    public function testRenderWithoutAssetsPath() {

        //Change assets config
        $this->createService(array('css' => array(
                $this->service->getOptions()->getAssetsPath() . DIRECTORY_SEPARATOR . 'css/test.css'
        )));

        //Unset assets path config
        $this->assertInstanceOf('AssetsBundle\Service\ServiceOptions', $this->service->getOptions()->setAssetsPath(null));

        //Render assets
        $this->assertInstanceOf('AssetsBundle\Service\Service', $this->service->renderAssets($this->mvcEvent));
    }

    /**
     * @param array $aAssetsFiles
     */
    protected function assertAssetCacheContent(array $aAssetsFiles) {
        $sCacheExpectedPath = getcwd() . '/_files/dev-cache-expected';
        foreach ($aAssetsFiles as $sAssetFile) {
            $this->assertFileExists($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sAssetFile);
            $this->assertStringEqualsFile(
                    //Expected file
                    $sCacheExpectedPath . DIRECTORY_SEPARATOR . $sAssetFile,
                    //Cache content
                    preg_replace('/' . preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', getcwd()), '/') . '/', '/current-dir', file_get_contents($this->service->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sAssetFile)),
                    //Info for failing assertion
                    $sAssetFile
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

    protected function emptyProcessedDirectory() {
        //Empty processed directory except .gitignore
        foreach (new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($this->service->getOptions()->getProcessedDirPath() . DIRECTORY_SEPARATOR . 'lessc', \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        ) as $oFileinfo) {
            if ($oFileinfo->isDir()) {
                rmdir($oFileinfo->getRealPath());
            } elseif ($oFileinfo->getBasename() !== '.gitignore') {
                unlink($oFileinfo->getRealPath());
            }
        }
        foreach (new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($this->service->getOptions()->getProcessedDirPath() . DIRECTORY_SEPARATOR . 'config', \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        ) as $oFileinfo) {
            if ($oFileinfo->isDir()) {
                rmdir($oFileinfo->getRealPath());
            } elseif ($oFileinfo->getBasename() !== '.gitignore') {
                unlink($oFileinfo->getRealPath());
            }
        }
    }

    public function tearDown() {
        //Empty cache directory
        // $this->emptyCacheDirectory();
        //Empty processed directory
        // $this->emptyProcessedDirectory();
        parent::tearDown();
    }

}
