<?php

namespace AssetsBundle\AssetFile;

class AssetFilesManager
{

    /**
     * @var \AssetsBundle\Service\ServiceOptions
     */
    protected $options;

    /**
     * @var \AssetsBundle\AssetFile\AssetFilesConfiguration
     */
    protected $assetFilesConfiguration;

    /**
     * @var \AssetsBundle\AssetFile\AssetFileFiltersManager
     */
    protected $assetFileFiltersManager;

    /**
     * @var \AssetsBundle\AssetFile\AssetFilesCacheManager
     */
    protected $assetFilesCacheManager;

    /**
     * Constructor
     * @param \AssetsBundle\Service\ServiceOptions $oOptions
     */
    public function __construct(\AssetsBundle\Service\ServiceOptions $oOptions = null)
    {
        if ($oOptions) {
            $this->setOptions($oOptions);
        }
    }

    /**
     * @param string $sAssetFileType
     * @return array
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    public function getCachedAssetsFiles($sAssetFileType)
    {
        if (!\AssetsBundle\AssetFile\AssetFile::assetFileTypeExists($sAssetFileType)) {
            throw new \InvalidArgumentException('Asset file type "' . $sAssetFileType . '" is not valid');
        }

        //Production
        if ($this->getOptions()->isProduction()) {

            $oAssetFilesCacheManager = $this->getAssetFilesCacheManager();

            //Production cached asset files do not exist
            if (!$oAssetFilesCacheManager->hasProductionCachedAssetFiles($sAssetFileType)) {
                switch ($sAssetFileType) {
                    case \AssetsBundle\AssetFile\AssetFile::ASSET_JS :
                        $this->cacheJsAssetFiles();
                        break;
                    case \AssetsBundle\AssetFile\AssetFile::ASSET_CSS :
                        $this->cacheCssAssetFiles();
                        break;
                    default:
                        throw new \DomainException('Only "' . \AssetsBundle\AssetFile\AssetFile::ASSET_JS . '" & "' . \AssetsBundle\AssetFile\AssetFile::ASSET_CSS . '" assets file type can be retrieved');
                }
            }
            return $oAssetFilesCacheManager->getProductionCachedAssetFiles($sAssetFileType);
        }
        //Development
        else {
            switch ($sAssetFileType) {
                case \AssetsBundle\AssetFile\AssetFile::ASSET_JS :
                    return $this->cacheJsAssetFiles();
                case \AssetsBundle\AssetFile\AssetFile::ASSET_CSS :
                    return $this->cacheCssAssetFiles();
                default:
                    throw new \DomainException('Only "' . \AssetsBundle\AssetFile\AssetFile::ASSET_JS . '" & "' . \AssetsBundle\AssetFile\AssetFile::ASSET_CSS . '" assets file type can be retrieved');
            }
        }
    }

    /**
     * Cache Css asset files and retrieve cached asset files
     * @return array
     */
    protected function cacheCssAssetFiles()
    {

        // Cache media asset files
        $this->cacheMediaAssetFiles();

        if ($bIsProduction = $this->getOptions()->isProduction()) {
            // Retrieve asset file filters manager
            $oAssetFileFiltersManager = $this->getAssetFileFiltersManager();

            // Retrieve Css file filter if available
            $oCssFileFilter = $oAssetFileFiltersManager->has(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS) ? $oAssetFileFiltersManager->get(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS) : null;

            // Create tmp asset file
            \Zend\Stdlib\ErrorHandler::start();
            $sTmpAssetFilePath = tempnam($this->getOptions()->getTmpDirPath(), '.' . \AssetsBundle\AssetFile\AssetFile::getAssetFileDefaultExtension(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS));
            \Zend\Stdlib\ErrorHandler::stop(true);

            $oTmpAssetFile = new \AssetsBundle\AssetFile\AssetFile(array(
                'asset_file_type' => \AssetsBundle\AssetFile\AssetFile::ASSET_CSS,
                'asset_file_path' => $sTmpAssetFilePath
            ));

            // Callback for url rewriting
            $oRewriteUrlCallback = array($this, 'rewriteUrl');

            // Merge less asset files
            foreach ($this->cacheLessAssetFiles() as $oAssetFile) {
                $oTmpAssetFile->setAssetFileContents(preg_replace_callback('/url\(([^\)]+)\)/', function($aMatches) use($oAssetFile, $oRewriteUrlCallback) {
                            return call_user_func($oRewriteUrlCallback, $aMatches, $oAssetFile);
                        }, $oCssFileFilter ? $oCssFileFilter->filterAssetFile($oAssetFile) : $oAssetFile->getAssetFileContents()) . PHP_EOL);

                // Remove temp less asset file
                \Zend\Stdlib\ErrorHandler::start();
                unlink($oAssetFile->getAssetFilePath());
                \Zend\Stdlib\ErrorHandler::stop(true);
            }

            // Merge css asset files
            foreach ($this->getAssetFilesConfiguration()->getAssetFiles(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS) as $oAssetFile) {
                $oTmpAssetFile->setAssetFileContents(preg_replace_callback('/url\(([^\)]+)\)/', function($aMatches) use($oAssetFile, $oRewriteUrlCallback) {
                            return call_user_func($oRewriteUrlCallback, $aMatches, $oAssetFile);
                        }, $oCssFileFilter ? $oCssFileFilter->filterAssetFile($oAssetFile) : $oAssetFile->getAssetFileContents()) . PHP_EOL);
            }

            return array($this->getAssetFilesCacheManager()->cacheAssetFile($oTmpAssetFile));
        } else {
            // Cache less asset files
            $this->cacheLessAssetFiles();

            // Retrieve asset files cache manager
            $oAssetFilesCacheManager = $this->getAssetFilesCacheManager();
            $aAssetFiles = array();
            foreach ($this->getAssetFilesConfiguration()->getAssetFiles(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS) as $oAssetFile) {
                // Cache asset file
                $aAssetFiles[] = $oAssetFilesCacheManager->cacheAssetFile($oAssetFile);
            }
            return $aAssetFiles;
        }
    }

