<?php

namespace AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter;

class PngImageAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter\AbstractImageAssetFileFilter {

    /**
     * @var string
     */
    protected $assetFileFilterName = 'Png';

    /**
     * Compression level: from 0 (no compression) to 9.
     * @var int
     */
    protected $imageQuality = 9;

    /**
     * @param int $iImageQuality
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\Filter\PngFilter
     */
    public function setImageQuality($iImageQuality) {
        if (!is_numeric($iImageQuality) || $iImageQuality < 0 || $iImageQuality > 9) {
            throw new \InvalidArgumentException(sprintf(
                    '$iImageQuality expects int from 0 to 9 "%s" given', is_scalar($iImageQuality) ? $iImageQuality : (is_object($iImageQuality) ? get_class($iImageQuality) : gettype($iImageQuality))
            ));
        }
        $this->imageQuality = (int) $iImageQuality;
        return $this;
    }

    /**
     * @return int
     */
    public function getImageQuality() {
        return $this->imageQuality;
    }

    /**
     * @param ressource $oImage
     * @return string
     * @throws \InvalidArgumentException
     */
    public function optimizeImage($oImage) {
        if (is_resource($oImage)) {
            ob_start();
            imagepng($oImage, null, $this->getImageQuality());
            return ob_get_clean();
        }
        throw new \InvalidArgumentException('Image expects a ressource, "' . gettype($oImage) . '" given');
    }

}
