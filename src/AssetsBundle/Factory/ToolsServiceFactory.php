<?php

namespace AssetsBundle\Factory;

class ToolsServiceFactory implements \Zend\ServiceManager\FactoryInterface
{

    /**
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return \AssetsBundle\Service\ToolsService
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator)
    {
        $oToolsService = new \AssetsBundle\Service\ToolsService();
        $oToolsService
                ->setAssetsBundleService($oServiceLocator->get('AssetsBundleService'))
                ->setMvcEvent(($oMvcEvent = $oServiceLocator->get('Application')->getMvcEvent()) ? clone $oMvcEvent : new \Zend\Mvc\MvcEvent());

        if ($oServiceLocator->has('console') && ($oConsole = $oServiceLocator->get('console')) instanceof \Zend\Console\Adapter\AdapterInterface) {
            $oToolsService->setConsole($oConsole);
        }
        return $oToolsService;
    }
}
