<?php
namespace AssetsBundle\Service\Filter;
class GifFilter extends \AssetsBundle\Service\Filter\AbstractImageFilter{

	/**
	 * @var boolean
	 */
	protected $imagickExists = false;

	/**
	 * @var callable
	 */
	protected $imageFunction = 'imagegif';

	/**
	 * Constructor
	 */
	public function __construct(){
		//Check if imagecreatefromstring function exists
		if(class_exists('Imagick'))$this->imagickExists = true;
		parent::__construct();
	}

	/**
	 * @param string $sImgPath
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @return string
	 */
	public function run($sImagePath){
		//Check image path
		if(!is_string($sImagePath))throw new \InvalidArgumentException('$sImagePath expects string, "'.gettype($sImagePath).'" given');
		elseif(!file_exists($sImagePath))throw new \InvalidArgumentException('File "'.$sImagePath.'" does not exist');

		$sImageContent = file_get_contents($sImagePath);

		//Check is Animated Gif
		if(preg_match('#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', file_get_contents($sImagePath))){
			if($this->imagickExists){
				$oImage = new \Imagick($sImagePath);
				if(!$oImage->optimizeImageLayers())throw new \RuntimeException('"optimizeImageLayers" function failed');
				elseif(!$oImage->writeImages($sImagePath, true))throw new \RuntimeException('"writeImages" function failed');
			}
		}
		else{
			//if imagecreatefromstring does not exist, return
			if(!$this->imagecreatefromstringExists)return $sImagePath;

			//Optimize image
			if(!($oImage = imagecreatefromstring($sImageContent)))throw new \RuntimeException('"imagecreatefromstring" function failed');
			elseif(!imagealphablending($oImage, false))throw new \RuntimeException('"imagealphablending" function failed');
			elseif(!imagesavealpha($oImage, true))throw new \RuntimeException('"imagesavealpha" function failed');
			elseif(!call_user_func($this->getImageFunction(),$oImage,$sImagePath))throw new \RuntimeException('Image function failed');
		}
		return $sImagePath;
	}
}