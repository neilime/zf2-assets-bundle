<?php
namespace AssetsBundle\Service;
class RenderStrategyManager extends \Zend\ServiceManager\AbstractPluginManager{

    /**
     * Default set of render strategies.
     * @var array
     */
    protected $invokableClasses = array(
        'zendviewrendererphprenderer'  => '\AssetsBundle\View\Strategy\ViewHelperStrategy',
        'zendviewrendererfeedrenderer' => '\AssetsBundle\View\Strategy\NoneStrategy',
        'zendviewrendererjsonrenderer' => '\AssetsBundle\View\Strategy\NoneStrategy'
    );

    /**
     * Validate the plugin. Checks that the filter loaded is an instance of \AssetsBundle\View\Strategy\StrategyInterface
     * @param mixed $oRenderStrategy
     * @throws \RuntimeException
     */
    public function validatePlugin($oRenderStrategy){
        if($oRenderStrategy instanceof \AssetsBundle\View\Strategy\StrategyInterface)return;
        throw new \RuntimeException(sprintf(
        	'Render strategy expects an instance of \AssetsBundle\View\Strategy\StrategyInterface, "%s" given',
            is_object($oRenderStrategy)?get_class($oRenderStrategy):is_scalar($oRenderStrategy)?$oRenderStrategy:gettype($oRenderStrategy)
        ));
    }
}