<?php
namespace Neilime\AssetsBundle\Factory;
class ServiceFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @throws \Exception
	 * @return \Neilime\AssetsBundle\Service\Service
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$aConfiguration = $oServiceLocator->get('Config');
		if(!isset($aConfiguration['asset_bundle']))throw new \Exception('AssetsBundle configuration is undefined');
		$oService = new \Neilime\AssetsBundle\Service\Service($aConfiguration['asset_bundle'], $oServiceLocator);
		return $oService->setFilters(array(
			\Neilime\AssetsBundle\Service\Service::ASSET_CSS => $oServiceLocator->get('CssFilter'),
			\Neilime\AssetsBundle\Service\Service::ASSET_JS => $oServiceLocator->get('JsFilter'),
			\Neilime\AssetsBundle\Service\Service::ASSET_LESS => $oServiceLocator->get('LessFilter')
		));
	}
}