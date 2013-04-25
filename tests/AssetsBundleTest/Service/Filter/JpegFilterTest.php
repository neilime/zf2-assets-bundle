<?php
namespace AssetsBundleTest\Service\Filter;
class JpegFilterTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @var \AssetsBundle\Service\Filter\JpegFilter
	 */
	protected $jpegFilter;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->jpegFilter = new \AssetsBundle\Service\Filter\JpegFilter();
	}

	public function testSetImageQuality(){
		$this->assertInstanceOf('AssetsBundle\Service\Filter\JpegFilter',$this->jpegFilter->setImageQuality(50));
		$this->assertEquals(50,$this->jpegFilter->getImageQuality());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetImageQualityWithWrongQuality(){
		$this->jpegFilter->setImageQuality('wrong');
	}
}