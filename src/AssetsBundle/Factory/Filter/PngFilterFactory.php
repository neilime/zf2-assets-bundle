<?php
namespace AssetsBundle\Factory\Filter;
class PngFilterFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @return \AssetsBundle\Service\Filter\PngFilter
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		return new \AssetsBundle\Service\Filter\PngFilter();
	}
}