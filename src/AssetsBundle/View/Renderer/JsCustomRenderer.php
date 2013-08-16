<?php
namespace AssetsBundle\View\Renderer;
class JsCustomRenderer implements \Zend\View\Renderer\RendererInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface{

	/**
	 * @var \Zend\ServiceManager\ServiceLocatorInterface
	 */
	protected $serviceLocator;

	/**
     * @var \Zend\View\Resolver\ResolverInterface
     */
    protected $resolver;

    /**
     * Set service locator
     * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
     * @return \AssetsBundle\View\Strategy\JsCustomStrategy
     */
    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
    	$this->serviceLocator = $oServiceLocator;
    	return $this;
    }

    /**
     * Get service locator
     * @throws \LogicException
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator(){
    	if($this->serviceLocator instanceof \Zend\ServiceManager\ServiceLocatorInterface)return $this->serviceLocator;
    	throw new \LogicException('Service locator is undefined');
    }


    /**
     * @return \AssetsBundle\View\Renderer\JsRenderer
     */
    public function getEngine(){
        return $this;
    }

    /**
     * Set the resolver used to map a template name to a resource the renderer may consume.
     * @param \Zend\View\Resolver\ResolverInterface $oResolver
     * @return \AssetsBundle\View\Renderer\JsRenderer
     */
    public function setResolver(\Zend\View\Resolver\ResolverInterface $oResolver){
        $this->resolver = $oResolver;
        return $this;
    }

    /**
     * Renders js files contents
     * @see \Zend\View\Renderer\RendererInterface::render()
     * @param \Zend\View\Model\ViewModel $oViewModel
     * @param string $sValues
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \RuntimeException
     * @return string
     */
    public function render($oViewModel, $sValues = null){
        if(!($oViewModel instanceof \Zend\View\Model\ViewModel))throw new \InvalidArgumentException(sprintf(
        	'View Model expects an instance of \Zend\View\Model\ViewModel, "%s" given',
        	is_object($oViewModel)?get_class($oViewModel):gettype($oViewModel)
        ));
    	$aJsFiles = $oViewModel->getVariable('jsCustomFiles');
    	if(!is_array($aJsFiles))throw new \LogicException('JsFiles expects an array "'.gettype($aJsFiles).'" given');
		$sRetour = '';

		$oAssetsBundleService = $this->getServiceLocator()->get('AssetsBundleService');
		foreach($aJsFiles as $sJsFile){
			if(!is_string($sJsFile))throw new \LogicException('Js file expects a string, "'.gettype($sJsFile).'" given');
			elseif(!is_readable($sJsFilePath = $oAssetsBundleService->getOptions()->getRealPath($sJsFile)))throw new \LogicException('File "'.$sJsFile.'" is unreadable');
			elseif(strtolower(pathinfo($sJsFilePath,PATHINFO_EXTENSION)) === 'php'){
				ob_start();
				if(false === include $sJsFilePath)throw new \RuntimeException('Error appends while including asset file "'.$sJsFilePath.'"');
				$sContent = ob_get_clean();
			}
			elseif(($sContent = file_get_contents($sJsFilePath)) === false)throw new \RuntimeException('Unabled to get file contents from file "'.$sJsFilePath.'"');
			$sRetour .= $sContent.PHP_EOL;
		}
        return $sRetour;
    }
}
