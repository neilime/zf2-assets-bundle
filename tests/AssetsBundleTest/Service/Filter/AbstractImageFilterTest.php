<?php
namespace AssetsBundleTest\Service\Filter;
class AbstractImageFilterTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @var \AssetsBundle\Service\Filter\AbstractImageFilter
	 */
	protected $abstractImageFilter;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->abstractImageFilter = $this->getMockForAbstractClass('AssetsBundle\Service\Filter\AbstractImageFilter');
	}

	public function testSetImageFunction(){
		$fFunction = function (){
			return 'ok';
		};
		$this->assertInstanceOf('AssetsBundle\Service\Filter\AbstractImageFilter',$this->abstractImageFilter->setImageFunction($fFunction));
		$this->assertEquals($fFunction,$this->abstractImageFilter->getImageFunction());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetImageFunctionWithWrongFunction(){
		$this->abstractImageFilter->setImageFunction('wrong');
	}
}