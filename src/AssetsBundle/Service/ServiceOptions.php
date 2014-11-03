<?php

namespace AssetsBundle\Service;

class ServiceOptions extends \Zend\Stdlib\AbstractOptions {

    const NO_MODULE = 'no_module';
    const NO_ACTION = 'no_action';
    const NO_CONTROLLER = 'no_controller';

    /**
     * Application environment (Developpement => false)
     * @var boolean
     */
    protected $production;

    /**
     * Arbitrary last modified time in production
     * @var scalable|null
     */
    protected $lastModifiedTime;

    /**
     * Cache directory absolute path
     * @var string
     */
    protected $cachePath;

    /**
     * Assets directory absolute path (allows you to define relative path for assets config)
     * @var string
     */
    protected $assetsPath;

    /**
     * Temp directory absolute path
     * @var string
     */
    protected $tmpDirPath;

    /**
     * Processed files directory absolute path
     * @var string
     */
    protected $processedDirPath;

    /**
     * Base URL of the application
     * @var string
     */
    protected $baseUrl = '';

    /**
     * Cache directory base url
     * @var string
     */
    protected $cacheUrl;

    /**
     * Media extensions to be cached
     * @var array
     */
    protected $mediaExt;

    /**
     * Allows search for matching assets in required folder and its subfolders
     * @var boolean
     */
    protected $recursiveSearch;

    /**
     * Required assets
     * @var array
     */
    protected $assets = array();

    /**
     * Assets renderer
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * Current module name
     * @var string
     */
    protected $moduleName = self::NO_MODULE;

    /**
     * Current controller name
     * @var string
     */
    protected $controllerName = self::NO_CONTROLLER;

    /**
     * Current action name
     * @var string
     */
    protected $actionName = self::NO_ACTION;

    /**
     * Disabled contexts
     * @var array
     */
    protected $disabledContexts = array();

    /**
     * View helper plugins by asset file types
     * @var array
     */
    protected $view_helper_plugins = array();

    /**
     * Store resolved real paths
     * @var array
     */
    protected $resolvedPaths = array();

    /**
     * @param boolean $bProduction
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setProduction($bProduction) {
        if (is_bool($bProduction)) {
            $this->production = $bProduction;
            return $this;
        }
        throw new \InvalidArgumentException('"Production" option expects a boolean, "' . gettype($bProduction) . '" given');
    }

    /**
     * @throws \LogicException
     * @return boolean
     */
    public function isProduction() {
        if (is_bool($this->production)) {
            return $this->production;
        }
        throw new \LogicException('"Production" option is undefined');
    }

    /**
     * @param scalable|null $sLastModifiedTime
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setLastModifiedTime($sLastModifiedTime = null) {
        if (is_scalar($sLastModifiedTime) || is_null($sLastModifiedTime)) {
            $this->lastModifiedTime = $sLastModifiedTime;
            return $this;
        }
        throw new \InvalidArgumentException('"Last modified time" option expects a scalable value, "' . gettype($sLastModifiedTime) . '" given');
    }

    /**
     * @throws \LogicException
     * @return scalable|null
     */
    public function getLastModifiedTime() {
        if (is_scalar($this->lastModifiedTime) || is_null($this->lastModifiedTime)) {
            return $this->lastModifiedTime;
        }
        throw new \LogicException('"Last modified time" option is undefined');
    }

    /**
     * @param string $sCachePath
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setCachePath($sCachePath) {
        if (is_string($sCachePath)) {
            if (is_dir($sRealCachePath = $this->getRealPath($sCachePath))) {
                if (is_writable($sRealCachePath)) {
                    $this->cachePath = $sRealCachePath;
                    return $this;
                }
                throw new \InvalidArgumentException('Cache path directory "' . $sRealCachePath . '" is not writable');
            }
            throw new \InvalidArgumentException('Cache path option expects a valid directory path, "' . $sCachePath . '" given');
        }
        throw new \InvalidArgumentException('"Cache path" option expects a string, "' . gettype($sCachePath) . '" given');
    }

    /**
     * @throws \LogicException
     * @return string
     */
    public function getCachePath() {
        if (is_string($this->cachePath)) {
            return $this->cachePath;
        }
        throw new \LogicException('"Cache path" option is undefined');
    }

