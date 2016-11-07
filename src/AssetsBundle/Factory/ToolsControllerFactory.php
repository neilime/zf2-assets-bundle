<?php

namespace AssetsBundle\Factory;

class ToolsControllerFactory implements \Zend\ServiceManager\FactoryInterface
{

    /**
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return  \AssetsBundle\Controller\ToolsController
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator)
    {
        $oToolsController = new \AssetsBundle\Controller\ToolsController();
        return $oToolsController->setAssetsBundleToolsService($oServiceLocator->getServiceLocator()->get('AssetsBundleToolsService'));
    }
}
