<?php

namespace AssetsBundle\AssetFile\AssetFileFilter;

abstract class AbstractAssetFileFilter extends \Zend\Stdlib\AbstractOptions implements \ AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface {

    /**
     * @var string
     */
    protected $assetFileFilterName;

    /**
     * @var \AssetsBundle\Service\ServiceOptions
     */
    protected $options;

    /**
     * @var string
     */
    protected $assetFileFilterProcessedDirPath;

    /**
     * @param string $sAssetFileFilterName
     * @return \AssetsBundle\Service\Filter\AbstractFilter
     * @throws \InvalidArgumentException
     */
    public function setAssetFileFilterName($sAssetFileFilterName) {
        if (empty($sAssetFileFilterName)) {
            throw new \InvalidArgumentException('Filter name is empty');
        }

        if (!is_string($sAssetFileFilterName)) {
            throw new \InvalidArgumentException('Filter name expects string, "' . gettype($sAssetFileFilterName) . '" given');
        }

        $this->assetFileFilterName = $sAssetFileFilterName;

        return $this;
    }

    /**
     * @return string
     * @throws \LogicException
     */
    public function getAssetFileFilterName() {
        if (is_string($this->assetFileFilterName) && !empty($this->assetFileFilterName)) {
            return $this->assetFileFilterName;
        }
        throw new \LogicException('Filter name is undefined');
    }

    /**
     * @param \AssetsBundle\Service\ServiceOptions $oOptions
     * @return \AssetsBundle\Service\Service
     */
    public function setOptions(\AssetsBundle\Service\ServiceOptions $oOptions) {
        $this->options = $oOptions;
        return $this;
    }

    /**
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function getOptions() {
        if (!($this->options instanceof \AssetsBundle\Service\ServiceOptions)) {
            $this->setOptions(new \AssetsBundle\Service\ServiceOptions());
        }
        return $this->options;
    }

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @return boolean|string
     */
    public function getCachedFilterRendering(\AssetsBundle\AssetFile\AssetFile $oAssetFile) {
        if (
                file_exists($sCachedFilterRenderingPath = $this->getOptions()->getProcessedDirPath() . DIRECTORY_SEPARATOR . md5($sAssetFilePath = $oAssetFile->getAssetFilePath())) && ($iLastModified = $oAssetFile->getAssetFileLastModified()) && ($iLastModifiedCompare = filemtime($sCachedFilterRenderingPath)) === false && $iLastModified <= $iLastModifiedCompare
        ) {
            \Zend\Stdlib\ErrorHandler::start();
            $sCachedFilterRendering = file_get_contents($sCachedFilterRenderingPath);
            \Zend\Stdlib\ErrorHandler::stop(true);
            return $sCachedFilterRendering;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getAssetFileFilterProcessedDirPath() {
        if (!is_dir($this->assetFileFilterProcessedDirPath)) {
            $this->assetFileFilterProcessedDirPath = $this->getOptions()->getProcessedDirPath() . DIRECTORY_SEPARATOR . strtolower(str_replace(
                                    array('/', '<', '>', '?', '*', '"', '|'), '_', $this->getAssetFileFilterName()
            ));
            if (!is_dir($this->assetFileFilterProcessedDirPath)) {
                \Zend\Stdlib\ErrorHandler::start();
                mkdir($this->assetFileFilterProcessedDirPath, 0775);
                \Zend\Stdlib\ErrorHandler::stop(true);
            }
        }
        return $this->assetFileFilterProcessedDirPath;
    }

}