    /**
     * @param string|null $sAssetsPath
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setAssetsPath($sAssetsPath = null) {
        if (!is_string($sAssetsPath) && !is_null($sAssetsPath)) {
            throw new \InvalidArgumentException('"Assets path" option expects a string or null, "' . gettype($sAssetsPath) . '" given');
        }
        if (is_null($sAssetsPath)) {
            $this->assetsPath = null;
        } elseif (is_dir($sAssetsRealPath = $this->getRealPath($sAssetsPath))) {
            $this->assetsPath = $sAssetsRealPath;
        } else {
            throw new \InvalidArgumentException('"assetsPath" config expects a valid directory path, "' . $sAssetsRealPath . '" given');
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasAssetsPath() {
        return is_string($this->assetsPath);
    }

    /**
     * @throws \LogicException
     * @return string
     */
    public function getAssetsPath() {
        if ($this->hasAssetsPath()) {
            return $this->assetsPath;
        }
        throw new \LogicException('"Assets path" option is undefined');
    }

    /**
     * @param string $sTmpDirPath
     * @return \AssetsBundle\Service\ServiceOptions
     * @throws \InvalidArgumentException
     */
    public function setTmpDirPath($sTmpDirPath) {
        if (!is_string($sTmpDirPath)) {
            throw new \InvalidArgumentException('"Temp dir path" option expects a string, "' . gettype($sTmpDirPath) . '" given');
        }
        if (($sTmpDirRealPath = $this->getRealPath($sTmpDirPath)) && is_dir($sTmpDirRealPath) && is_writable($sTmpDirRealPath)) {
            $this->tmpDirPath = $sTmpDirRealPath;
            return $this;
        }
        throw new \InvalidArgumentException('"Temp dir path" option "' . $sTmpDirPath . '" is not writable directory path');
    }

    /**
     * @return string
     * @throws \LogicException
     */
    public function getTmpDirPath() {
        if (is_dir($this->tmpDirPath) && is_writable($this->tmpDirPath)) {
            return $this->tmpDirPath;
        }
        throw new \LogicException('"Temp dir path" option is undefined');
    }

    /**
     * @param string $sProcessedDirPath
     * @return \AssetsBundle\Service\ServiceOptions
     * @throws \InvalidArgumentException
     */
    public function setProcessedDirPath($sProcessedDirPath) {
        if (!is_string($sProcessedDirPath)) {
            throw new \InvalidArgumentException('"Processed dir path" option expects a string, "' . gettype($sProcessedDirPath) . '" given');
        }
        if (($sProcessedDirRealPath = $this->getRealPath($sProcessedDirPath)) && is_dir($sProcessedDirRealPath) && is_writable($sProcessedDirRealPath)) {
            $this->processedDirPath = $sProcessedDirRealPath;
            return $this;
        }
        throw new \InvalidArgumentException('"Processed dir path" option "' . $sProcessedDirPath . '" is not writable directory path');
    }

    /**
     * @return string
     * @throws \LogicException
     */
    public function getProcessedDirPath() {
        if (is_dir($this->processedDirPath) && is_writable($this->processedDirPath)) {
            return $this->processedDirPath;
        }
        throw new \LogicException('"Processed dir path" option is undefined');
    }

    /**
     * @param string $sBaseUrl
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setBaseUrl($sBaseUrl) {
        if (is_string($sBaseUrl)) {
            $this->baseUrl = rtrim($sBaseUrl, '/');
            return $this;
        }
        throw new \InvalidArgumentException('"Base url" option expects a string, "' . gettype($sBaseUrl) . '" given');
    }

    /**
     * @throws \LogicException
     * @return string
     */
    public function getBaseUrl() {
        if (is_string($this->baseUrl)) {
            return $this->baseUrl;
        }
        throw new \LogicException('"Base url" option is undefined');
    }