    /**
     * Cache Less asset files and retrieve cached asset files
     * @return array
     */
    protected function cacheLessAssetFiles()
    {

        // Create tmp asset file
        \Zend\Stdlib\ErrorHandler::stop(true);
        $sTmpAssetFilePath = tempnam($this->getOptions()->getTmpDirPath(), '');
        \Zend\Stdlib\ErrorHandler::start();
        $oTmpAssetFile = new \AssetsBundle\AssetFile\AssetFile(array(
            'asset_file_type' => \AssetsBundle\AssetFile\AssetFile::ASSET_LESS,
            'asset_file_path' => $sTmpAssetFilePath
        ));

        if (file_exists($sTmpAssetFilePath)) {

        }

        // Retrieve Asset file cache manager;
        $oAssetFilesCacheManager = $this->getAssetFilesCacheManager();

        // Production don't need to cache less asset file
        if ($this->getOptions()->isProduction()) {
            $oTmpAssetFile->setAssetFileType(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS);
        }

        // Retrieve asset file cached if exists
        if (file_exists($sAssetFileCachedPath = $oAssetFilesCacheManager->getAssetFileCachePath($oTmpAssetFile))) {
            \Zend\Stdlib\ErrorHandler::start();
            $iAssetFileCachedFilemtime = filemtime($sAssetFileCachedPath);
            \Zend\Stdlib\ErrorHandler::stop(true);
        } else {
            $iAssetFileCachedFilemtime = null;
        }

        // Build import less file
        \Zend\Stdlib\ErrorHandler::start();
        $bIsUpdated = !$this->getAssetFilesConfiguration()->assetsConfigurationHasChanged(array(\AssetsBundle\AssetFile\AssetFile::ASSET_LESS));

        foreach ($this->getAssetFilesConfiguration()->getAssetFiles(\AssetsBundle\AssetFile\AssetFile::ASSET_LESS) as $oAssetFile) {
            if ($iAssetFileCachedFilemtime && $bIsUpdated) {
                $bIsUpdated = $iAssetFileCachedFilemtime >= $oAssetFile->getAssetFileLastModified();
            }
            $oTmpAssetFile->setAssetFileContents('@import "' . str_replace(array(getcwd(), DIRECTORY_SEPARATOR), array('', '/'), $oAssetFile->getAssetFilePath()) . '";' . PHP_EOL);
        }
        \Zend\Stdlib\ErrorHandler::stop(true);

        // If less file is updated return cached asset file
        if ($iAssetFileCachedFilemtime && $bIsUpdated) {
            return array($oTmpAssetFile->setAssetFilePath($sAssetFileCachedPath)->setAssetFileType(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS));
        }

        // Run less asset file filter
        $sAssetFileFilteredContent = $this->getAssetFileFiltersManager()->get(\AssetsBundle\AssetFile\AssetFile::ASSET_LESS)->filterAssetFile($oTmpAssetFile);

        // Set new content to temp asset file contents
        $oTmpAssetFile->setAssetFileContents($sAssetFileFilteredContent, false);

        // Development need to cache less asset file
        return array($this->getAssetFilesCacheManager()->cacheAssetFile($oTmpAssetFile)->setAssetFileType(\AssetsBundle\AssetFile\AssetFile::ASSET_CSS));
    }

