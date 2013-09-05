<?php
namespace AssetsBundle;
class Module implements
	\Zend\ModuleManager\Feature\ConfigProviderInterface,
	\Zend\ModuleManager\Feature\AutoloaderProviderInterface,
	\Zend\ModuleManager\Feature\ConsoleUsageProviderInterface{

	/**
	 * @param \Zend\EventManager\EventInterface $oEvent
	 */
	public function onBootstrap(\Zend\EventManager\EventInterface $oEvent){
		$oServiceManager = $oEvent->getApplication()->getServiceManager();
		if(
			($oRequest = $oEvent->getRequest()) instanceof \Zend\Http\Request
			&& !$oRequest->isXmlHttpRequest()
			&& $oServiceManager->get('ViewRenderer') instanceof \Zend\View\Renderer\PhpRenderer
		)$oEvent->getApplication()->getEventManager()->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER, array($this, 'renderAssets'), 32);

		//Catch MVC errors
		$oEvent->getApplication()->getEventManager()->attach(
			array(\Zend\Mvc\MvcEvent::EVENT_DISPATCH_ERROR,\Zend\Mvc\MvcEvent::EVENT_RENDER_ERROR),
			array($this,'consoleError')
		);
	}

	/**
	 * @param \Zend\Mvc\MvcEvent $oEvent
	 */
	public function renderAssets(\Zend\Mvc\MvcEvent $oEvent){
		$oAssetsBundleService = $oEvent->getApplication()->getServiceManager()->get('AssetsBundleService');
		$oAssetsBundleService->getOptions()->setRenderer($oEvent->getApplication()->getServiceManager()->get('ViewRenderer'));

		/* @var $oRouter \Zend\Mvc\Router\RouteMatch */
		$oRouter = $oEvent->getRouteMatch();
		if($oRouter instanceof \Zend\Mvc\Router\RouteMatch){
			if($sControllerName = $oRouter->getParam('controller'))$sModuleName = current(explode('\\',ltrim($sControllerName,'\\')));
			$sActionName = $oRouter->getParam('action');
			$oOptions = $oAssetsBundleService->getOptions()
				->setControllerName($sControllerName);
			if(!empty($sActionName))$oOptions->setActionName($sActionName);
			if(!empty($sModuleName))$oOptions->setModuleName($sModuleName);
		}
		$oAssetsBundleService->renderAssets();
	}

	/**
	 * Display errors to the console, if an error appends during a ToolsController action
	 * @param \Zend\Mvc\MvcEvent $oEvent
	 */
	public function consoleError(\Zend\Mvc\MvcEvent $oEvent){
		if(
			($oRequest = $oEvent->getRequest()) instanceof \Zend\Console\Request
			&& $oRequest->getParam('controller') === 'AssetsBundle\Controller\Tools'
		){
			$oConsole = $oEvent->getApplication()->getServiceManager()->get('console');
			$oConsole->writeLine(PHP_EOL.'======================================================================', \Zend\Console\ColorInterface::GRAY);
			$oConsole->writeLine('An error occured', \Zend\Console\ColorInterface::RED);
			$oConsole->writeLine('======================================================================', \Zend\Console\ColorInterface::GRAY);

			if(!($oException = $oEvent->getParam('exception')) instanceof \Exception)$oException = new \RuntimeException($oEvent->getError());
			$oConsole->writeLine($oException.PHP_EOL);
		}
	}

	/**
	 * @param \Zend\Console\Adapter\AdapterInterface $oConsole
	 * @return string
	 */
	public function getConsoleBanner(\Zend\Console\Adapter\AdapterInterface $oConsole){
		return 'AssetsBundle - Command line Tool';
	}

	/**
	 * @see \Zend\ModuleManager\Feature\ConsoleUsageProviderInterface::getConsoleUsage()
	 * @param \Zend\Console\Adapter\AdapterInterface $oConsole
	 * @return array
	 */
	public function getConsoleUsage(\Zend\Console\Adapter\AdapterInterface $oConsole){
		return array(
			'Rendering assets:',
			'render' => 'render all assets',
			'Empty cache:',
			'empty' => 'empty cache directory'
		);
	}

	/**
	 * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
	 * @return array
	 */
	public function getAutoloaderConfig(){
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__.DIRECTORY_SEPARATOR.'autoload_classmap.php'
            )
        );
    }

    /**
     * @return array
     */
    public function getConfig(){
        return include __DIR__.DIRECTORY_SEPARATOR.'config/module.config.php';
    }
}