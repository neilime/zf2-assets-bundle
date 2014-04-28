<?php

namespace AssetsBundle\Factory\AssetFileFilter;

class LessPhpAssetFileFilterFactory implements \Zend\ServiceManager\FactoryInterface {

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return \AssetsBundle\AssetFile\AssetFileFilter\LessPhpAssetFileFilter
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator) {
        return new \AssetsBundle\AssetFile\AssetFileFilter\LessPhpAssetFileFilter();
    }

}
