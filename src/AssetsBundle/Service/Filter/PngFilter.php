<?php
namespace AssetsBundle\Service\Filter;
class PngFilter implements \AssetsBundle\Service\Filter\FilterInterface{

	/**
	 * Compression level: from 0 (no compression) to 9.
	 * @var int
	 */
	protected $pngQuality = 9;

	/**
	 * @var boolean
	 */
	protected $imagecreatefromstringExists = false;

	/**
	 * Constructor
	 * @param int $iPngQuality
	 */
	public function __construct($iPngQuality = null){
		//Check if imagecreatefromstring function exists
		if(function_exists('imagecreatefromstring'))$this->imagecreatefromstringExists = true;
		if(isset($iPngQuality))$this->setPngQuality($iPngQuality);
	}

	/**
	 * @param int $iPngQuality
	 * @throws \InvalidArgumentException
	 * @return \AssetsBundle\Service\Filter\PngFilter
	 */
	public function setPngQuality($iPngQuality){
		if(!is_numeric($iPngQuality) || $iPngQuality < 0 || $iPngQuality > 9)throw new \InvalidArgumentException(sprintf(
			'$iPngQuality expects int from 0 to 9 "%s" given',
			is_scalar($iPngQuality)?$iPngQuality:gettype($iPngQuality)
		));
		$this->pngQuality = (int)$iPngQuality;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPngQuality(){
		return $this->pngQuality;
	}

	/**
	 * @param string $sImgPath
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @return string
	 */
	public function run($sPngPath){
		//if imagecreatefromstring does not exist, return
		if(!$this->imagecreatefromstringExists)return $sPngPath;

		//Check png path
		elseif(!is_string($sPngPath))throw new \InvalidArgumentException('$sPngPath expects string, "'.gettype($sPngPath).'" given');
		elseif(!file_exists($sPngPath))throw new \InvalidArgumentException('File "'.$sPngPath.'" does not exist');

		//Optimize image
		elseif(!($oImage = imagecreatefromstring(file_get_contents($sPngPath))))throw new \RuntimeException('"imagecreatefromstring" function failed');
		elseif(!imagepng($oImage,$sPngPath, $this->getPngQuality()))throw new \RuntimeException('"imagepng" function failed');

		return $sPngPath;
	}
}