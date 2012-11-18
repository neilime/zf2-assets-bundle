<?php
namespace Neilime\AssetsBundle\Factory;
class LessFilterFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @throws \Exception
	 * @return \Neilime\AssetsBundle\Service\Filter\LessFilter
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$aConfiguration = $oServiceLocator->get('Config');
		if(!isset($aConfiguration['asset_bundle']))throw new \Exception('AssetsBundle configuration is undefined');
		return new \Neilime\AssetsBundle\Service\Filter\LessFilter($aConfiguration);
	}
}