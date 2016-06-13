<?php

namespace AssetsBundle\AssetFile\AssetFileFilter;

class JsAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter
{

    /**
     * @var string
     */
    const EXEC_TIME_PER_CHAR = 7E-5;

    /**
     * @var string
     */
    protected $assetFileFilterName = \AssetsBundle\AssetFile\AssetFile::ASSET_JS;

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return string
     */
    public function filterAssetFile(\AssetsBundle\AssetFile\AssetFile $oAssetFile)
    {
        // Try to retrieve cached filter rendering
        if ($sCachedFilterRendering = $this->getCachedFilteredContent($oAssetFile)) {
            return $sCachedFilterRendering;
        }

        if (!class_exists('JSMin')) {
            throw new \LogicException('"JSMin" class does not exist');
        }

        $iExecTime = strlen($sContent = $oAssetFile->getAssetFileContents()) * self::EXEC_TIME_PER_CHAR;
        $iMaxExecutionTime = ini_get('max_execution_time');
        set_time_limit(0);
        try {
            \Zend\Stdlib\ErrorHandler::start();
            $sFilteredContent = \JSMin::minify($sContent);
            \Zend\Stdlib\ErrorHandler::stop(true);
        } catch (\Exception $oException) {
            throw new \RuntimeException('An error occured while executing "\JSMin::minify" on file "'.$oAssetFile->getAssetFilePath().'"', $oException->getCode(), $oException);
        }
        $sFilteredContent = trim($sFilteredContent);
        $this->cacheFilteredAssetFileContent($oAssetFile, $sFilteredContent);
        set_time_limit($iMaxExecutionTime);
        return $sFilteredContent;
    }

}
