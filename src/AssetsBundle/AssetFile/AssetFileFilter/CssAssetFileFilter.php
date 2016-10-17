<?php

namespace AssetsBundle\AssetFile\AssetFileFilter;

class CssAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter {

    /**
     * @var string
     */
    protected $assetFileFilterName = \AssetsBundle\AssetFile\AssetFile::ASSET_CSS;

    /**
     * @var \CSSmin
     */
    protected $cssMin;

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @return string
     */
    public function filterAssetFile(\AssetsBundle\AssetFile\AssetFile $oAssetFile) {
        //Try to retrieve cached filter rendering
        if ($sCachedFilterRendering = $this->getCachedFilteredContent($oAssetFile)) {
            return $sCachedFilterRendering;
        }
        $sFilteredContent = $this->getCSSmin()->run($oAssetFile->getAssetFileContents());
        $this->cacheFilteredAssetFileContent($oAssetFile, $sFilteredContent);
        return $sFilteredContent;
    }

    /**
     * @return \CSSmin
     * @throws \LogicException
     */
    protected function getCSSmin() {
        if ($this->cssMin instanceof \CSSmin) {
            return $this->cssMin;
        }
        if (!class_exists('CSSmin')) {
            throw new \LogicException('"CSSmin" class does not exist');
        }
        return $this->cssMin = new \CSSmin(false);
    }

}
