<?php

namespace AssetsBundle\AssetFile\AssetFileFilter;

class LessPhpAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter {

    /**
     * @var string
     */
    protected $assetFileFilterName = 'Less.php';

    /**
     * @var string
     */
    protected $assetFileFilterCacheDir;

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @return string
     */
    public function filterAssetFile(\AssetsBundle\AssetFile\AssetFile $oAssetFile) {
        //Try to retrieve cached filter rendering
        if ($sCachedFilterRendering = $this->getCachedFilterRendering($oAssetFile)) {
            return $sCachedFilterRendering;
        }

        $oParser = new \Less_Parser(array(
            'cache_dir' => $this->getAssetFileFilterProcessedDirPath(),
        ));

        //Parse asset file
        $oParser->parseFile($oAssetFile->getAssetFileContents(), getcwd());

        //Return css
        return $oParser->getCss();
    }

}
