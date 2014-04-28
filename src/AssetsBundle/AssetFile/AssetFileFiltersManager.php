<?php

namespace AssetsBundle\AssetFile;

class AssetFileFiltersManager extends \Zend\ServiceManager\AbstractPluginManager {

    /**
     * @var \AssetsBundle\Service\ServiceOptions
     */
    protected $options;

    /**
     * Validate the plugin. Checks that the filter loaded is an instance of \AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface
     * @param mixed $oAssetsFilter
     * @throws \RuntimeException
     */
    public function validatePlugin($oAssetFileFilter) {
        if ($oAssetFileFilter instanceof \AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface) {
            return;
        }
        throw new \RuntimeException(sprintf(
                'Assets Filter expects an instance of \AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface, "%s" given', is_object($oAssetFileFilter) ? get_class($oAssetFileFilter) : (is_scalar($oAssetFileFilter) ? $oAssetFileFilter : gettype($oAssetFileFilter))
        ));
    }

    /**
     * @param string $sName
     * @param mixed $oAssetFileFilter
     * @param boolean $bShared
     * @return \AssetsBundle\AssetFile\AssetFileFiltersManager
     */
    public function setService($sName, $oAssetFileFilter, $bShared = true) {
        if ($oAssetFileFilter) {
            $this->validatePlugin($oAssetFileFilter);
            $oAssetFileFilter->setOptions($this->getOptions());
        }
        parent::setService($sName, $oAssetFileFilter, $bShared);
        return $this;
    }

    /**
     * @param \AssetsBundle\Service\ServiceOptions $oOptions
     * @return \AssetsBundle\AssetFile\AssetFileFiltersManager
     */
    public function setOptions(\AssetsBundle\Service\ServiceOptions $oOptions) {
        $this->options = $oOptions;
        foreach ($this->instances as $oAssetFileFilter) {
            $oAssetFileFilter->setOptions($oOptions);
        }
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

}
