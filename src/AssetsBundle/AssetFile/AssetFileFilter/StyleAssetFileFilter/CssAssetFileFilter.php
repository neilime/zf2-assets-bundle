<?php

namespace AssetsBundle\AssetFile\AssetFileFilter\StyleAssetFileFilter;

class CssAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\AbstractMinifierAssetFileFilter {

    /**
     * @var string
     */
    protected $assetFileFilterName = \AssetsBundle\AssetFile\AssetFile::ASSET_CSS;

    /**
     * @var \CSSmin
     */
    protected $cssMin;

       /**
     * @var string $sContent
     * @return string
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    protected function minifyContent($sContent) {
        return $this->getCSSmin()->run($sContent);
    }

    /**
     * @return \tubalmartin\CssMin\Minifier
     * @throws \LogicException
     */
    protected function getCSSmin() {
        if ($this->cssMin instanceof \tubalmartin\CssMin\Minifier) {
            return $this->cssMin;
        }
        $sClassName = '\\tubalmartin\\CssMin\\Minifier';
        if (!class_exists($sClassName)) {
            throw new \LogicException('"'. $sClassName. '" class does not exist');
        }
        return $this->cssMin = new $sClassName();
    }

}
