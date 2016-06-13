<?php

namespace AssetsBundle\AssetFile;

class AssetFilesConfiguration
{

    /**
     * @var array
     */
    protected $assetFiles = array();

    /**
     * @return string
     */
    public function getConfigurationKey()
    {
        return $this->getOptions()->getModuleName() . '-' . $this->getOptions()->getControllerName() . '-' . $this->getOptions()->getActionName();
    }

    /**
     * @param string $sAssetFileType : (optionnal)
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getAssetFiles($sAssetFileType = null)
    {
        if ($sAssetFileType && !\AssetsBundle\AssetFile\AssetFile::assetFileTypeExists($sAssetFileType)) {
            throw new \InvalidArgumentException('Asset file type "' . $sAssetFileType . '" is not valid');
        }

        //Check if assets configuration is already set
        $sConfigurationKey = $this->getConfigurationKey();
        if (isset($this->assetFiles[$sConfigurationKey])) {
            if ($sAssetFileType) {
                return $this->assetFiles[$sConfigurationKey][$sAssetFileType];
            } else {
                return $this->assetFiles[$sConfigurationKey];
            }
        }

        //Define default assets
        $aAssets = $this->assetFiles[$sConfigurationKey] = array(
            \AssetsBundle\AssetFile\AssetFile::ASSET_CSS => array(),
            \AssetsBundle\AssetFile\AssetFile::ASSET_LESS => array(),
            \AssetsBundle\AssetFile\AssetFile::ASSET_JS => array(),
            \AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA => array()
        );

        //Common configuration
        $aCommonConfiguration = $this->getOptions()->getAssets();
        if (!empty($aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS]) && is_array($aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS])) {
            $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS], $aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS]);
        }
        if (!empty($aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS]) && is_array($aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS])) {
            $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS], $aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS]);
        }
        if (!empty($aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS]) && is_array($aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS])) {
            $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_JS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_JS], $aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS]);
        }
        if (!empty($aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA]) && is_array($aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA])) {
            $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA], $aCommonConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA]);
        }
        //Module configuration
        if (isset($aCommonConfiguration[$sModuleName = $this->getOptions()->getModuleName()])) {
            $aModuleConfiguration = $aCommonConfiguration[$sModuleName];
            if (!empty($aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS]) && is_array($aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS])) {
                $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS], $aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS]);
            }
            if (!empty($aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS]) && is_array($aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS])) {
                $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS], $aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS]);
            }
            if (!empty($aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS]) && is_array($aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS])) {
                $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_JS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_JS], $aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS]);
            }
            if (!empty($aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA]) && is_array($aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA])) {
                $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA], $aModuleConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA]);
            }

            //Controller configuration
            if (isset($aModuleConfiguration[$sControllerName = $this->getOptions()->getControllerName()])) {
                $aControllerConfiguration = $aModuleConfiguration[$sControllerName];
                if (!empty($aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS]) && is_array($aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS])) {
                    $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS], $aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS]);
                }
                if (!empty($aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS]) && is_array($aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS])) {
                    $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS], $aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS]);
                }
                if (!empty($aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS]) && is_array($aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS])) {
                    $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_JS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_JS], $aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS]);
                }
                if (!empty($aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA]) && is_array($aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA])) {
                    $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA], $aControllerConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA]);
                }

                //Action configuration
                if (isset($aControllerConfiguration[$sActionName = $this->getOptions()->getActionName()])) {
                    $aActionConfiguration = $aControllerConfiguration[$sActionName];
                    if (!empty($aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS]) && is_array($aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS])) {
                        $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS], $aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_CSS]);
                    }
                    if (!empty($aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS]) && is_array($aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS])) {
                        $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS], $aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_LESS]);
                    }
                    if (!empty($aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS]) && is_array($aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS])) {
                        $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_JS] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_JS], $aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_JS]);
                    }
                    if (!empty($aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA]) && is_array($aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA])) {
                        $aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA] = array_merge($aAssets[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA], $aActionConfiguration[\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA]);
                    }
                }
            }
        }

        //Retrieve asset files from configuration
        foreach ($aAssets as $sAssetFileTypeKey => $aAssetFiles) {
            foreach (array_unique($aAssetFiles) as $sAssetFilePath) {
                $this->addAssetFileFromOptions(is_array($sAssetFilePath) ? array_merge(array('asset_file_type' => $sAssetFileTypeKey, $sAssetFilePath)) : array('asset_file_path' => $sAssetFilePath, 'asset_file_type' => $sAssetFileTypeKey));
            }
        }

        if ($sAssetFileType) {
            return $this->assetFiles[$sConfigurationKey][$sAssetFileType];
        }
        return $this->assetFiles[$sConfigurationKey];
    }

    /**
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @return \AssetsBundle\AssetFile\AssetFilesManager
     */
    public function addAssetFile(\AssetsBundle\AssetFile\AssetFile $oAssetFile)
    {
        $this->assetFiles[$this->getConfigurationKey()][$oAssetFile->getAssetFileType()][$oAssetFile->getAssetFilePath()] = $oAssetFile;
        return $this;
    }