    /**
     * @param string $sCacheUrl
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setCacheUrl($sCacheUrl) {
        if (is_string($sCacheUrl)) {
            if (strpos($sCacheUrl, '@zfBaseUrl') !== false) {
                $sCacheUrl = $this->getBaseUrl() . '/' . ltrim(str_ireplace('@zfBaseUrl', '', $sCacheUrl), '/');
            }
            $this->cacheUrl = $sCacheUrl;
            return $this;
        }
        throw new \InvalidArgumentException('"Cache url" option expects a string, "' . gettype($sCacheUrl) . '" given');
    }

    /**
     * @throws \LogicException
     * @return string
     */
    public function getCacheUrl() {
        if (is_string($this->cacheUrl)) {
            return $this->cacheUrl;
        }
        throw new \LogicException('"Cache url" option is undefined');
    }

    /**
     * @param array $aMediaExt
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setMediaExt(array $aMediaExt) {
        $this->mediaExt = array();
        foreach (array_unique($aMediaExt) as $sMediaExt) {
            if (empty($sMediaExt)) {
                throw new \InvalidArgumentException('Media extension is empty');
            }
            if (is_string($sMediaExt)) {
                $this->mediaExt[] = $sMediaExt;
            } else {
                throw new \InvalidArgumentException('"Media extension" expects a string, "' . gettype($sMediaExt) . '" given');
            }
        }
        return $this;
    }

    /**
     * @throws \LogicException
     * @return array
     */
    public function getMediaExt() {
        if (is_array($this->mediaExt)) {
            return $this->mediaExt;
        }
        throw new \LogicException('"Media extensions" option is undefined');
    }

    /**
     * @param boolean $bRecursiveSearch
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setRecursiveSearch($bRecursiveSearch) {
        if (is_bool($bRecursiveSearch)) {
            $this->recursiveSearch = $bRecursiveSearch;
            return $this;
        }
        throw new \InvalidArgumentException('"Recursive search" option expects a boolean, "' . gettype($bRecursiveSearch) . '" given');
    }

    /**
     * @throws \LogicException
     * @return boolean
     */
    public function allowsRecursiveSearch() {
        if (is_bool($this->recursiveSearch)) {
            return $this->recursiveSearch;
        }
        throw new \LogicException('"Recursive search" option is undefined');
    }

    /**
     * @param array $aAssets
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setAssets(array $aAssets) {
        $this->assets = $aAssets;
        return $this;
    }

    /**
     * @throws \LogicException
     * @return array
     */
    public function getAssets() {
        if (is_array($this->assets)) {
            return $this->assets;
        }
        throw new \LogicException('"Assets" option is undefined');
    }

    /**
     * @param \Zend\View\Renderer\RendererInterface $oRenderer
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setRenderer(\Zend\View\Renderer\RendererInterface $oRenderer) {
        $this->renderer = $oRenderer;
        return $this;
    }

    /**
     * @throws \LogicException
     * @return \Zend\View\Renderer\RendererInterface
     */
    public function getRenderer() {
        if ($this->renderer instanceof \Zend\View\Renderer\RendererInterface) {
            return $this->renderer;
        }
        throw new \LogicException('"Renderer" option is undefined');
    }

    /**
     * @param string $sModuleName
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setModuleName($sModuleName) {
        if (empty($sModuleName)) {
            throw new \InvalidArgumentException('"Module name" option is empty');
        }
        if (!is_string($sModuleName)) {
            throw new \InvalidArgumentException('"Module name" option expects a string, "' . gettype($sModuleName) . '" given');
        }
        $this->moduleName = $sModuleName;
        return $this;
    }

    /**
     * @throws \LogicException
     * @return string
     */
    public function getModuleName() {
        if (is_string($this->moduleName)) {
            return $this->moduleName;
        }
        throw new \LogicException('"Module name" option is undefined');
    }

    /**
     * @param string $sControllerName
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setControllerName($sControllerName) {
        if (empty($sControllerName)) {
            throw new \InvalidArgumentException('"Controller name" option is empty');
        }
        if (!is_string($sControllerName)) {
            throw new \InvalidArgumentException('"Controller name" option expects a string, "' . gettype($sControllerName) . '" given');
        }
        $this->controllerName = $sControllerName;
        return $this;
    }

    /**
     * @throws \LogicException
     * @return string
     */
    public function getControllerName() {
        if (is_string($this->controllerName)) {
            return $this->controllerName;
        }
        throw new \LogicException('"Controller name" option is undefined');
    }

