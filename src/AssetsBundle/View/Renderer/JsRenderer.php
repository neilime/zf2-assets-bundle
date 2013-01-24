<?php
namespace AssetsBundle\View\Renderer;
class JsRenderer implements \Zend\View\Renderer\RendererInterface{

	/**
     * @var \Zend\View\Resolver\ResolverInterface
     */
    protected $resolver;

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
     * @return string
     */
    public function render($oViewModel, $values = null){
        if(!($oViewModel instanceof \Zend\View\Model\ViewModel))throw new \Exception('View Model is not valid');
    	$aJsFiles = $oViewModel->getVariable('jsFiles');
    	if(!is_array($aJsFiles))throw new \Exception('JsFiles is not an array :'.gettype($aJsFiles));
		$sRetour = '';
		foreach($aJsFiles as $sJsFile){
			if(!file_exists($sJsFile))throw new \Exception('File not found : '.$sJsFile);
			if(($sContent = file_get_contents($sJsFile)) === false)throw new \Exception('Unabled to get file content : '.$sJsFile);
			$sRetour .= $sContent.PHP_EOL;
		}
        return $sRetour;
    }
}