    /**
     * @param array $aAssetFileOptions
     * @return \AssetsBundle\AssetFile\AssetFilesConfiguration
     * @throws \InvalidArgumentException
     */
    public function addAssetFileFromOptions(array $aAssetFileOptions)
    {
        if (empty($aAssetFileOptions['asset_file_type'])) {
            throw new \InvalidArgumentException('Asset file type is empty');
        }

        // Initialize asset file
        $oAssetFile = new \AssetsBundle\AssetFile\AssetFile();
        $oAssetFile->setAssetFileType($aAssetFileOptions['asset_file_type']);
        unset($aAssetFileOptions['asset_file_type']);

        // Retrieve asset file path
        if (empty($aAssetFileOptions['asset_file_path'])) {
            throw new \InvalidArgumentException('Asset file path is empty');
        }

        if (!is_string($aAssetFileOptions['asset_file_path'])) {
            throw new \InvalidArgumentException('Asset file path expects string, "' . gettype($aAssetFileOptions['asset_file_path']) . '" given');
        }

        // Retrieve asset file realpath
        $sAssetRealPath = $this->getOptions()->getRealPath($aAssetFileOptions['asset_file_path'])?:$aAssetFileOptions['asset_file_path'];
        if (is_dir($sAssetRealPath)) {
            foreach ($this->getAssetFilesPathFromDirectory($sAssetRealPath, $oAssetFile->getAssetFileType()) as $sChildAssetRealPath) {
                $oNewAssetFile = clone $oAssetFile;
                $this->addAssetFile($oNewAssetFile->setAssetFilePath($sChildAssetRealPath));
            }
            return $this;
        }

        return $this->addAssetFile($oAssetFile->setAssetFilePath($sAssetRealPath));
    }

