<?php
namespace AssetsBundle\View\Strategy;
class JsCustomStrategy implements \Zend\EventManager\ListenerAggregateInterface, \Zend\ServiceManager\ServiceLocatorAwareInterface{
	const ACTION_JS_CUSTOM = 'jscustom';

	/**
	 * @var \Zend\ServiceManager\ServiceLocatorInterface
	 */
	protected $serviceLocator;

	/**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var \AssetsBundle\View\Renderer\JsCustomRenderer
     */
    protected $renderer;

    /**
     * @param \AssetsBundle\View\Renderer\JsCustomRenderer $oRenderer
     * @return \AssetsBundle\View\Strategy\JsCustomStrategy
     */
    public function setRenderer(\AssetsBundle\View\Renderer\JsCustomRenderer $oRenderer){
		$this->renderer = $oRenderer;
		return $this;
    }

    /**
     * @throws \LogicException
     * @return \AssetsBundle\View\Renderer\JsCustomRenderer
     */
    public function getRenderer(){
    	if($this->renderer instanceof \AssetsBundle\View\Renderer\JsCustomRenderer)return $this->renderer;
    	throw new \LogicException('Renderer is undefined');
    }

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
     * Attach the aggregate to the specified event manager
     * @param \Zend\EventManager\EventManagerInterface $oEvents
     * @param int $iPriority
     * @return void
     */
    public function attach(\Zend\EventManager\EventManagerInterface $oEvents, $iPriority = 1){
    	$this->listeners[] = $oEvents->attach(\Zend\View\ViewEvent::EVENT_RENDERER, array($this, 'selectRenderer'), $iPriority);
        $this->listeners[] = $oEvents->attach(\Zend\View\ViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'), $iPriority);
    }

    /**
     * Detach aggregate listeners from the specified event manager
     * @param \Zend\EventManager\EventManagerInterface $oEvents
     * @return void
     */
    public function detach(\Zend\EventManager\EventManagerInterface $oEvents){
        foreach($this->listeners as $iIndex => $oListener){
            if($oEvents->detach($oListener))unset($this->listeners[$iIndex]);
        }
    }

    /**
     * Check if JsCustomStrategy has to be used (MVC action = self::ACTION_JS_CUSTOM)
     * @param \Zend\View\ViewEvent $oEvent
     * @throws \LogicException
     * @return void|\AssetsBundle\View\Renderer\JsRenderer
     */
    public function selectRenderer(\Zend\View\ViewEvent $oEvent){
    	if(
    		$this->getServiceLocator()->has('router')
    		&& ($oRouter = $this->getServiceLocator()->get('router')) instanceof \Zend\Mvc\Router\RouteInterface
    		&& ($oRequest = $oEvent->getRequest()) instanceof \Zend\Http\Request
    		&& ($oRouteMatch = $oRouter->match($oRequest)) instanceof \Zend\Mvc\Router\RouteMatch
    		&& $oRouteMatch->getParam('action') === self::ACTION_JS_CUSTOM
    	){
    		if(!($oViewModel = $oEvent->getModel()) instanceof \Zend\View\Model\ViewModel)throw new \UnexpectedValueException(sprintf(
				'Event model expects an instance of "Zend\View\Model\ViewModel", "%s" given',
				is_object($oViewModel)?get_class($oViewModel):gettype($oViewModel)
			));
			elseif(($oException = $oViewModel->getVariable('exception')) instanceof \Exception)throw new \RuntimeException('An exception occured in view model', $oException->getCode(), $oException);

    		$aJsCustomFiles = $oEvent->getModel()->getVariable('jsCustomFiles');
    		if(!is_array($aJsCustomFiles))throw new \LogicException('jsCustomFiles expects an array, "'.gettype($aJsCustomFiles).'" given');
			return $this->getRenderer();
		}
    }

    /**
     * @param \Zend\View\ViewEvent $oEvent
     * @throws \UnexpectedValueException
     */
    public function injectResponse(\Zend\View\ViewEvent $oEvent){
    	if($oEvent->getRenderer() !== $this->getRenderer())return;
    	if(!is_string($sResult = $oEvent->getResult()))throw new \UnexpectedValueException('Result expects string, "'.gettype($sResult).'" given');
        //Inject javascript in the response
        $oEvent->getResponse()->setContent($sResult)->getHeaders()->addHeaderLine('content-type','text/javascript');
    }
}