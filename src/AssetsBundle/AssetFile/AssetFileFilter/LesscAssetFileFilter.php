<?php

namespace AssetsBundle\AssetFile\AssetFileFilter;

class LesscAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter {

    /**
     * @var string
     */
    protected $assetFileFilterName = 'Lessc';

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @return string
     */
    public function filterAssetFile(\AssetsBundle\AssetFile\AssetFile $oAssetFile) {
        //Try to retrieve cached filter rendering
        if ($sCachedFilterRendering = $this->getCachedFilterRendering($oAssetFile)) {
            return $sCachedFilterRendering;
        }

        $oLessParser = new \lessc();
        $oLessParser->addImportDir(getcwd());
        $oLessParser->setAllowUrlRewrite(true);
        $sReturn = trim($oLessParser->compile($oAssetFile->getAssetFileContents()));
        return $sReturn;
    }

}
