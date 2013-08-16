<?php
namespace AssetsBundle\Service;
class AssetsFilterManager extends \Zend\ServiceManager\AbstractPluginManager{
    /**
     * Validate the plugin. Checks that the filter loaded is an instance of \AssetsBundle\Service\Filter\FilterInterface
     * @param mixed $oAssetsFilter
     * @throws \RuntimeException
     */
    public function validatePlugin($oAssetsFilter){
        if($oAssetsFilter instanceof \AssetsBundle\Service\Filter\FilterInterface)return;
        throw new \RuntimeException(sprintf(
        	'Assets Filter expects an instance of \AssetsBundle\Service\Filter\FilterInterface, "%s" given',
        	is_object($oAssetsFilter)?get_class($oAssetsFilter):(is_scalar($oAssetsFilter)?$oAssetsFilter:gettype($oAssetsFilter))
        ));
    }
}