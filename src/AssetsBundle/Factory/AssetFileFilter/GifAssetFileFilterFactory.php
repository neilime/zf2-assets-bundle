<?php

namespace AssetsBundle\Factory\AssetFileFilter;

class GifAssetFileFilterFactory implements \Zend\ServiceManager\FactoryInterface {

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return \AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter\GifImageAssetFileFilter
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator) {
        return new \AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter\GifImageAssetFileFilter();
    }

}