    /**
     * Cache Js asset files and retrieve cached asset files
     * @return array
     */
    protected function cacheJsAssetFiles()
    {

        if ($bIsProduction = $this->getOptions()->isProduction()) {
            //Retrieve asset file filters manager
            $oAssetFileFiltersManager = $this->getAssetFileFiltersManager();

            //Retrieve Js asset file filter if available
            $oJsFileFilter = $oAssetFileFiltersManager->has(\AssetsBundle\AssetFile\AssetFile::ASSET_JS) ? $oAssetFileFiltersManager->get(\AssetsBundle\AssetFile\AssetFile::ASSET_JS) : null;

            //Create tmp asset file
            \Zend\Stdlib\ErrorHandler::stop(true);
            $sTmpAssetFilePath = tempnam($this->getOptions()->getTmpDirPath(), '.' . \AssetsBundle\AssetFile\AssetFile::getAssetFileDefaultExtension(\AssetsBundle\AssetFile\AssetFile::ASSET_JS));
            \Zend\Stdlib\ErrorHandler::start();

            $oTmpAssetFile = new \AssetsBundle\AssetFile\AssetFile(array(
                'asset_file_type' => \AssetsBundle\AssetFile\AssetFile::ASSET_JS,
                'asset_file_path' => $sTmpAssetFilePath
            ));

            foreach ($this->getAssetFilesConfiguration()->getAssetFiles(\AssetsBundle\AssetFile\AssetFile::ASSET_JS) as $oAssetFile) {
                $sAssetFileContent = $oJsFileFilter ? $oJsFileFilter->filterAssetFile($oAssetFile) : $oAssetFile->getAssetFileContents();
                $oTmpAssetFile->setAssetFileContents($sAssetFileContent . PHP_EOL);
            }
            return array($this->getAssetFilesCacheManager()->cacheAssetFile($oTmpAssetFile));
        } else {
            //Retrieve asset files cache manager
            $oAssetFilesCacheManager = $this->getAssetFilesCacheManager();
            $aAssetFiles = array();
            foreach ($this->getAssetFilesConfiguration()->getAssetFiles(\AssetsBundle\AssetFile\AssetFile::ASSET_JS) as $oAssetFile) {
                //Cache asset file
                $aAssetFiles[] = $oAssetFilesCacheManager->cacheAssetFile($oAssetFile);
            }
            return $aAssetFiles;
        }
    }

