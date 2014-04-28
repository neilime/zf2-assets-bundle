<?php

namespace AssetsBundle\Factory\AssetFileFilter;

class PngAssetFileFilterFactory implements \Zend\ServiceManager\FactoryInterface {

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return \AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter\PngImageAssetFileFilter
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator) {
        return new \AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter\PngImageAssetFileFilter();
    }

}