    /**
     * Retrieve assets from a directory
     *
     * @param  string $sDirPath
     * @param  string $sAssetType
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function getAssetFilesPathFromDirectory($sDirPath, $sAssetType)
    {
        if (!is_string($sDirPath) || !($sDirPath = $this->getOptions()->getRealPath($sDirPath)) && !is_dir($sDirPath)) {
            throw new \InvalidArgumentException('Directory not found : ' . $sDirPath);
        }
        if (!\AssetsBundle\AssetFile\AssetFile::assetFileTypeExists($sAssetType)) {
            throw new \InvalidArgumentException('Asset\'s type is undefined : ' . $sAssetType);
        }
        
        $oDirIterator = new \DirectoryIterator($sDirPath);
        $aAssets = array();
        $bRecursiveSearch = $this->getOptions()->allowsRecursiveSearch();
        
        // Defined expected extensions forthe given type
        if ($sAssetType === \AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA) {
            $aExpectedExtensions = $this->getOptions()->getMediaExt();
        } else {
            $aExpectedExtensions = array(\AssetsBundle\AssetFile\AssetFile::getAssetFileDefaultExtension($sAssetType));
        }
        
        foreach ($oDirIterator as $oFile) {
            if ($oFile->isFile()) {
                if (in_array(strtolower(pathinfo($oFile->getFilename(), PATHINFO_EXTENSION)), $aExpectedExtensions, true)) {
                    $aAssets[] = $oFile->getPathname();
                }
            } elseif ($oFile->isDir() && !$oFile->isDot() && $bRecursiveSearch) {
                $aAssets = array_merge(
                    $aAssets,
                    $this->getAssetFilesPathFromDirectory($oFile->getPathname(), $sAssetType)
                );
            }
        }
        return $aAssets;
    }

    /**
     * Retrieve asset relative path
     *
     * @param  string $sAssetPath
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getAssetRelativePath($sAssetPath)
    {
        if (!($sAssetRealPath = $this->getOptions()->getRealPath($sAssetPath))) {
            throw new \InvalidArgumentException('File "' . $sAssetPath . '" does not exist');
        }

        //If asset is already a cache file
        $sCachePath = $this->getOptions()->getCachePath();
        return strpos($sAssetRealPath, $sCachePath) !== false ? str_ireplace(
            array($sCachePath, '.less'),
            array('', '.css'),
            $sAssetRealPath
        ) : (
                $this->getOptions()->hasAssetsPath() ? str_ireplace(
                    array($this->getOptions()->getAssetsPath(), getcwd(), DIRECTORY_SEPARATOR),
                    array('', '', '_'),
                    $sAssetRealPath
                ) : str_ireplace(
                    array(getcwd(), DIRECTORY_SEPARATOR),
                    array('', '_'),
                    $sAssetRealPath
                )
                );
    }

    /**
     * Check if assets configuration is the same as last saved configuration
     *
     * @param  array $aAssetsType
     * @return boolean
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function assetsConfigurationHasChanged(array $aAssetsType = null)
    {
        $aAssetsType = $aAssetsType ? array_unique($aAssetsType) : array(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS, \AssetsBundle\AssetFile\AssetFile::ASSET_LESS, \AssetsBundle\AssetFile\AssetFile::ASSET_JS, \AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA);

        //Retrieve saved onfiguration file
        if (file_exists($sConfigFilePath = $this->getConfigurationFilePath())) {
            \Zend\Stdlib\ErrorHandler::start();
            $aConfig = include $sConfigFilePath;
            \Zend\Stdlib\ErrorHandler::stop(true);

            if ($aConfig === false || !is_array($aConfig)) {
                throw new \RuntimeException('Unable to get file content from file "' . $sConfigFilePath . '"');
            }

            //Get assets configuration
            $aAssets = $this->getOptions()->getAssets();

            //Check if configuration has changed for each type of asset
            foreach ($aAssetsType as $sAssetType) {
                if (!\AssetsBundle\AssetFile\AssetFile::assetFileTypeExists($sAssetType)) {
                    throw new \LogicException('Asset type "' . $sAssetType . '" does not exist');
                }
                if (empty($aAssets[$sAssetType]) && !empty($aConfig[$sAssetType])) {
                    return true;
                } elseif (!empty($aAssets[$sAssetType])) {
                    if (empty($aConfig[$sAssetType])) {
                        return true;
                    } elseif (array_diff($aAssets[$sAssetType], $aConfig[$sAssetType]) || array_diff($aConfig[$sAssetType], $aAssets[$sAssetType])
                    ) {
                        return true;
                    }
                }
            }
            return false;
        }
        return true;
    }

    /**
     * Retrieve configuration file name for the current request
     *
     * @return string
     */
    public function getConfigurationFilePath()
    {
        return $this->getOptions()->getProcessedDirPath() . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $this->getOptions()->getCacheFileName() . '.conf';
    }

    /**
     * Save current asset configuration into conf file
     *
     * @return \AssetsBundle\AssetFile\AssetFilesConfiguration
     */
    public function saveAssetFilesConfiguration()
    {
        \Zend\Stdlib\ErrorHandler::start();

        //Retrieve configuration file path
        $sConfigurationFilePath = $this->getConfigurationFilePath();

        //Create dir if needed
        if (!is_dir($sConfigurationFileDirPath = dirname($sConfigurationFilePath))) {
            mkdir($sConfigurationFileDirPath, 0775);
        }
        file_put_contents($sConfigurationFilePath, '<?php' . PHP_EOL . 'return ' . var_export($this->getOptions()->getAssets(), 1) . ';');
        \Zend\Stdlib\ErrorHandler::stop(true);
        return $this;
    }

    /**
     * @param \AssetsBundle\Service\ServiceOptions $oOptions
     * @return \AssetsBundle\AssetFile\AssetFilesConfiguration
     */
    public function setOptions(\AssetsBundle\Service\ServiceOptions $oOptions)
    {
        $this->options = $oOptions;
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

}
