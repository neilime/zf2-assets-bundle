<?php
namespace AssetsBundleTest\Service;
class AssetsFilterManagerTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @var \AssetsBundle\Service\AssetsFilterManager
	 */
	protected $assetsFilterManager;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->assetsFilterManager = new \AssetsBundle\Service\AssetsFilterManager();
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testValidatePluginWithWrongPlugin(){
		$this->assetsFilterManager->validatePlugin('wrong');
	}
}