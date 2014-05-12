<?php

namespace AssetsBundle\Factory;

class ToolsServiceFactory implements \Zend\ServiceManager\FactoryInterface {

    /**
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return \AssetsBundle\Service\ToolsService
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator) {
        $oToolsService = new \AssetsBundle\Service\ToolsService();

        $oMvcEvent = $oServiceLocator->get('Application')->getMvcEvent();

        return $oToolsService
                        ->setAssetsBundleService($oServiceLocator->get('AssetsBundleService'))
                        ->setConsole($oServiceLocator->get('console'))
                        ->setMvcEvent($oMvcEvent ? clone $oMvcEvent : new \Zend\Mvc\MvcEvent());
    }

}
