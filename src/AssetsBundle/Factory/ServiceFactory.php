<?php
namespace AssetsBundle\Factory;
class ServiceFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @throws \Exception
	 * @return \AssetsBundle\Service\Service
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$aConfiguration = $oServiceLocator->get('Config');
		if(!isset($aConfiguration['asset_bundle']))throw new \Exception('AssetsBundle configuration is undefined');

		//Define base path
		if(($oRequest = $oServiceLocator->get('request')) instanceof \Zend\Http\PhpEnvironment\Request)$aConfiguration['asset_bundle']['basePath'] = $oRequest->getBasePath();
		$oService = new \AssetsBundle\Service\Service($aConfiguration['asset_bundle']);
		return $oService->setFilters(array(
			\AssetsBundle\Service\Service::ASSET_CSS => $oServiceLocator->get('CssFilter'),
			\AssetsBundle\Service\Service::ASSET_JS => $oServiceLocator->get('JsFilter'),
			\AssetsBundle\Service\Service::ASSET_LESS => $oServiceLocator->get('LessFilter')
		));
	}
}