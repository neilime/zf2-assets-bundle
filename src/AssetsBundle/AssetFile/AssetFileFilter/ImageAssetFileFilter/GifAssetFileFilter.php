<?php

namespace AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter;

class GifImageAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter\AbstractImageAssetFileFilter {

    /**
     * @var string
     */
    protected $assetFileFilterName = 'Gif';

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @return boolean
     */
    protected function assetFileShouldBeOptimize(\AssetsBundle\AssetFile\AssetFile $oAssetFile) {
        //Check if image is not an animated Gif
        return parent::assetFileShouldBeOptimize($oAssetFile) && !preg_match('#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', $oAssetFile->getAssetFileContents());
    }

    /**
     * @param ressource $oImage
     * @return string
     * @throws \InvalidArgumentException
     */
    public function optimizeImage($oImage) {
        if (is_resource($oImage)) {
            ob_start();
            imagegif($oImage);
            return ob_get_clean();
        }
        throw new \InvalidArgumentException('Image expects a ressource, "' . gettype($oImage) . '" given');
    }

}
