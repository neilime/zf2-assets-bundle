<?php

namespace AssetsBundle\Factory\AssetFileFilter;

class JsMinAssetFileFilterFactory implements \Zend\ServiceManager\FactoryInterface
{

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JsMinAssetFileFilter
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator)
    {
        return new \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JsMinAssetFileFilter();
    }

}
