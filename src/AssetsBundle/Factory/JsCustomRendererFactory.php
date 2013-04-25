<?php
namespace AssetsBundle\Factory;
class JsCustomRendererFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \AssetsBundle\View\Renderer\JsCustomRenderer
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oJsCustomRenderer = new \AssetsBundle\View\Renderer\JsCustomRenderer();
		return $oJsCustomRenderer->setServiceLocator($oServiceLocator);
	}
}