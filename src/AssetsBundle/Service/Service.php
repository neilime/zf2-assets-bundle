<?php

namespace AssetsBundle\Service;

class Service implements \Zend\EventManager\ListenerAggregateInterface
{

    /**
     * @var \AssetsBundle\Service\ServiceOptions
     */
    protected $options;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var \AssetsBundle\AssetFile\AssetFilesManager
     */
    protected $assetFilesManager;

    /**
     * @var \Zend\View\HelperPluginManager
     */
    protected $viewHelperPluginManager;

    /**
     * Constructor
     *
     * @param  \AssetsBundle\Service\ServiceOptions $oOptions
     * @throws \InvalidArgumentException
     */
    public function __construct(\AssetsBundle\Service\ServiceOptions $oOptions = null)
    {
        if ($oOptions) {
            $this->setOptions($oOptions);
        }
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $oEventManager
     * @return \AssetsBundle\Service\Service
     */
    public function attach(\Zend\EventManager\EventManagerInterface $oEventManager)
    {
        // Assets rendering
        $this->listeners[] = $oEventManager->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER, array($this, 'renderAssets'));

        // MVC errors
        $this->listeners += $oEventManager->attach(array(\Zend\Mvc\MvcEvent::EVENT_DISPATCH_ERROR, \Zend\Mvc\MvcEvent::EVENT_RENDER_ERROR), array($this, 'consoleError'));

        return $this;
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $oEventManager
     * @return \AssetsBundle\Service\Service
     */
    public function detach(\Zend\EventManager\EventManagerInterface $oEventManager)
    {
        foreach ($this->listeners as $iIndex => $oCallback) {
            if ($oEventManager->detach($oCallback)) {
                unset($this->listeners[$iIndex]);
            }
        }
        return $this;
    }

    /**
     * Render assets
     *
     * @param  \Zend\Mvc\MvcEvent $oEvent
     * @return \AssetsBundle\Service\Service
     */
    public function renderAssets(\Zend\Mvc\MvcEvent $oEvent)
    {

        // Retrieve service manager
        $oServiceManager = $oEvent->getApplication()->getServiceManager();

        // Check if asset should be rendered
        if (// Assert that request is an Http request
                !(($oRequest = $oEvent->getRequest()) instanceof \Zend\Http\Request)
                // Not an Ajax request
                || $oRequest->isXmlHttpRequest()
                // Renderer is PHP
                || !($oServiceManager->get('ViewRenderer') instanceof \Zend\View\Renderer\PhpRenderer)
        ) {
            return $this;
        }

        // Retrieve options
        $oOptions = $this->getOptions();

        // Define options from route match
        $oRouteMatch = $oEvent->getRouteMatch();
        if ($oRouteMatch instanceof \Zend\Mvc\Router\RouteMatch) {
            // Retrieve controller
            if ($sControllerName = $oRouteMatch->getParam('controller')) {
                $oControllerLoader = $oServiceManager->get('ControllerLoader');
                if ($oControllerLoader->has('ControllerLoader') && ($oController = $oControllerLoader->get($sControllerName))) {
                    $oOptions->setControllerName($sControllerName);
                    $sControllerClass = get_class($oController);
                    if ($sModuleName = substr($sControllerClass, 0, strpos($sControllerClass, '\\'))) {
                        $oOptions->setModuleName($sModuleName);
                    }
                }
            }

            if ($sActionName = $oRouteMatch->getParam('action')) {
                $oOptions->setActionName($sActionName);
            }
            // Assert that rendering should continue depends on route match
            if ($oOptions->isAssetsBundleDisabled()) {
                return $this;
            }
        }

        // Defined current view renderer
        $this->getOptions()->setRenderer($oServiceManager->get('ViewRenderer'));

        // Retrieve asset files manager
        $oAssetFilesManager = $this->getAssetFilesManager();

        // Render Css and Js assets
        $this->displayAssets(
            // Retrieve cached Css assets
                array_merge(
                    $oAssetFilesManager->getCachedAssetsFiles(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS),
                    // Retrieve cached Js assets
                    $oAssetFilesManager->getCachedAssetsFiles(\AssetsBundle\AssetFile\AssetFile::ASSET_JS)
                )
        );

        // Save current configuration
        $this->getAssetFilesManager()->getAssetFilesConfiguration()->saveAssetFilesConfiguration();

        return $this;
    }

    /**
     * Display assets through renderer
     *
     * @param  array $aAssetFiles
     * @return \AssetsBundle\Service\Service
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    protected function displayAssets(array $aAssetFiles)
    {
        // Retrieve options
        $oOptions = $this->getOptions();

        // Arbitrary last modified time in production
        $iLastModifiedTime = $oOptions->isProduction() ? $oOptions->getLastModifiedTime() : null;

        // Use to cache loaded plugins
        $aRendererPlugins = array();

        // Render asset files
        foreach ($aAssetFiles as $oAssetFile) {
            if (!($oAssetFile instanceof \AssetsBundle\AssetFile\AssetFile)) {
                throw new \InvalidArgumentException(sprintf(
                    'Asset file expects an instance of "AssetsBundle\AssetFile\AssetFile", "%s" given',
                    is_object($oAssetFile) ? get_class($oAssetFile) : gettype($oAssetFile)
                ));
            }

            switch ($sAssetFileType = $oAssetFile->getAssetFileType()) {
                case \AssetsBundle\AssetFile\AssetFile::ASSET_JS:
                    $oRendererPlugin = isset($aRendererPlugins[$sAssetFileType]) ? $aRendererPlugins[$sAssetFileType] : $aRendererPlugins[$sAssetFileType] = $oOptions->getViewHelperPluginForAssetFileType($sAssetFileType);
                    $oRendererPlugin->appendFile($oOptions->getAssetFileBaseUrl($oAssetFile, $iLastModifiedTime));
                    break;
                case \AssetsBundle\AssetFile\AssetFile::ASSET_CSS:
                    $oRendererPlugin = isset($aRendererPlugins[$sAssetFileType]) ? $aRendererPlugins[$sAssetFileType] : $aRendererPlugins[$sAssetFileType] = $oOptions->getViewHelperPluginForAssetFileType($sAssetFileType);
                    $oRendererPlugin->appendStylesheet($oOptions->getAssetFileBaseUrl($oAssetFile, $iLastModifiedTime), 'all');
                    break;
                default:
                    throw new \DomainException('Asset\'s type "' . gettype($oAssetFile->getAssetFileType()) . '" can not be rendering as asset');
            }
        }
        return $this;
    }

    /**
     * Display errors to the console, if an error appends during a ToolsController action
     *
     * @param \Zend\Mvc\MvcEvent $oEvent
     */
    public function consoleError(\Zend\Mvc\MvcEvent $oEvent)
    {
        if (($oRequest = $oEvent->getRequest()) instanceof \Zend\Console\Request && $oRequest->getParam('controller') === 'AssetsBundle\Controller\Tools'
        ) {
            $oConsole = $oEvent->getApplication()->getServiceManager()->get('console');
            $oConsole->writeLine(PHP_EOL . '======================================================================', \Zend\Console\ColorInterface::GRAY);
            $oConsole->writeLine('An error occured', \Zend\Console\ColorInterface::RED);
            $oConsole->writeLine('======================================================================', \Zend\Console\ColorInterface::GRAY);

            if (!($oException = $oEvent->getParam('exception')) instanceof \Exception) {
                $oException = new \RuntimeException($oEvent->getError());
            }
            $oConsole->writeLine($oException . PHP_EOL);
        }
    }

    /**
     * @param \AssetsBundle\Service\ServiceOptions $oOptions
     * @return \AssetsBundle\Service\Service
     */
    public function setOptions(\AssetsBundle\Service\ServiceOptions $oOptions)
    {
        $this->options = $oOptions;
        if (isset($this->assetFilesManager)) {
            $this->getAssetFilesManager()->setOptions($this->options);
        }
        return $this;
    }

    /**
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function getOptions()
    {
        if (!($this->options instanceof \AssetsBundle\Service\ServiceOptions)) {
            $this->setOptions(new \AssetsBundle\Service\ServiceOptions());
        }
        return $this->options;
    }

    /**
     * @param \AssetsBundle\AssetFile\AssetFilesManager $oAssetFilesManager
     * @return \AssetsBundle\Service\Service
     */
    public function setAssetFilesManager(\AssetsBundle\AssetFile\AssetFilesManager $oAssetFilesManager)
    {
        $this->assetFilesManager = $oAssetFilesManager->setOptions($this->getOptions());
        return $this;
    }

    /**
     * @return \AssetsBundle\AssetFile\AssetFilesManager
     */
    public function getAssetFilesManager()
    {
        if (!($this->assetFilesManager instanceof \AssetsBundle\AssetFile\AssetFilesManager)) {
            $this->setAssetFilesManager(new \AssetsBundle\AssetFile\AssetFilesManager());
        }
        return $this->assetFilesManager;
    }
}
