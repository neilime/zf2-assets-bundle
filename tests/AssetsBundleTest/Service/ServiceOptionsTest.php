<?php
namespace AssetsBundleTest\Service;
class ServiceOptionsTest extends \PHPUnit\Framework\TestCase{

	/**
	 * @var \AssetsBundle\Service\ServiceOptions
	 */
	protected $serviceOptions;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->serviceOptions = new \AssetsBundle\Service\ServiceOptions();
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testIsProductionUndefined(){
		$this->serviceOptions->isProduction();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetProductionWithWrongValue(){
		$this->serviceOptions->setProduction('wrong');
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetLastModifiedTimeUndefined(){
		//Override lastModifiedTime
		$oReflectionClass = new \ReflectionClass('\AssetsBundle\Service\ServiceOptions');
		$oLastModifiedTimeProp = $oReflectionClass->getProperty('lastModifiedTime');
		$oLastModifiedTimeProp->setAccessible(true);
		$oLastModifiedTimeProp->setValue($this->serviceOptions,array());
		$this->serviceOptions->getLastModifiedTime();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetLastModifiedTimeWithWrongValue(){
		$this->serviceOptions->setLastModifiedTime(array());
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetCachePathUndefined(){
		$this->serviceOptions->getCachePath();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetCachePathWithWrongValue(){
		$this->serviceOptions->setCachePath(array());
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetAssetsPathUndefined(){
		$this->serviceOptions->getAssetsPath();
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetBaseUrlUndefined(){
		//Override baseUrl
		$oReflectionClass = new \ReflectionClass('\AssetsBundle\Service\ServiceOptions');
		$oBaseUrlProp = $oReflectionClass->getProperty('baseUrl');
		$oBaseUrlProp->setAccessible(true);
		$oBaseUrlProp->setValue($this->serviceOptions,array());
		$this->serviceOptions->getBaseUrl();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetBaseUrlWithWrongValue(){
		$this->serviceOptions->setBaseUrl(array());
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetCacheUrlUndefined(){
		$this->serviceOptions->getCacheUrl();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetCacheUrlWithWrongValue(){
		$this->serviceOptions->setCacheUrl(array());
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetMediaExtUndefined(){
		$this->serviceOptions->getMediaExt();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetMediaExtWithWrongValue(){
		$this->serviceOptions->setMediaExt(array(array('wrong')));
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testAllowsRecursiveSearchUndefined(){
		$this->serviceOptions->allowsRecursiveSearch();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetRecursiveSearchWithWrongValue(){
		$this->serviceOptions->setRecursiveSearch('wrong');
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetAssetsUndefined(){
		//Override assets
		$oReflectionClass = new \ReflectionClass('\AssetsBundle\Service\ServiceOptions');
		$oAssetsProp = $oReflectionClass->getProperty('assets');
		$oAssetsProp->setAccessible(true);
		$oAssetsProp->setValue($this->serviceOptions,null);
		$this->serviceOptions->getAssets();
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetRendererUndefined(){
		$this->serviceOptions->getRenderer();
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetModuleNameUndefined(){
		//Override moduleName
		$oReflectionClass = new \ReflectionClass('\AssetsBundle\Service\ServiceOptions');
		$oModuleNameProp = $oReflectionClass->getProperty('moduleName');
		$oModuleNameProp->setAccessible(true);
		$oModuleNameProp->setValue($this->serviceOptions,null);
		$this->serviceOptions->getModuleName();
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetControllerNameUndefined(){
		//Override controllerName
		$oReflectionClass = new \ReflectionClass('\AssetsBundle\Service\ServiceOptions');
		$oControllerNameProp = $oReflectionClass->getProperty('controllerName');
		$oControllerNameProp->setAccessible(true);
		$oControllerNameProp->setValue($this->serviceOptions,null);
		$this->serviceOptions->getControllerName();
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testGetActionNameUndefined(){
		//Override actionName
		$oReflectionClass = new \ReflectionClass('\AssetsBundle\Service\ServiceOptions');
		$oActionNameProp = $oReflectionClass->getProperty('actionName');
		$oActionNameProp->setAccessible(true);
		$oActionNameProp->setValue($this->serviceOptions,null);
		$this->serviceOptions->getActionName();
	}
}