<?php

namespace AssetsBundle\Service;

class Service {

    //Assets
    const ASSET_CSS = 'css';
    const ASSET_JS = 'js';
    const ASSET_LESS = 'less';
    const ASSET_MEDIA = 'media';

    /**
     * @var \AssetsBundle\Service\ServiceOptions
     */
    protected $options;

    /**
     * @var \AssetsBundle\Service\AssetsFilterManager
     */
    protected $assetsFilterManager;

    /**
     * @var \AssetsBundle\Service\RenderStrategyManager
     */
    protected $renderStrategyManager;

    /**
     * Clean assets configuration
     * @var array
     */
    protected $assetsConfiguration;

    /**
     * Constructor
     * @param \AssetsBundle\Service\ServiceOptions $oOptions
     * @throws \InvalidArgumentException
     */
    public function __construct(\AssetsBundle\Service\ServiceOptions $oOptions = null) {
        if ($oOptions) {
            $this->setOptions($oOptions);
        }
    }

    /**
     * Instantiate a AssetsBundle service
     * @param array|Traversable $aOptions
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\Service
     */
    public static function factory($aOptions) {
        if ($aOptions instanceof \Traversable) {
            $aOptions = \Zend\Stdlib\ArrayUtils::iteratorToArray($aOptions);
        } elseif (!is_array($aOptions)) {
            throw new \InvalidArgumentException(__METHOD__ . ' expects an array or Traversable object; received "' . (is_object($aOptions) ? get_class($aOptions) : gettype($aOptions)) . '"');
        }

        //Retrieve filters
        if (isset($aOptions['filters'])) {
            if (is_array($aOptions['filters'])) {
                $aFilters = $aOptions['filters'];
            } else {
                throw new \InvalidArgumentException('"Filters" option expects array, "' . gettype($aOptions['filters']) . '" given');
            }
            unset($aOptions['filters']);
        }

        //Retrieve render strategies
        if (isset($aOptions['rendererToStrategy'])) {
            if (is_array($aOptions['rendererToStrategy'])) {
                $aRendererToStrategy = $aOptions['rendererToStrategy'];
            } else {
                throw new \InvalidArgumentException('"Renderer to strategy" option expects array, "' . gettype($aOptions['rendererToStrategy']) . '" given');
            }
            unset($aOptions['rendererToStrategy']);
        }

        $oAssetsBundleService = new static(new \AssetsBundle\Service\ServiceOptions($aOptions));

        //Define filters
        if (!empty($aFilters)) {
            $oAssetsFilterManager = $oAssetsBundleService->getAssetsFilterManager();
            foreach ($aFilters as $sName => $oFilter) {
                $oAssetsFilterManager->setService($sName, $oFilter);
            }
        }

        //Define render strategies
        if (!empty($aRendererToStrategy)) {
            $oRenderStrategyManager = $oAssetsBundleService->getRenderStrategyManager();
            foreach ($aRendererToStrategy as $sName => $oRenderStrategy) {
                $oRenderStrategyManager->setService($sName, $oRenderStrategy);
            }
        }
        return $oAssetsBundleService;
    }

    /**
     * @param \AssetsBundle\Service\ServiceOptions $oOptions
     * @return \AssetsBundle\Service\Service
     */
    public function setOptions(\AssetsBundle\Service\ServiceOptions $oOptions) {
        $this->options = $oOptions;
        return $this;
    }

    /**
     * @throws \LogicException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function getOptions() {
        if ($this->options instanceof \AssetsBundle\Service\ServiceOptions) {
            return $this->options;
        }
        throw new \LogicException('Options are undefined');
    }

    /**
     * Set the assets filter manager
     * @param \AssetsBundle\Service\AssetsFilterManager $oAssetsFilterManager
     * @return \AssetsBundle\Service\Service
     */
    public function setAssetsFilterManager(\AssetsBundle\Service\AssetsFilterManager $oAssetsFilterManager) {
        $this->assetsFilterManager = $oAssetsFilterManager;
        return $this;
    }

    /**
     * Retrieve the assets filter manager. Lazy loads an instance if none currently set.
     * @return \AssetsBundle\Service\AssetsFilterManager
     */
    public function getAssetsFilterManager() {
        if (!$this->assetsFilterManager instanceof \AssetsBundle\Service\AssetsFilterManager) {
            $this->setAssetsFilterManager(new \AssetsBundle\Service\AssetsFilterManager());
        }
        return $this->assetsFilterManager;
    }

    /**
     * Set the render strategy manager
     * @param \AssetsBundle\Service\RenderStrategyManager $oRenderStrategyManager
     * @return \AssetsBundle\Service\Service
     */
    public function setRenderStrategyManager(\AssetsBundle\Service\RenderStrategyManager $oRenderStrategyManager) {
        $this->renderStrategyManager = $oRenderStrategyManager;
        return $this;
    }

    /**
     * Retrieve the render strategy manager. Lazy loads an instance if none currently set.
     * @return \AssetsBundle\Service\RenderStrategyManager
     */
    public function getRenderStrategyManager() {
        if (!$this->renderStrategyManager instanceof \AssetsBundle\Service\RenderStrategyManager) {
            $this->setRenderStrategyManager(new \AssetsBundle\Service\RenderStrategyManager());
        }
        return $this->renderStrategyManager;
    }

