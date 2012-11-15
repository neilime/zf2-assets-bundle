<?php
namespace Neilime\AssetsBundle\View\Strategy;
interface StrategyInterface{
	/**
	 * @param \Zend\View\Renderer\RendererInterface $oRenderer
	 */
	public function setRenderer(\Zend\View\Renderer\RendererInterface $oRenderer);
    
    /**
     * @return \Zend\View\Renderer\RendererInterface
     */
	public function getRenderer();

    /**
     * @param string $sBaseUrl
     * @return \Neilime\AssetsBundle\View\Strategy\StrategyInterface
     */
	public function setBaseUrl($sBaseUrl);

	/**
	 * @return string
	 */
	public function getBaseUrl();
}