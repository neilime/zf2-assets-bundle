<?php

namespace AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter;

abstract class AbstractImageAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter {

    /**
     * @var boolean
     */
    protected $imagecreatefromstringExists = false;

    /**
     * Constructor
     * @param array $oOptions
     */
    public function __construct($oOptions = null) {
        parent::__construct($oOptions);

        //Check if imagecreatefromstring function exists
        if (function_exists('imagecreatefromstring')) {
            $this->imagecreatefromstringExists = true;
        }
    }

    /**
     * @param string $sImagePath
     * @see \AssetsBundle\Service\Filter\FilterInterface::run()
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return string
     */
    public function filterAssetFile(\AssetsBundle\AssetFile\AssetFile $oAssetFile) {
        //If asset file should not be optimize, return current content
        if (!$this->assetFileShouldBeOptimize($oAssetFile)) {
            return $oAssetFile->getAssetFileContents();
        }
        //Optimize image
        \Zend\Stdlib\ErrorHandler::start();
        $oImage = imagecreatefromstring($oAssetFile->getAssetFileContents());
        imagealphablending($oImage, false);
        imagesavealpha($oImage, true);
        \Zend\Stdlib\ErrorHandler::stop(true);
        return $this->optimizeImage($oImage);
    }

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @return boolean
     */
    protected function assetFileShouldBeOptimize(\AssetsBundle\AssetFile\AssetFile $oAssetFile) {
        return $this->imagecreatefromstringExists && !!$oAssetFile->getAssetFileContents();
    }

    /**
     * @param resource $oImage
     * @return string
     */
    protected abstract function optimizeImage($oImage);
}