    /**
     * Cache media asset files and retrieve cached asset files
     * @return array
     */
    protected function cacheMediaAssetFiles()
    {
        $aAssetFileFilters = array();
        $aAssetFiles = array();

        //Retrieve asset files cache manager
        $oAssetFilesCacheManager = $this->getAssetFilesCacheManager();

        // Retrieve asset file filters manager
        $oAssetFileFiltersManager = $this->getAssetFileFiltersManager();
        $bIsProduction = $this->getOptions()->isProduction();

        foreach ($this->getAssetFilesConfiguration()->getAssetFiles(\AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA) as $oAssetFile) {
            if ($oAssetFilesCacheManager->isAssetFileCached($oAssetFile)) {
                $aAssetFiles[] = $oAssetFilesCacheManager->getAssetFileCachePath($oAssetFile);
                continue;
            }
            if ($bIsProduction) {
                if (!isset($aAssetFileFilters[$sAssetFileExtension = $oAssetFile->getAssetFileExtension()])) {
                    $aAssetFileFilters[$sAssetFileExtension] = $oAssetFileFiltersManager->has($sAssetFileExtension) ? $oAssetFileFiltersManager->get($sAssetFileExtension) : null;
                }
                $oMediaFileFilter = $aAssetFileFilters[$sAssetFileExtension];
            } else {
                $oMediaFileFilter = null;
            }

            if ($oMediaFileFilter) {
                // Create tmp asset file
                \Zend\Stdlib\ErrorHandler::stop(true);
                $sTmpAssetFilePath = tempnam($this->getOptions()->getTmpDirPath(), '.' . $sAssetFileExtension);
                \Zend\Stdlib\ErrorHandler::start();

                $oTmpAssetFile = new \AssetsBundle\AssetFile\AssetFile(array(
                    'asset_file_type' => \AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA,
                    'asset_file_path' => $sTmpAssetFilePath
                ));
                $oTmpAssetFile->setAssetFileContents($oMediaFileFilter->filterAssetFile($oAssetFile), false);

                // Cache asset file
                $aAssetFiles[] = $oAssetFilesCacheManager->cacheAssetFile($oTmpAssetFile, $oAssetFile);

                // Unlink tmp asset file
                \Zend\Stdlib\ErrorHandler::stop(true);
                unlink($sTmpAssetFilePath);
                \Zend\Stdlib\ErrorHandler::start();
            } else {
                // Cache asset file
                $aAssetFiles[] = $oAssetFilesCacheManager->cacheAssetFile($oAssetFile);
            }
        }

        return $aAssetFiles;
    }

