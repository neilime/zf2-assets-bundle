<?php
namespace AssetsBundleTest\Service\Filter;
class LessFilterTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @var \AssetsBundle\Service\Filter\LessFilter
	 */
	protected $lessFilter;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->lessFilter = new \AssetsBundle\Service\Filter\LessFilter();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetAssetsPathWithWrongPath(){
		$this->lessFilter->setAssetsPath('wrong');
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetAssetsPathUnset(){
		$this->lessFilter->getAssetsPath();
	}
}