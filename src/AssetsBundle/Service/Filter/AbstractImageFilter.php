<?php
namespace AssetsBundle\Service\Filter;
abstract class AbstractImageFilter implements \AssetsBundle\Service\Filter\FilterInterface{

	/**
	 * @var callable
	 */
	protected $imageFunction;

	/**
	 * @var boolean
	 */
	protected $imagecreatefromstringExists = false;

	/**
	 * Constructor
	 */
	public function __construct(){
		//Check if imagecreatefromstring function exists
		if(function_exists('imagecreatefromstring'))$this->imagecreatefromstringExists = true;
	}

	/**
	 * @param callable $sImageFunction
	 * @throws \LogicException
	 */
	public function setImageFunction($sImageFunction){
		//Check if imageFunction function exists
		if(!is_callable($sImageFunction))throw new \LogicException(sprintf(
			'Image function expects callable value, "%s" given',
			is_scalar($sImageFunction)?$sImageFunction:(is_object($sImageFunction)?get_class($sImageFunction):gettype($sImageFunction))
		));
		return $this;
	}

	/**
	 * @return callable
	 */
	public function getImageFunction(){
		return $this->imageFunction;
	}

	/**
	 * @param string $sImgPath
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @return string
	 */
	public function run($sImagePath){
		//if imagecreatefromstring does not exist, return
		if(!$this->imagecreatefromstringExists)return $sImagePath;

		//Check image path
		elseif(!is_string($sImagePath))throw new \InvalidArgumentException('$sImagePath expects string, "'.gettype($sImagePath).'" given');
		elseif(!file_exists($sImagePath))throw new \InvalidArgumentException('File "'.$sImagePath.'" does not exist');

		//Optimize image
		elseif(!($oImage = imagecreatefromstring(file_get_contents($sImagePath))))throw new \RuntimeException('"imagecreatefromstring" function failed');
		elseif(!imagealphablending($oImage, false))throw new \RuntimeException('"imagealphablending" function failed');
		elseif(!imagesavealpha($oImage, true))throw new \RuntimeException('"imagesavealpha" function failed');
		elseif(!call_user_func($this->getImageFunction(),$oImage,$sImagePath))throw new \RuntimeException('Image function failed');

		return $sImagePath;
	}
}