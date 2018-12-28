<?php

namespace AssetsBundle\AssetFile\AssetFileFilter\StyleAssetFileFilter;

class LessphpAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter {

    /**
     * @var string
     */
    protected $assetFileFilterName = 'Lessphp';

    /**
     * @var \Less_Parser
     */
    protected $lessParser;

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @return string
     */
    public function filterAssetFile(\AssetsBundle\AssetFile\AssetFile $oAssetFile) {
        // Prevent time limit errors
        set_time_limit(0);

        $oLessParser = $this->getLessParser();
        $oLessParser->Reset();
        return trim($oLessParser->parseFile($oAssetFile->getAssetFilePath())->getCss());
    }

    /**
     * @return \Less_Parser
     */
    public function getLessParser() {
        if ($this->lessParser instanceof \Less_Parser) {
            return $this->lessParser;
        }
        $oLessParser = new \Less_Parser();
        return $this->setLessParser($oLessParser)->getLessParser();
    }

    /**
     * @param \Less_Parser $oParser
     * @return \AssetsBundle\AssetFile\AssetFileFilter\LessphpAssetFileFilter
     */
    public function setLessParser(\Less_Parser $oParser) {

        $oParser->SetImportDirs(array(getcwd() => getcwd()));
        $this->lessParser = $oParser;
        return $this;
    }

}
