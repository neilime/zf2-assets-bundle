<?php
namespace AssetsBundle\Factory;
class ServiceFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @throws \UnexpectedValueException
	 * @return \AssetsBundle\Service\Service
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$aConfiguration = $oServiceLocator->get('Config');
		if(!isset($aConfiguration['asset_bundle']))throw new \UnexpectedValueException('AssetsBundle configuration is undefined');
                
                $oRequest = $oServiceLocator->get('request');
                
		//Define base URL of the application
		if(!isset($aConfiguration['asset_bundle']['baseUrl'])){                       
			if($oRequest instanceof \Zend\Http\PhpEnvironment\Request)$aConfiguration['asset_bundle']['baseUrl'] = $oRequest->getBaseUrl();
			else{
				$oRequest = new \Zend\Http\PhpEnvironment\Request();
				$aConfiguration['asset_bundle']['baseUrl'] = $oRequest->getBaseUrl();
			}
		}
                
                //set the request uri
                if($oRequest instanceof \Zend\Http\PhpEnvironment\Request)$aConfiguration['asset_bundle']['requestUri'] = $oRequest->getUri()->toString();

		//Retrieve filters
		if(isset($aConfiguration['asset_bundle']['filters'])
		&& is_array($aConfiguration['asset_bundle']['filters']))foreach($aConfiguration['asset_bundle']['filters'] as $sFilterType => $oFilter){
			if(is_string($oFilter)){
				if($oServiceLocator->has($oFilter))$aConfiguration['asset_bundle']['filters'][$sFilterType] = $oServiceLocator->get($oFilter);
				elseif(class_exists($oFilter))$aConfiguration['asset_bundle']['filters'][$sFilterType] = new $oFilter();
			}
		}

		//Retrieve render strategies
		if(isset($aConfiguration['asset_bundle']['rendererToStrategy'])
		&& is_array($aConfiguration['asset_bundle']['rendererToStrategy']))foreach($aConfiguration['asset_bundle']['rendererToStrategy'] as $sRendererName => $oRendererStrategy){
			if(is_string($oRendererStrategy)){
				if($oServiceLocator->has($oRendererStrategy))$aConfiguration['asset_bundle']['rendererToStrategy'][$sRendererName] = $oServiceLocator->get($oRendererStrategy);
				elseif(class_exists($oRendererStrategy))$aConfiguration['asset_bundle']['rendererToStrategy'][$sRendererName] = new $oRendererStrategy();
			}
		}
		return \AssetsBundle\Service\Service::factory($aConfiguration['asset_bundle']);
	}
}