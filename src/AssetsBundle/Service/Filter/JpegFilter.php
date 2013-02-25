<?php
namespace AssetsBundle\Service\Filter;
class JpegFilter extends \AssetsBundle\Service\Filter\AbstractImageFilter{
	/**
	 * Compression level: from 0 (worst quality, smaller file) to 100 (best quality, biggest file)
	 * @var int
	 */
	protected $imageQuality = 30;

	/**
	 * Constructor
	 * @param int $iImageQuality
	 */
	public function __construct($iImageQuality = null){
		parent::__construct();
		$this->imageFunction = array($this,'optimize');
		if(isset($iImageQuality))$this->setImageQuality($iImageQuality);
	}

	/**
	 * @param int $iImageQuality
	 * @throws \InvalidArgumentException
	 * @return \AssetsBundle\Service\Filter\JpegFilter
	 */
	public function setImageQuality($iImageQuality){
		if(!is_numeric($iImageQuality) || $iImageQuality < 0 || $iImageQuality > 100)throw new \InvalidArgumentException(sprintf(
			'$iImageQuality expects int from 0 to 100 "%s" given',
			is_scalar($iImageQuality)?$iImageQuality:(is_object($iImageQuality)?get_class($iImageQuality):gettype($iImageQuality))
		));
		$this->imageQuality = (int)$iImageQuality;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getImageQuality(){
		return $this->imageQuality;
	}

	/**
	 * @param resource $oImage
	 * @param string $sImagePath
	 * @return boolean
	 */
	public function optimize($oImage,$sImagePath){
		return imagejpeg($oImage,$sImagePath,$this->getImageQuality());
	}
}