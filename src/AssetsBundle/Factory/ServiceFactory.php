<?php
namespace AssetsBundle\Factory;
class ServiceFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @throws \UnexpectedValueException
	 * @return \AssetsBundle\Service\Service
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$aConfiguration = $oServiceLocator->get('Config');
		if(!isset($aConfiguration['asset_bundle']))throw new \UnexpectedValueException('AssetsBundle configuration is undefined');

		//Define base path
		if(!isset($aConfiguration['asset_bundle']['basePath'])){
			if(($oRequest = $oServiceLocator->get('request')) instanceof \Zend\Http\PhpEnvironment\Request)$aConfiguration['asset_bundle']['basePath'] = $oRequest->getBasePath();
			else{
				$oRequest = new \Zend\Http\PhpEnvironment\Request();
				$aConfiguration['asset_bundle']['basePath'] = $oRequest->getBasePath();
			}
		}

		//Retrieve filters
		if(isset($aConfiguration['asset_bundle']['filters'])
		&& is_array($aConfiguration['asset_bundle']['filters']))foreach($aConfiguration['asset_bundle']['filters'] as $sFilterType => $oFilter){
			if(is_string($oFilter)){
				if($oServiceLocator->has($oFilter))$aConfiguration['asset_bundle']['filters'][$sFilterType] = $oServiceLocator->get($oFilter);
				elseif(class_exists($oFilter))$aConfiguration['asset_bundle']['filters'][$sFilterType] = new $oFilter();
			}
		}
		return new \AssetsBundle\Service\Service($aConfiguration['asset_bundle']);
	}
}