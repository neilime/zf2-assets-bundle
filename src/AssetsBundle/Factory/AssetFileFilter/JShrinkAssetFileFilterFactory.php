<?php

namespace AssetsBundle\Factory\AssetFileFilter;

class JShrinkAssetFileFilterFactory implements \Zend\ServiceManager\FactoryInterface
{

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JShrinkAssetFileFilter
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator)
    {
        return new \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JShrinkAssetFileFilter();
    }

}
