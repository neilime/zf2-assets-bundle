<?php
namespace AssetsBundle\Factory;
class ViewHelperStrategyFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \AssetsBundle\View\Strategy\JsCustomStrategy
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oViewHelperStrategy = new \AssetsBundle\View\Strategy\JsCustomStrategy();
		return $oViewHelperStrategy->setServiceLocator($oServiceLocator)->setRenderer($oServiceLocator->get('JsCustomRenderer'));
	}
}