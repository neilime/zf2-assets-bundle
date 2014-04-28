<?php

namespace AssetsBundle\Factory\AssetFileFilter;

class JsAssetFileFilterFactory implements \Zend\ServiceManager\FactoryInterface {

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator) {
        return new \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter();
    }

}
