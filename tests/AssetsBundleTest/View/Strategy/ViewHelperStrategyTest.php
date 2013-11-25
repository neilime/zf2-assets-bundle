<?php
namespace AssetsBundleTest\View\Strategy;
class ViewHelperStrategyTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @var \AssetsBundle\View\Strategy\ViewHelperStrategy
	 */
	protected $viewHelperStrategy;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->viewHelperStrategy = new \AssetsBundle\View\Strategy\ViewHelperStrategy();
	}

	/**
	 * @expectedException DomainException
	 */
	public function testRenderAssetWithWrongAssetType(){
		$this->viewHelperStrategy->renderAsset('test','test',\AssetsBundle\Service\Service::ASSET_MEDIA);
	}
}