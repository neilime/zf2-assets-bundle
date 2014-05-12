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
        $oLessParser = new \lessc();
        $oLessParser->addImportDir(getcwd());
        $oLessParser->setAllowUrlRewrite(true);

        //Prevent time limit errors
        set_time_limit(0);

        return trim($oLessParser->compile($oAssetFile->getAssetFileContents()));
    }

}
