<?php
namespace AssetsBundleTest\Service\Filter;
class PngFilterTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @var \AssetsBundle\Service\Filter\PngFilter
	 */
	protected $pngFilter;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->pngFilter = new \AssetsBundle\Service\Filter\PngFilter();
	}

	public function testSetImageQuality(){
		$this->assertInstanceOf('AssetsBundle\Service\Filter\PngFilter',$this->pngFilter->setImageQuality(4));
		$this->assertEquals(4,$this->pngFilter->getImageQuality());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetImageQualityWithWrongQuality(){
		$this->pngFilter->setImageQuality('wrong');
	}
}