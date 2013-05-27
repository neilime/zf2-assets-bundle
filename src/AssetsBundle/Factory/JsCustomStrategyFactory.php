<?php
namespace AssetsBundle\Factory;
class JsCustomStrategyFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \AssetsBundle\View\Strategy\JsCustomStrategy
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oJsCustomStrategy = new \AssetsBundle\View\Strategy\JsCustomStrategy();
		return $oJsCustomStrategy->setServiceLocator($oServiceLocator)->setRenderer($oServiceLocator->get('JsCustomRenderer'));
	}
}