    /**
     * Rewrite url to match with cache path if needed
     * @param array $aMatches
     * @param \AssetsBundle\AssetFile\AssetFile $oAssetFile
     * @return array
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function rewriteUrl(array $aMatches, \AssetsBundle\AssetFile\AssetFile $oAssetFile)
    {
        if (!isset($aMatches[1])) {
            throw new \InvalidArgumentException('Url match is not valid');
        }

        // Remove quotes & double quotes from url
        $aFirstCharMatches = null;
        $sFirstChar = preg_match('/^("|\'){1}/', $sUrl = trim($aMatches[1]), $aFirstCharMatches) ? $aFirstCharMatches[1] : '';
        $sUrl = str_ireplace(array('"', '\''), '', $sUrl);

        // Data url
        if (strpos($sUrl, 'data:') === 0) {
            return $aMatches[0];
        }

        // Remote absolute url
        if (preg_match('/^http/', $sUrl)) {
            return $aMatches[0];
        }

        // Split arguments
        if (strpos($sUrl, '?') !== false) {
            list($sUrl, $sArguments) = explode('?', $sUrl);
        }

        // Split anchor
        if (strpos($sUrl, '#') !== false) {
            list($sUrl, $sAnchor) = explode('#', $sUrl);
        }

        // Absolute url
        if (($sUrlRealpath = $this->getOptions()->getRealPath($sUrl, $oAssetFile))) {

            // Initialize asset file from url
            $oUrlAssetFile = new \AssetsBundle\AssetFile\AssetFile(array(
                'asset_file_type' => \AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA,
                'asset_file_path' => $sUrlRealpath
            ));

            $sAssetFileCachePath = $this->getAssetFilesCacheManager()->getAssetFileCachePath($oUrlAssetFile);
            if (!file_exists($sAssetFileCachePath)) {
                throw new \LogicException('Media file "' . $oUrlAssetFile->getAssetFilePath() . '" used by "' . $oAssetFile->getAssetFilePath() . '" does not have been cached. Please add it into ["assets_bundle"]["assets"]["media"] configuration array');
            }

            // Define cached file path
            $oUrlAssetFile->setAssetFilePath($sAssetFileCachePath);

            // Retrieve asset file base url
            $sAssetFileBaseUrl = $this->getOptions()->getAssetFileBaseUrl($oUrlAssetFile);

            // Add argument and / or anchor to asset file base url
            $sAssetFileRealBaseUrl = $sFirstChar . $sAssetFileBaseUrl . (empty($sArguments) ? '' : '?' . $sArguments) . (empty($sAnchor) ? '' : '#' . $sAnchor) . $sFirstChar;

            // Return asset file base url
            return str_replace($aMatches[1], $sAssetFileRealBaseUrl, $aMatches[0]);
        }
        // Remote relative url
        elseif ($oAssetFile->isAssetFilePathUrl()) {
            return str_replace($aMatches[1], $sFirstChar . dirname($oAssetFile->getAssetFilePath()) . '/' . ltrim($sUrl, '/') . $sFirstChar, $aMatches[0]);
        }
        // Url is not an exising file
        else {
            throw new \LogicException('Url file "' . $sUrl . '" does not exist even relative with "' . $oAssetFile->getAssetFilePath() . '"');
        }
    }

    /**
     * @param \AssetsBundle\Service\ServiceOptions $oOptions
     * @return \AssetsBundle\AssetFile\AssetFilesManager
     */
    public function setOptions(\AssetsBundle\Service\ServiceOptions $oOptions)
    {
        $this->options = $oOptions;
        if (isset($this->assetFilesConfiguration)) {
            $this->getAssetFilesConfiguration()->setOptions($this->options);
        }
        if (isset($this->assetFileFiltersManager)) {
            $this->getAssetFileFiltersManager()->setOptions($this->options);
        }
        if (isset($this->assetFilesCacheManager)) {
            $this->getAssetFilesCacheManager()->setOptions($this->options);
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
     * Set the asset files configuration
     * @param \AssetsBundle\AssetFile\AssetFilesConfiguration $oAssetFilesConfiguration
     * @return \AssetsBundle\AssetFile\AssetFilesManager
     */
    public function setAssetFilesConfiguration(\AssetsBundle\AssetFile\AssetFilesConfiguration $oAssetFilesConfiguration)
    {
        $this->assetFilesConfiguration = $oAssetFilesConfiguration->setOptions($this->getOptions());
        return $this;
    }

    /**
     * Retrieve the asset files configuration. Lazy loads an instance if none currently set.
     * @return \AssetsBundle\AssetFile\AssetFilesConfiguration
     */
    public function getAssetFilesConfiguration()
    {
        if (!$this->assetFilesConfiguration instanceof \AssetsBundle\AssetFile\AssetFilesConfiguration) {
            $this->setAssetFilesConfiguration(new \AssetsBundle\AssetFile\AssetFilesConfiguration());
        }
        return $this->assetFilesConfiguration;
    }

    /**
     * Set the asset file filters manager
     * @param \AssetsBundle\AssetFile\AssetFileFiltersManager $oAssetFileFiltersManager
     * @return \AssetsBundle\AssetFile\AssetFilesManager
     */
    public function setAssetFileFiltersManager(\AssetsBundle\AssetFile\AssetFileFiltersManager $oAssetFileFiltersManager)
    {
        $this->assetFileFiltersManager = $oAssetFileFiltersManager->setOptions($this->getOptions());
        return $this;
    }

    /**
     * Retrieve the asset file filters manager. Lazy loads an instance if none currently set.
     * @return \AssetsBundle\AssetFile\AssetFileFiltersManager
     */
    public function getAssetFileFiltersManager()
    {
        if (!$this->assetFileFiltersManager instanceof \AssetsBundle\AssetFile\AssetFileFiltersManager) {
            $this->setAssetFileFiltersManager(new \AssetsBundle\AssetFile\AssetFileFiltersManager());
        }
        return $this->assetFileFiltersManager;
    }

    /**
     * Set the asset files cache manager
     * @param \AssetsBundle\AssetFile\AssetFilesCacheManager $oAssetFilesCacheManager
     * @return \AssetsBundle\AssetFile\AssetFilesManager
     */
    public function setAssetFilesCacheManager(\AssetsBundle\AssetFile\AssetFilesCacheManager $oAssetFilesCacheManager)
    {
        $this->assetFilesCacheManager = $oAssetFilesCacheManager->setOptions($this->getOptions());
        return $this;
    }

    /**
     * Retrieve the asset files cache manager. Lazy loads an instance if none currently set.
     * @return \AssetsBundle\AssetFile\AssetFilesCacheManager
     */
    public function getAssetFilesCacheManager()
    {
        if (!$this->assetFilesCacheManager instanceof \AssetsBundle\AssetFile\AssetFilesCacheManager) {
            $this->setAssetFilesCacheManager(new \AssetsBundle\AssetFile\AssetFilesCacheManager());
        }
        return $this->assetFilesCacheManager;
    }
}
