<?php
namespace AssetsBundleTest\Service;
class RenderStrategyManagerTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @var \AssetsBundle\Service\RenderStrategyManager
	 */
	protected $renderStrategyManager;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->renderStrategyManager = new \AssetsBundle\Service\RenderStrategyManager();
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testValidatePluginWithWrongPlugin(){
		$this->renderStrategyManager->validatePlugin('wrong');
	}
}