    /**
     * @return array
     */
    public function getAssetsConfiguration() {
        //Check if assets configuration is already set
        $sModuleName = $this->getOptions()->getModuleName();
        $sControllerName = $this->getOptions()->getControllerName();
        $sActionName = $this->getOptions()->getActionName();
        if (isset($this->assetsConfiguration[$sConfigurationKey = $sModuleName . '-' . $sControllerName . '-' . $sActionName])) {
            return $this->assetsConfiguration[$sConfigurationKey];
        }

        $aAssets = array(
            self::ASSET_CSS => array(),
            self::ASSET_LESS => array(),
            self::ASSET_JS => array(),
            self::ASSET_MEDIA => array()
        );

        //Common configuration
        $aCommonConfiguration = $this->getOptions()->getAssets();
        if (!empty($aCommonConfiguration[self::ASSET_CSS]) && is_array($aCommonConfiguration[self::ASSET_CSS])) {
            $aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS], $aCommonConfiguration[self::ASSET_CSS]);
        }
        if (!empty($aCommonConfiguration[self::ASSET_LESS]) && is_array($aCommonConfiguration[self::ASSET_LESS])) {
            $aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS], $aCommonConfiguration[self::ASSET_LESS]);
        }
        if (!empty($aCommonConfiguration[self::ASSET_JS]) && is_array($aCommonConfiguration[self::ASSET_JS])) {
            $aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS], $aCommonConfiguration[self::ASSET_JS]);
        }
        if (!empty($aCommonConfiguration[self::ASSET_MEDIA]) && is_array($aCommonConfiguration[self::ASSET_MEDIA])) {
            $aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA], $aCommonConfiguration[self::ASSET_MEDIA]);
        }

        //Module configuration
        if (isset($aCommonConfiguration[$sModuleName])) {
            $aModuleConfiguration = $aCommonConfiguration[$sModuleName];
            if (!empty($aModuleConfiguration[self::ASSET_CSS]) && is_array($aModuleConfiguration[self::ASSET_CSS])) {
                $aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS], $aModuleConfiguration[self::ASSET_CSS]);
            }
            if (!empty($aModuleConfiguration[self::ASSET_LESS]) && is_array($aModuleConfiguration[self::ASSET_LESS])) {
                $aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS], $aModuleConfiguration[self::ASSET_LESS]);
            }
            if (!empty($aModuleConfiguration[self::ASSET_JS]) && is_array($aModuleConfiguration[self::ASSET_JS])) {
                $aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS], $aModuleConfiguration[self::ASSET_JS]);
            }
            if (!empty($aModuleConfiguration[self::ASSET_MEDIA]) && is_array($aModuleConfiguration[self::ASSET_MEDIA])) {
                $aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA], $aModuleConfiguration[self::ASSET_MEDIA]);
            }

            //Controller configuration
            if (isset($aModuleConfiguration[$sControllerName])) {
                $aControllerConfiguration = $aModuleConfiguration[$sControllerName];
                if (!empty($aControllerConfiguration[self::ASSET_CSS]) && is_array($aControllerConfiguration[self::ASSET_CSS])) {
                    $aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS], $aControllerConfiguration[self::ASSET_CSS]);
                }
                if (!empty($aControllerConfiguration[self::ASSET_LESS]) && is_array($aControllerConfiguration[self::ASSET_LESS])) {
                    $aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS], $aControllerConfiguration[self::ASSET_LESS]);
                }
                if (!empty($aControllerConfiguration[self::ASSET_JS]) && is_array($aControllerConfiguration[self::ASSET_JS])) {
                    $aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS], $aControllerConfiguration[self::ASSET_JS]);
                }
                if (!empty($aControllerConfiguration[self::ASSET_MEDIA]) && is_array($aControllerConfiguration[self::ASSET_MEDIA])) {
                    $aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA], $aControllerConfiguration[self::ASSET_MEDIA]);
                }

                //Action configuration
                if (isset($aControllerConfiguration[$sActionName])) {
                    $aActionConfiguration = $aControllerConfiguration[$sActionName];
                    if (!empty($aActionConfiguration[self::ASSET_CSS]) && is_array($aActionConfiguration[self::ASSET_CSS])) {
                        $aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS], $aActionConfiguration[self::ASSET_CSS]);
                    }
                    if (!empty($aActionConfiguration[self::ASSET_LESS]) && is_array($aActionConfiguration[self::ASSET_LESS])) {
                        $aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS], $aActionConfiguration[self::ASSET_LESS]);
                    }
                    if (!empty($aActionConfiguration[self::ASSET_JS]) && is_array($aActionConfiguration[self::ASSET_JS])) {
                        $aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS], $aActionConfiguration[self::ASSET_JS]);
                    }
                    if (!empty($aActionConfiguration[self::ASSET_MEDIA]) && is_array($aActionConfiguration[self::ASSET_MEDIA])) {
                        $aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA], $aActionConfiguration[self::ASSET_MEDIA]);
                    }
                }
            }
        }

        $aAssets[self::ASSET_MEDIA] = $this->getValidAssets(array_unique($aAssets[self::ASSET_MEDIA]), self::ASSET_MEDIA);
        $aAssets[self::ASSET_LESS] = $this->getValidAssets(array_unique($aAssets[self::ASSET_LESS]), self::ASSET_LESS);
        $aAssets[self::ASSET_CSS] = $this->getValidAssets(array_unique(array_filter($aAssets[self::ASSET_CSS])), self::ASSET_CSS);
        $aAssets[self::ASSET_JS] = $this->getValidAssets(array_unique(array_filter($aAssets[self::ASSET_JS])), self::ASSET_JS);

        return $this->assetsConfiguration[$sConfigurationKey] = $aAssets;
    }

    /**
     * Check if assets configuration is the same as last saved configuration
     * @throws \RuntimeException
     * @return boolean
     */
    public function assetsConfigurationHasChanged(array $aAssetsType = null) {
        $aAssetsType = $aAssetsType ? array_unique($aAssetsType) : array(self::ASSET_CSS, self::ASSET_LESS, self::ASSET_JS, self::ASSET_MEDIA);

        //Configuration file
        if (($sConfigContent = file_get_contents($sConfigFilePath = $this->getConfigurationFilePath())) === false) {
            throw new \RuntimeException('Unable to get in file content from file "' . $sConfigFilePath . '"');
        }
        if (
                ($aConfig = json_decode($sConfigContent, true)) === false || !is_array($aConfig)
        ) {
            throw new \RuntimeException('Configuration is not a well formed json array "' . $sConfigContent . '"');
        }

        //Get assets configuration
        $aAssets = $this->getAssetsConfiguration();

        //Check if configuration has changed for each type of asset
        foreach ($aAssetsType as $sAssetType) {
            if (!$this->assetTypeExists($sAssetType)) {
                throw new \InvalidArgumentException('Asset type "' . $sAssetType . '" does not exist');
            }
            if (empty($aAssets[$sAssetType]) && !empty($aConfig[$sAssetType])) {
                return true;
            } elseif (!empty($aAssets[$sAssetType])) {
                if (empty($aConfig[$sAssetType])) {
                    return true;
                } elseif (
                        array_diff($aAssets[$sAssetType], $aConfig[$sAssetType]) || array_diff($aConfig[$sAssetType], $aAssets[$sAssetType])
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $sModuleName
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function moduleHasAssetConfiguration($sModuleName) {
        if (!is_string($sModuleName) || empty($sModuleName)) {
            throw new \InvalidArgumentException('Module name is not valid');
        }
        $aAssets = $this->getOptions()->getAssets();
        return isset($aAssets[$sModuleName]);
    }

    /**
     * @param string $sControllerName
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function controllerHasAssetConfiguration($sControllerName) {
        if (!is_string($sControllerName) || empty($sControllerName)) {
            throw new \InvalidArgumentException('Controller name is not valid');
        }
        $aUnwantedKeys = array(self::ASSET_CSS => true, self::ASSET_LESS => true, self::ASSET_JS => true, self::ASSET_MEDIA => true);
        foreach (array_diff_key($this->getOptions()->getAssets(), $aUnwantedKeys) as $aModuleConfig) {
            if (isset($aModuleConfig[$sControllerName])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $sActionName
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function actionHasAssetConfiguration($sActionName) {
        if (!is_string($sActionName) || empty($sActionName)) {
            throw new \InvalidArgumentException('Action name is not valid');
        }
        $aUnwantedKeys = array(self::ASSET_CSS => true, self::ASSET_LESS => true, self::ASSET_JS => true, self::ASSET_MEDIA => true);
        foreach (array_diff_key($this->getOptions()->getAssets(), $aUnwantedKeys) as $aModuleConfig) {
            foreach (array_diff_key($aModuleConfig, $aUnwantedKeys) as $aControllerConfig) {
                if (isset($aControllerConfig[$sActionName])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Retrieve cache file name for given module name, controller name and action name
     * @param string $sModuleName : (optionnal)
     * @param string $sControllerName : (optionnal)
     * @param string $sActionName : (optionnal)
     * @return string
     */
    public function getCacheFileName($sModuleName = null, $sControllerName = null, $sActionName = null) {
        $sModuleName = $sModuleName? : $this->getOptions()->getModuleName();
        $sControllerName = $sControllerName? : $this->getOptions()->getControllerName();
        $sActionName = $sActionName? : $this->getOptions()->getActionName();
        return md5(
                ($this->moduleHasAssetConfiguration($sModuleName) ? $sModuleName : \AssetsBundle\Service\ServiceOptions::NO_MODULE) .
                ($this->controllerHasAssetConfiguration($sControllerName) ? $sControllerName : \AssetsBundle\Service\ServiceOptions::NO_CONTROLLER) .
                ($this->actionHasAssetConfiguration($sActionName) ? $sActionName : \AssetsBundle\Service\ServiceOptions::NO_ACTION)
        );
    }

    /**
     * Retrieve configuration file name for given module name, controller name and action name
     * @param string $sModuleName : (optionnal)
     * @param string $sControllerName : (optionnal)
     * @param string $sActionName : (optionnal)
     * @return string
     */
    public function getConfigurationFilePath($sModuleName = null, $sControllerName = null, $sActionName = null) {
        return $this->getOptions()->getCachePath() . $this->getCacheFileName($sModuleName, $sControllerName, $sActionName) . '.conf';
    }

    /**
     * Retrieve assets realpath
     * @param array $aAssets
     * @param string $sAssetType
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return array
     */
    private function getValidAssets(array $aAssets, $sAssetType) {
        if (!self::assetTypeExists($sAssetType)) {
            throw new \InvalidArgumentException('Asset\'s type is undefined : ' . $sAssetType);
        }
        $aReturn = array();
        foreach ($aAssets as $sAssetPath) {

            //Attempt to retrieve asset's real path
            if (!($sRealAssetsPath = $this->getOptions()->getRealPath($sAssetPath))) {
                if (strpos($sAssetPath, '://') === false) {
                    throw new \InvalidArgumentException('Asset\'s file "' . $sAssetPath . '" does not exist');
                } elseif ($sAssetType === self::ASSET_LESS) {
                    throw new \InvalidArgumentException('Less assets does not support urls, "' . $sAssetPath . '" given');
                }

                if (($oFileHandle = @fopen($sAssetPath, 'r')) === false) {
                    throw new \RuntimeException('Unable to retrieve asset contents from url "' . $sAssetPath . '"');
                }
                fclose($oFileHandle);
                $sRealAssetsPath = $sAssetPath;
            }

            //Asset path is a directory
            if (is_dir($sRealAssetsPath)) {
                $aReturn = array_merge($aReturn, $this->getAssetsFromDirectory($sRealAssetsPath, $sAssetType));
            } else {
                $aReturn[] = $sRealAssetsPath;
            }
        }
        return array_unique(array_filter($aReturn));
    }

    /**
     * Retrieve assets from a directory
     * @param string $sDirPath
     * @param string $sAssetType
     * @throws \InvalidArgumentException
     * @return array
     */
    private function getAssetsFromDirectory($sDirPath, $sAssetType) {
        if (!is_string($sDirPath) || !($sDirPath = $this->getOptions()->getRealPath($sDirPath)) && !is_dir($sDirPath)) {
            throw new \InvalidArgumentException('Directory not found : ' . $sDirPath);
        }
        if (!self::assetTypeExists($sAssetType)) {
            throw new \Exception('Asset\'s type is undefined : ' . $sAssetType);
        }
        $oDirIterator = new \DirectoryIterator($sDirPath);
        $aAssets = array();
        $aMediasExt = $this->getOptions()->getMediaExt();
        $bRecursiveSearch = $this->getOptions()->allowsRecursiveSearch();
        foreach ($oDirIterator as $oFile) {
            if ($oFile->isFile()) {
                switch ($sAssetType) {
                    case self::ASSET_CSS:
                    case self::ASSET_JS:
                    case self::ASSET_LESS:
                        if (strtolower(pathinfo($oFile->getFilename(), PATHINFO_EXTENSION)) === $sAssetType) {
                            $aAssets[] = $oFile->getPathname();
                        }
                        break;
                    case self::ASSET_MEDIA:
                        if (in_array(
                                        $sExtension = strtolower(pathinfo($oFile->getFilename(), PATHINFO_EXTENSION)), $aMediasExt
                                )) {
                            $aAssets[] = $oFile->getPathname();
                        }
                        break;
                }
            } elseif ($oFile->isDir() && !$oFile->isDot() && $bRecursiveSearch) {
                $aAssets = array_merge(
                        $aAssets, $this->getAssetsFromDirectory($oFile->getPathname(), $sAssetType)
                );
            }
        }
        return $aAssets;
    }

    /**
     * Attempts to retrieve contents from asset file
     * @param string $sAssetPath
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return string
     */
    public function assetGetContents($sAssetPath) {
        if (!is_string($sAssetPath)) {
            throw new \InvalidArgumentException('Asset path expects string, "' . gettype($sAssetPath) . '" given');
        }
        if (!is_readable($sAssetPath)) {
            if (($oFileHandle = @fopen($sAssetPath, 'r')) === false) {
                throw new \InvalidArgumentException('Asset\'s file "' . $sAssetPath . '" does not exist');
            }
            $sAssetContents = '';
            while (($sContent = fgets($oFileHandle)) !== false) {
                $sAssetContents .= $sContent . PHP_EOL;
            }
            if (!feof($oFileHandle)) {
                throw new \RuntimeException('Unable to retrieve asset contents from file "' . $sAssetPath . '"');
            }
            fclose($oFileHandle);
        } elseif (strtolower(pathinfo($sAssetPath, PATHINFO_EXTENSION)) === 'php') {
            ob_start();
            if (false === include $sAssetPath) {
                throw new \RuntimeException('Error appends while including asset file "' . $sAssetPath . '"');
            }
            $sAssetContents = ob_get_clean();
        } elseif (($sAssetContents = file_get_contents($sAssetPath)) === false) {
            throw new \RuntimeException('Unable to retrieve asset contents from file "' . $sAssetPath . '"');
        }
        return $sAssetContents;
    }

    /**
     * Retrieve asset relative path
     * @param string $sAssetPath
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getAssetRelativePath($sAssetPath) {
        if (!($sAssetRealPath = $this->getOptions()->getRealPath($sAssetPath))) {
            throw new \InvalidArgumentException('File "' . $sAssetPath . '" does not exist');
        }

        //If asset is already a cache file
        $sCachePath = $this->getOptions()->getCachePath();
        return strpos($sAssetRealPath, $sCachePath) !== false ? str_ireplace(
                        array($sCachePath, '.less'), array('', '.css'), $sAssetRealPath
                ) : (
                $this->getOptions()->hasAssetsPath() ? str_ireplace(
                                array($this->getOptions()->getAssetsPath(), getcwd(), DIRECTORY_SEPARATOR), array('', '', '_'), $sAssetRealPath
                        ) : str_ireplace(
                                array(getcwd(), DIRECTORY_SEPARATOR), array('', '_'), $sAssetRealPath
                        )
                );
    }

    /**
     * Render assets
     * @throws \RuntimeException
     * @return \AssetsBundle\Service\Service
     */
    public function renderAssets() {
        //Retrieve cache file name
        $sCacheName = $this->getCacheFileName();

        //Production : check if cache files exist
        if ($this->getOptions()->isProduction()) {
            $sJsCacheFile = $sCacheName . '.' . self::ASSET_JS;
            $sCssCacheFile = $sCacheName . '.' . self::ASSET_CSS;           
            $sCachePath = $this->getOptions()->getCachePath();
            
            $cacheUrl = new \Zend\Uri\Uri($this->getOptions()->getCacheUrl());
            $cacheUrlHost = $cacheUrl->getHost();
            //cache is set with a full url
            if(!empty($cacheUrlHost)) {
                $requestUri = new \Zend\Uri\Uri($this->getOptions()->getRequestUri());
                $requestUriHost = $requestUri->getHost();
                //cacheUrlHost is different than the requestUriHost
                if($requestUriHost !== $cacheUrlHost) {
                    //render the assets as they may be in a cdn and not in the server
                    return $this
                                ->displayAssets(array($sCssCacheFile), self::ASSET_CSS)
                                ->displayAssets(array($sJsCacheFile), self::ASSET_JS);
                }
            }
            if (
                    $this->getOptions()->getRealPath($sCachePath . $sCssCacheFile) && $this->getOptions()->getRealPath($sCachePath . $sJsCacheFile)
            ) {
                return $this
                                ->displayAssets(array($sCssCacheFile), self::ASSET_CSS)
                                ->displayAssets(array($sJsCacheFile), self::ASSET_JS);
            }
        }
        
        //Retrieve assets configuration
        $aAssetsToRender = $aAssetsConfiguration = $this->getAssetsConfiguration();

        //Manage images caching
        $this->cacheMedias($aAssetsToRender[self::ASSET_MEDIA]);

        //Manage less files caching
        $aAssetsToRender[self::ASSET_CSS][] = $this->cacheLess($aAssetsToRender[self::ASSET_LESS], $sCacheName);

        //Manage css & js file caching
        $this->displayAssets(
                array_unique(array_filter(array_merge($this->cacheAssets(array_filter($aAssetsToRender[self::ASSET_CSS]), self::ASSET_CSS, $sCacheName)))), self::ASSET_CSS
        )->displayAssets(
                array_unique(array_filter(array_merge($this->cacheAssets(array_filter($aAssetsToRender[self::ASSET_JS]), self::ASSET_JS, $sCacheName)))), self::ASSET_JS
        );

        //Write assets configuration into configuration file
        if (!file_put_contents($sConfigFilePath = $this->getConfigurationFilePath(), json_encode($aAssetsConfiguration))) {
            throw new \RuntimeException('Unable to write in file "' . $sConfigFilePath . '"');
        }

        return $this;
    }

    /**
     * Optimise and cache "Css" & "Js" assets
     * @param array $aAssetsPath : files to cache
     * @param string $sAssetType : asset's type to cache (self::ASSET_CSS or self::ASSET_JS)
     * @param string $sCacheName : cache file name
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \RuntimeException
     * @return string
     */
    private function cacheAssets(array $aAssetsPath, $sAssetType, $sCacheName) {
        if (!is_array($aAssetsPath)) {
            throw new \InvalidArgumentException('AssetsPath expects an array, "' . gettype($aAssetsPath) . '" given');
        }
        if (!self::assetTypeExists($sAssetType)) {
            throw new \InvalidArgumentException('Asset\'s type "' . $sAssetType . '" is undefined');
        }
        if (!is_string($sCacheName)) {
            throw new \InvalidArgumentException('CacheName expects string, "' . gettype($aAssetsPath) . '" given');
        }
        if (empty($sCacheName)) {
            throw new \InvalidArgumentException('CacheName is empty');
        }

        $aReturn = array();

        //No assets to cache
        if (empty($aAssetsPath)) {
            return $aReturn;
        }

        //Production cache file
        $sCachePath = $this->getOptions()->getCachePath();
        $sCacheFile = $sCacheName . '.' . $sAssetType;
        $aCacheAssets = array();

        $bHasContent = false;
        $oAssetsFilterManager = $this->getAssetsFilterManager();
        foreach ($aAssetsPath as $sAssetPath) {
            //Reset time limit
            set_time_limit(0);

            //Developpement : don't optimize assets
            if (!$this->getOptions()->isProduction()) {

                if ($sAssetRealPath = $this->getOptions()->getRealPath($sAssetPath)) {
                    $sAssetRelativePath = $this->getAssetRelativePath($sAssetRealPath);

                    //Rewrite urls for CSS files
                    if ($sAssetType === self::ASSET_CSS && !preg_match('/\.less$/', $sAssetRealPath)) {
                        $sAssetContent = $this->assetGetContents($sAssetRealPath);
                        $aRewriteUrlCallback = array($this, 'rewriteUrl');
                        if (!file_put_contents($sCachePath . $sAssetRelativePath, preg_replace_callback(
                                                '/url\(([^\)]+)\)/', function($aMatches) use($aRewriteUrlCallback, $sAssetRealPath) {
                                            return call_user_func($aRewriteUrlCallback, $aMatches, $sAssetRealPath);
                                        }, $sAssetContent
                                ))) {
                            throw new \RuntimeException('Unable to write in file : ' . $sCachePath . $sAssetRelativePath);
                        }
                    } else {
                        $this->copyIntoCache($sAssetRealPath, $sCachePath . $sAssetRelativePath);
                    }
                    $aCacheAssets[] = $sAssetRelativePath;
                } else {
                    if (($oFileHandle = @fopen($sAssetPath, 'r')) === false) {
                        throw new \LogicException('Asset\'s file "' . $sAssetPath . '" does not exist');
                    }
                    fclose($oFileHandle);
                    $aCacheAssets[] = $sAssetPath;
                }
                continue;
            }

            //Absolute path
            if (!($sAssetRealPath = $this->getOptions()->getRealPath($sAssetPath))) {
                if (($oFileHandle = @fopen($sAssetPath, 'r')) === false) {
                    throw new \LogicException('Asset\'s file "' . $sAssetPath . '" does not exist');
                }
                $sAssetRealPath = $sAssetPath;
            }

            //Production : optimize assets
            $sAssetContent = $this->assetGetContents($sAssetRealPath);

            switch ($sAssetType) {
                case self::ASSET_CSS:
                    //Rewrite urls for CSS files
                    if (!preg_match('/\.less$/', $sAssetRealPath)) {
                        $aRewriteUrlCallback = array($this, 'rewriteUrl');
                        $sAssetContent = preg_replace_callback(
                                '/url\(([^\)]+)\)/', function($aMatches) use($aRewriteUrlCallback, $sAssetRealPath) {
                            return call_user_func($aRewriteUrlCallback, $aMatches, $sAssetRealPath);
                        }, $sAssetContent
                        );
                    }

                    $sCacheContent = trim($oAssetsFilterManager->has(self::ASSET_CSS) ? $oAssetsFilterManager->get(self::ASSET_CSS)->run($sAssetContent) : $sAssetContent);
                    break;
                case self::ASSET_JS:
                    $sCacheContent = trim($oAssetsFilterManager->has(self::ASSET_JS) ? $oAssetsFilterManager->get(self::ASSET_JS)->run($sAssetContent) : $sAssetContent) . PHP_EOL . '//' . PHP_EOL;
                    break;
            }
            $sCacheContent = trim($sCacheContent);
            if (empty($sCacheContent)) {
                continue;
            } else {
                $bHasContent = true;
            }
            $sCacheFilePath = $sCachePath . $sCacheFile;
            if (!file_put_contents($sCacheFilePath, $sCacheContent . PHP_EOL, FILE_APPEND)) {
                throw new \RuntimeException('Unable to write in file : ' . $sCacheFilePath);
            }
        }
        return $this->getOptions()->isProduction() ? ($bHasContent ? array($sCacheFile) : array()) : $aCacheAssets;
    }

    /**
     * Optimise and cache "Less" assets
     * @param array $aAssetsPath : assets to cache
     * @param string $sCacheName : cache file name
     * @throws \LogicException
     * @return string|null
     */
    private function cacheLess(array $aAssetsPath, $sCacheName) {
        //Create global import file for Less assets
        $sCachePath = $this->getOptions()->getCachePath();
        $sCacheFile = $sCacheName . '.' . self::ASSET_LESS;
        if (!$this->getOptions()->isProduction()) {
            $sCacheFile = 'dev_' . $sCacheFile;
        }

        //Allows service to store existing assets
        $aAssetsExists = array();

        //Check if cache file has to been updated
        if (
                is_readable($this->getConfigurationFilePath()) && !$this->assetsConfigurationHasChanged(array(self::ASSET_LESS)) && file_exists($sCachePath . $sCacheFile) && ($iLastModifiedCache = filemtime($sCachePath . $sCacheFile)) !== false
        ) {
            $bCacheOk = true;
            foreach ($aAssetsPath as $sAssetPath) {
                if (!($sAssetPath = $this->getOptions()->getRealPath($sAssetPath))) {
                    throw new \LogicException('File "' . $sAssetPath . '" does not exist');
                }
                $aAssetsExists[] = $sAssetPath;
                if (($iLastModified = filemtime($sAssetPath)) === false || $iLastModified > $iLastModifiedCache) {
                    $bCacheOk = false;
                    break;
                }
                //If file is up to date, check if it doesn't contain @imports
                else {
                    $sAssetContent = $this->assetGetContents($sAssetPath);

                    if (preg_match_all('/@import([^;]*);/', $sAssetContent, $aImports, PREG_PATTERN_ORDER)) {
                        $sAssetDirPath = realpath(pathinfo($sAssetPath, PATHINFO_DIRNAME)) . DIRECTORY_SEPARATOR;
                        foreach ($aImports[1] as $sImport) {
                            $sImport = trim(str_ireplace(array('"', '\'', 'url', '(', ')'), '', $sImport));
                            //Check if file to be imported exists
                            if (
                                    !($sImportPath = $this->getOptions()->getRealPath($sImport)) && !file_exists($sImportPath = $sAssetDirPath . $sImport) //Relative path to less file directory
                            ) {
                                throw new \LogicException('File "' . $sImportPath . '" referenced in "' . $sAssetPath . ' does not exists');
                            }
                            if (($iLastModified = filemtime($sImportPath)) === false || $iLastModified > $iLastModifiedCache) {
                                $bCacheOk = false;
                                break;
                            }
                        }
                        if (!$bCacheOk) {
                            break;
                        }
                    }
                }
            }
            if ($bCacheOk) {
                return $sCachePath . $sCacheFile;
            }
        }

        $sImportContent = '';
        foreach ($aAssetsPath as $sAssetPath) {
            //Absolute path
            if (!in_array($sAssetPath, $aAssetsExists) && !($sAssetPath = $this->getOptions()->getRealPath($sAssetPath))) {
                throw new \LogicException('File "' . $sAssetPath . '" does not exist');
            }
            $sImportContent .= '@import "' . str_ireplace(getcwd(), '', $sAssetPath) . '";' . PHP_EOL;
        }
        $sImportContent = trim($sImportContent);

        //Reset time limit
        set_time_limit(0);

        //If content is empty, stop rendering process
        $oAssetsFilterManager = $this->getAssetsFilterManager();
        if (
                empty($sImportContent) || ($oAssetsFilterManager->has(self::ASSET_LESS) && !($sImportContent = $oAssetsFilterManager->get(self::ASSET_LESS)->run($sImportContent)))
        ) {
            return null;
        }

        //Rewrite urls
        $sImportContent = preg_replace_callback(
                '/url\(([^\)]+)\)/', array($this, 'rewriteUrl'), $sImportContent
        );

        if (!file_put_contents($sCacheFile = $sCachePath . $sCacheFile, $sImportContent)) {
            throw new \LogicException('Unable to write in file "' . $sCacheFile . '"');
        }
        return $sCacheFile;
    }

    /**
     * Optimise and cache "Medias" assets
     * @param array $aMediasPath : medias to cache
     * @throws \LogicException
     * @return \AssetsBundle\Service\Service
     */
    private function cacheMedias(array $aMediasPath) {
        $oAssetsFilterManager = $this->getAssetsFilterManager();

        if ($bHasAssetsPath = $this->getOptions()->hasAssetsPath()) {
            $sAssetsPath = $this->getOptions()->getAssetsPath();
        }
        $sCachePath = $this->getOptions()->getCachePath();
        $aMediaExt = $this->getOptions()->getMediaExt();

        foreach ($aMediasPath as $sMediaPath) {
            //Absolute path
            if (!($sMediaPath = $this->getOptions()->getRealPath($sMediaPath))) {
                throw new \LogicException('File not found : ' . $sMediaPath);
            }

            //Check if media may be in "assets" defined directory or not
            $sMediaCachePath = $bHasAssetsPath ? preg_replace('/^' . preg_quote($sAssetsPath, '/') . '/', '', dirname($sMediaPath)) : dirname($sMediaPath);

            //In production mask assets files tree
            if ($this->getOptions()->isProduction()) {
                $sMediaCachePath = md5($sMediaCachePath);
            } else {
                $sMediaCachePath = self::sanitizeFileName($sMediaCachePath);
            }

            //Define media cache absolute path
            $sMediaCachePath = $sCachePath . $sMediaCachePath . DIRECTORY_SEPARATOR . basename($sMediaPath);

            //Media isn't cached or it's deprecated
            if ($this->hasToCache($sMediaPath, $sMediaCachePath)) {
                $sExtension = strtolower(pathinfo($sMediaPath, PATHINFO_EXTENSION));
                if (!in_array($sExtension, $aMediaExt)) {
                    throw new \LogicException('Extension is not valid (' . join(', ', $aMediaExt) . ') : ' . $sExtension);
                }

                $this->copyIntoCache($sMediaPath, $sMediaCachePath);

                //If filter is defined for extension
                if ($oAssetsFilterManager->has($sExtension)) {
                    $oAssetsFilterManager->get($sExtension)->run($sMediaCachePath);
                }
            }
        }
        return $this;
    }

    /**
     * Show assets through View Helper
     * @param array $aAssets
     * @throws \Exception
     * @return \AssetsBundle\Service\Service
     */
    public function displayAssets(array $aAssets, $sAssetType) {
        if (!self::assetTypeExists($sAssetType)) {
            throw new \InvalidArgumentException('Asset\'s type "' . $sAssetType . '" is undefined');
        }

        //Arbitrary last modified time in production
        $sLastModifiedTime = $this->getOptions()->isProduction() ? ($this->getOptions()->getLastModifiedTime()? : null) : null;

        //Retrieve rendering strategy
        $oRenderer = $this->getOptions()->getRenderer();
        $oStrategy = $this->getRenderStrategyManager()->get(get_class($oRenderer))->setBaseUrl($this->getOptions()->getCacheUrl())->setRenderer($oRenderer);

        foreach ($aAssets as $sAssetPath) {
            $oStrategy->renderAsset(
                    $sAssetPath, $sLastModifiedTime? : (file_exists($sAbsolutePath = $this->getOptions()->getCachePath() . DIRECTORY_SEPARATOR . $sAssetPath) ? filemtime($sAbsolutePath) : time()), $sAssetType
            );
        }
        return $this;
    }

    /**
     * Check if asset's type is valid
     * @param string $sAssetType
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public static function assetTypeExists($sAssetType) {
        if (!is_string($sAssetType)) {
            throw new \InvalidArgumentException('Asset type expects string, "' . gettype($sAssetType) . '" given');
        }
        switch ($sAssetType) {
            case self::ASSET_CSS:
            case self::ASSET_LESS:
            case self::ASSET_JS:
            case self::ASSET_MEDIA:
                return true;
            default:
                return false;
        }
    }

    /**
     * Check if a file is already cached and if it is up to date
     * @param string $sFilePath
     * @param string $sCachePath
     * @throws \InvalidArgumentException
     * @return boolean
     */
    private function hasToCache($sFilePath, $sCachePath) {
        if (!file_exists($sFilePath)) {
            throw new \InvalidArgumentException('File "' . $sFilePath . '" does not exist');
        }
        return
                !file_exists($sCachePath) || ($iLastModified = filemtime($sFilePath)) === false || ($iLastModifiedCompare = filemtime($sCachePath)) === false || $iLastModified > $iLastModifiedCompare;
    }

    /**
     * Allows service to move a file in cache directory, keeping the same directory structure
     * @param string $sFilePath
     * @param string $sCachePath
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \AssetsBundle\Service\Service
     */
    public function copyIntoCache($sFilePath, $sCachePath) {
        if (!file_exists($sFilePath)) {
            \InvalidArgumentException('File "' . $sFilePath . '" does not exist');
        }
        if (!$this->hasToCache($sFilePath, $sCachePath)) {
            return $this;
        }
        //Create directory structure if it doesn't exist in cache
        if (!is_dir($sDirPath = pathinfo($sCachePath, PATHINFO_DIRNAME))) {
            $sCurrentPath = $this->getOptions()->getCachePath();

            //Directory traversal
            foreach (explode(DIRECTORY_SEPARATOR, str_ireplace($sCurrentPath, '', $sDirPath)) as $sDirPathPart) {
                //Create current directory if it doesn't exist
                if ($sDirPathPart && !is_dir($sCurrentPath = $sCurrentPath . DIRECTORY_SEPARATOR . $sDirPathPart)) {
                    \Zend\Stdlib\ErrorHandler::start();

                    mkdir($sCurrentPath);
                    if ($oException = \Zend\Stdlib\ErrorHandler::stop()) {
                        throw new \RuntimeException('Error occured while copy into cache file "' . $sCachePath . '"', $oException->getCode(), $oException);
                    }
                }
            }
        }
        if (!copy($sFilePath, $sCachePath) || !file_exists($sCachePath)) {
            throw new \RuntimeException('Unable to create file : ' . $sCachePath);
        }
        return $this;
    }

    /**
     * @param string $sFilename
     * @return type
     */
    public static function sanitizeFileName($sFilename) {
        return preg_replace(array('/\s+/', '/[^a-zA-Z0-9\-]/', '/-+/', '/^-+/', '/-+$/'), array('-', '', '-', '', ''), $sFilename);
    }

    /**
     * Rewrite url to match with cache path if needed
     * @param array $aMatches
     * @param string $sAssetPath
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @return string
     */
    public function rewriteUrl(array $aMatches, $sAssetPath = null) {
        if (!isset($aMatches[1])) {
            throw new \InvalidArgumentException('Url match is not valid');
        }

        //Remove quotes & double quotes from url
        $sUrl = trim(str_ireplace(array('"', '\''), '', $aMatches[1]));

        //Url is absolute or an external links
        if (preg_match('/^\/|http/', $sUrl)) {
            return $aMatches[0];
        }

        //Url is a data image
        if (preg_match('/^data:image\//', $sUrl)) {
            return $aMatches[0];
        }

        //Split arguments
        if (strpos($sUrl, '?') !== false) {
            list($sUrl, $sArguments) = explode('?', $sUrl);
        }

        //Split anchor
        if (strpos($sUrl, '#') !== false) {
            list($sUrl, $sAnchor) = explode('#', $sUrl);
        }

        if ($bHasAssetsPath = $this->getOptions()->hasAssetsPath()) {
            $sAssetsPath = $this->getOptions()->getAssetsPath();
        }

        //Url with @zfAssetsPath
        if (strpos($sUrl, '@zfAssetsPath') !== false) {
            if (!$bHasAssetsPath) {
                throw new \LogicException($sUrl . ' contains "@zfAssetsPath", but "Assets path" option is undefined');
            }
            if (!file_exists($sUrlRealPath = str_ireplace('@zfAssetsPath', $sAssetsPath, $sUrl))) {
                throw new \LogicException('File not found : ' . $sUrlRealPath);
            }
            $sUrlRealPath = realpath($sUrlRealPath);
        }

        //Url with context path
        elseif (!is_null($sAssetPath)) {
            if (!is_string($sAssetPath)) {
                throw new \InvalidArgumentException('Asset path is not valid : ' . gettype($sAssetPath));
            }

            //Remote url
            if (!file_exists($sAssetPath)) {
                if (strpos($sAssetPath, '://') === false) {
                    throw new \InvalidArgumentException('File "' . $sAssetPath . '" does not exists');
                }
                return str_ireplace(
                        $sUrl, dirname($sAssetPath) . DIRECTORY_SEPARATOR . $sUrl, $aMatches[0]
                );
            } elseif (($sUrlRealPath = realpath(dirname($sAssetPath) . DIRECTORY_SEPARATOR . $sUrl)) === false) {
                $sUrlRealPath = $sUrl;
            }
        }

        //Absolute path url
        elseif (($sUrlRealPath = realpath(getcwd() . DIRECTORY_SEPARATOR . $sUrl)) === false) {
            throw new \LogicException('"' . $sUrl . '" is not a valid path');
        }

        $sCachePath = $this->getOptions()->getCachePath();

        //Check if media may be in "assets" defined directory or not
        $sMediaCachePath = $bHasAssetsPath ? preg_replace('/^' . preg_quote($sAssetsPath, '/') . '/', '', dirname($sUrlRealPath)) : dirname($sUrlRealPath);

        //In production mask assets files tree
        if ($this->getOptions()->isProduction()) {
            $sMediaCachePath = md5($sMediaCachePath);
        } else {
            $sMediaCachePath = self::sanitizeFileName($sMediaCachePath);
        }

        //Define media cache absolute path
        $sMediaCachePath = $sCachePath . $sMediaCachePath . DIRECTORY_SEPARATOR . basename($sUrlRealPath);

        //Assert file has been cached
        if (file_exists($sMediaCachePath)) {
            return str_ireplace(
                    $sUrl, $this->getOptions()->getCacheUrl() . str_ireplace(DIRECTORY_SEPARATOR, '/', ltrim(str_ireplace(
                                            $sCachePath, '', $sMediaCachePath
                                    ), DIRECTORY_SEPARATOR)) . (empty($sArguments) ? '' : '?' . $sArguments) . (empty($sAnchor) ? '' : '#' . $sAnchor), $aMatches[0]
            );
        }
        throw new \LogicException('"' . $sUrlRealPath . '" has not been cached as file "' . $sMediaCachePath . '"');
    }

}