    /**
     * @param string $sActionName
     * @throws \InvalidArgumentException
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setActionName($sActionName) {
        if (empty($sActionName)) {
            throw new \InvalidArgumentException('"Action name" option is empty');
        }
        if (!is_string($sActionName)) {
            throw new \InvalidArgumentException('"Action name" option expects a string, "' . gettype($sActionName) . '" given');
        }
        $this->actionName = $sActionName;
        return $this;
    }

    /**
     * @throws \LogicException
     * @return string
     */
    public function getActionName() {
        if (is_string($this->actionName)) {
            return $this->actionName;
        }
        throw new \LogicException('"Action name" option is undefined');
    }

    /**
     * @param array $aDisabledContexts
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setDisabledContexts(array $aDisabledContexts) {
        $this->disabledContexts = $aDisabledContexts;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAssetsBundleDisabled() {
        if (isset($this->disabledContexts[$sModuleName = $this->getModuleName()])) {
            if (is_bool($this->disabledContexts[$sModuleName])) {
                return $this->disabledContexts[$sModuleName];
            }
            if (isset($this->disabledContexts[$sModuleName][$sControllerName = $this->getControllerName()])) {
                if (is_bool($this->disabledContexts[$sModuleName][$sControllerName])) {
                    return $this->disabledContexts[$sModuleName][$sControllerName];
                }
                if (isset($this->disabledContexts[$sModuleName][$sControllerName][$sActionName = $this->getActionName()])) {
                    if (is_bool($this->disabledContexts[$sModuleName][$sControllerName][$sActionName])) {
                        return $this->disabledContexts[$sModuleName][$sControllerName][$sActionName];
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param array $aViewHelperPlugins
     * @return \AssetsBundle\Service\ServiceOptions
     */
    public function setViewHelperPlugins(array $aViewHelperPlugins) {
        $this->view_helper_plugins = $aViewHelperPlugins;
        return $this;
    }

    /**
     * @return array
     * @throws \LogicException
     */
    public function getViewHelperPlugins() {
        if (is_array($this->view_helper_plugins)) {
            return $this->view_helper_plugins;
        }
        throw new \LogicException('View helper plugins are undefined');
    }

    /**
     * @param string $sAssetFileType
     * @return \Zend\View\Helper\HelperInterface
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function getViewHelperPluginForAssetFileType($sAssetFileType) {
        if (\AssetsBundle\AssetFile\AssetFile::assetFileTypeExists($sAssetFileType)) {
            if (!is_array($this->view_helper_plugins)) {
                throw new \LogicException('View helper plugins are undefined');
            }

            if (isset($this->view_helper_plugins[$sAssetFileType]) && $this->view_helper_plugins[$sAssetFileType] instanceof \Zend\View\Helper\HelperInterface) {
                return $this->view_helper_plugins[$sAssetFileType];
            }
            throw new \InvalidArgumentException('View helper plugin for asset file type "' . $sAssetFileType . '" is undefined');
        }
    }

    /**
     * Try to retrieve realpath for a given path (manage @zfRootPath)
     * @param string $sPathToResolve
     * @return string|boolean
     * @throws \InvalidArgumentException
     */
    public function getRealPath($sPathToResolve, \AssetsBundle\AssetFile\AssetFile $oAssetFile = null) {
        if (!is_string($sPathToResolve)) {
            throw new \InvalidArgumentException('Path to resolve expects string, "' . gettype($sPathToResolve) . '" given');
        }

        //Define resolved paths key
        $sResolvedPathsKey = ($oAssetFile ? $oAssetFile->getAssetFilePath() . '_' : '') . $sPathToResolve;

        if (isset($this->resolvedPaths[$sResolvedPathsKey])) {
            return $this->resolvedPaths[$sResolvedPathsKey];
        } else {
            //If path is "/", assets path is prefered
            if ($sPathToResolve === DIRECTORY_SEPARATOR && $this->hasAssetsPath()) {
                return $this->resolvedPaths[$sResolvedPathsKey] = $this->getAssetsPath();
            }

            //Path is absolute
            if (file_exists($sPathToResolve)) {
                return $this->resolvedPaths[$sResolvedPathsKey] = realpath($sPathToResolve);
            }

            if (strpos($sPathToResolve, '@zfRootPath') !== false) {
                $sPathToResolve = str_ireplace('@zfRootPath', getcwd(), $sPathToResolve);
            }
            if (strpos($sPathToResolve, '@zfAssetsPath') !== false) {
                $sPathToResolve = str_ireplace('@zfAssetsPath', $this->getAssetsPath(), $sPathToResolve);
            }

            if (($sRealPath = realpath($sPathToResolve)) !== false) {
                return $this->resolvedPaths[$sResolvedPathsKey] = $sRealPath;
            }

            //Try to guess real path with root path or asset path (if defined)
            if (file_exists($sRealPath = getcwd() . DIRECTORY_SEPARATOR . $sPathToResolve)) {
                return $this->resolvedPaths[$sResolvedPathsKey] = realpath($sRealPath);
            }
            if ($this->hasAssetsPath() && file_exists($sRealPath = $this->getAssetsPath() . DIRECTORY_SEPARATOR . $sPathToResolve)) {
                return $this->resolvedPaths[$sResolvedPathsKey] = realpath($sRealPath);
            }

            //Try to define real path with given asset file path
            if ($oAssetFile && file_exists($sRealPath = dirname($oAssetFile->getAssetFilePath()) . DIRECTORY_SEPARATOR . $sPathToResolve)) {
                return $this->resolvedPaths[$sResolvedPathsKey] = realpath($sRealPath);
            }
        }
        return false;
    }

