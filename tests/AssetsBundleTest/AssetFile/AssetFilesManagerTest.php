<?php

namespace AssetsBundleTest\AssetFile;

class AssetFilesManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \AssetsBundle\AssetFile\AssetFilesManager
     */
    protected $assetFilesManager;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $this->assetFilesManager = new \AssetsBundle\AssetFile\AssetFilesManager();
    }

    public function testRewriteDataUrl()
    {
        $aMatches = array(
            'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAyCAYAAACd+7GKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAClJREFUeNpi/v//vwMTAwPDfzjBgMpFI/7hFSOT9Y8qRuF3JLoHAQIMAHYtMmRA+CugAAAAAElFTkSuQmCC)',
            'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAyCAYAAACd+7GKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAClJREFUeNpi/v//vwMTAwPDfzjBgMpFI/7hFSOT9Y8qRuF3JLoHAQIMAHYtMmRA+CugAAAAAElFTkSuQmCC'
        );

        $this->assertSame($aMatches[0], $this->assetFilesManager->rewriteUrl($aMatches, new \AssetsBundle\AssetFile\AssetFile()));
    }

    public function tearDown()
    {
        \AssetsBundleTest\Bootstrap::getServiceManager()->get('AssetsBundleToolsService')->emptyCache(false);
        parent::tearDown();
    }
}
