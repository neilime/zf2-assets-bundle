<?php
namespace Neilime\AssetsBundle\View\Strategy;
class JsCustomStrategy implements \Zend\EventManager\ListenerAggregateInterface{
	const ACTION_JS_CUSTOM = 'jscustom';

	/**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var \Neilime\AssetsBundle\View\Renderer\JsRenderer
     */
    protected $renderer;

    /**
     * @var string
     */
    protected $action;

    /**
     * Constructor
     * @param \Neilime\AssetsBundle\View\Renderer\JsRenderer $oRenderer
     */
    public function __construct(\Neilime\AssetsBundle\View\Renderer\JsRenderer $oRenderer){
    	$this->renderer = $oRenderer;
    }

    /**
     * Attach the aggregate to the specified event manager
     * @param \Zend\EventManager\EventManagerInterface $oEvents
     * @param int $iPriority
     * @return void
     */
    public function attach(\Zend\EventManager\EventManagerInterface $oEvents, $iPriority = 1){
    	$this->listeners[] = $oEvents->attach(\Zend\View\ViewEvent::EVENT_RENDERER, array($this, 'selectRenderer'), $iPriority);
        $this->listeners[] = $oEvents->attach(\Zend\View\ViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'), $iPriority);
        $this->listeners['Application'] = \Zend\EventManager\StaticEventManager::getInstance()->attach('Application',\Zend\Mvc\MvcEvent::EVENT_DISPATCH,array($this,'setAction'));
    }

    /**
     * Detach aggregate listeners from the specified event manager
     * @param \Zend\EventManager\EventManagerInterface $oEvents
     * @return void
     */
    public function detach(\Zend\EventManager\EventManagerInterface $oEvents){
        if(\Zend\EventManager\StaticEventManager::getInstance()->detach('Application', $this->listeners['Application']))unset($this->listeners['Application']);
        foreach($this->listeners as $iIndex => $oListener){
            if($oEvents->detach($oListener))unset($this->listeners[$iIndex]);
        }
    }

    /**
     * Set current MVC action
     * @param \Zend\Mvc\MvcEvent $oEvent
     * @return \Neilime\AssetsBundle\View\Strategy\JsCustomStrategy
     */
    public function setAction(\Zend\Mvc\MvcEvent $oEvent){
    	$this->action = $oEvent->getRouteMatch()->getParam('action');
    	return $this;
    }


    /**
     * Check if JsRenderer has to be used (MVC action = self::ACTION_JS_CUSTOM)
     * @param \Zend\View\ViewEvent $oEvent
     * @throws \Exception
     * @return void|\Neilime\AssetsBundle\View\Renderer\JsRenderer
     */
    public function selectRenderer(\Zend\View\ViewEvent $oEvent){
		if($this->action === self::ACTION_JS_CUSTOM){
			$aJsFiles = $oEvent->getModel()->getVariable('jsFiles');
			if(!is_array($aJsFiles))throw new \Exception('JsFiles is not an array : '.gettype($aJsFiles));
			return $this->renderer;
		}
    	return;
    }

    /**
     * @param \Zend\View\ViewEvent $oEvent
     * @throws \Exception
     */
    public function injectResponse(\Zend\View\ViewEvent $oEvent){
    	if($oEvent->getRenderer() !== $this->renderer)return;
    	if(!is_string($sResult = $oEvent->getResult()))throw new \Exception('Result is not a string : '.gettype($sResult));
        //Inject javascript in the response
        $oEvent->getResponse()->setContent($sResult)->getHeaders()->addHeaderLine('content-type','text/javascript');
    }
}