    /**
     * Retrieve cache file name for given module name, controller name and action name
     * @return string
     */
    public function getCacheFileName() {
        $aAssets = $this->getAssets();

        $sCacheFileName = (isset($aAssets[$sModuleName = $this->getModuleName()]) ? $sModuleName : \AssetsBundle\Service\ServiceOptions::NO_MODULE);

        $aUnwantedKeys = array(
            \AssetsBundle\AssetFile\AssetFile::ASSET_CSS => true,
            \AssetsBundle\AssetFile\AssetFile::ASSET_LESS => true,
            \AssetsBundle\AssetFile\AssetFile::ASSET_JS => true,
            \AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA => true
        );
        $aAvailableModuleAssets = array_diff_key($aAssets, $aUnwantedKeys);
        $bControllerNameFound = false;
        $sControllerName = $this->getControllerName();
        foreach ($aAvailableModuleAssets as $aModuleConfig) {
            if (isset($aModuleConfig[$sControllerName])) {
                $bControllerNameFound = true;
                break;
            }
        }
        $sCacheFileName .= $bControllerNameFound ? $sControllerName : \AssetsBundle\Service\ServiceOptions::NO_CONTROLLER;

        $bActionNameFound = false;
        $sActionName = $this->getActionName();
        reset($aAvailableModuleAssets);
        foreach ($aAvailableModuleAssets as $aModuleConfig) {
            foreach (array_diff_key($aModuleConfig, $aUnwantedKeys) as $aControllerConfig) {
                if (isset($aControllerConfig[$sActionName])) {
                    $bActionNameFound = true;
                }
            }
        }
        $sCacheFileName .= $bActionNameFound ? $sActionName : \AssetsBundle\Service\ServiceOptions::NO_ACTION;
        return md5($sCacheFileName);
    }

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @param scalar $iLastModifiedTime
     * @return type
     */
    public function getAssetFileBaseUrl(\AssetsBundle\AssetFile\AssetFile $oAssetFile, $iLastModifiedTime = null) {
        if ($oAssetFile->isAssetFilePathUrl()) {
            return $oAssetFile->getAssetFilePath();
        }
        $sAssetPath = str_replace(array($this->getCachePath(), DIRECTORY_SEPARATOR), array('', '/'), $oAssetFile->getAssetFilePath());

    /**
     * Current controller name
     * @var string
     */
    protected $controllerName = self::NO_CONTROLLER;

        if ($oAssetFile->getAssetFileType() === \AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA) {
            return $this->getCacheUrl() . ltrim($sAssetPath, '/');
        }

        $iLastModifiedTime = is_null($iLastModifiedTime) ? $oAssetFile->getAssetFileLastModified() : $iLastModifiedTime;
        return $this->getCacheUrl() . ltrim($sAssetPath, '/') . ($iLastModifiedTime ? (strpos($sAssetPath, '?') === false ? '?' : '&' ) . $iLastModifiedTime : '');
    }

}
