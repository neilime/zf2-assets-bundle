<?php

namespace AssetsBundle\AssetFile;

class AssetFile extends \Zend\Stdlib\AbstractOptions {

    const ASSET_CSS = 'css';
    const ASSET_JS = 'js';
    const ASSET_LESS = 'less';
    const ASSET_MEDIA = 'media';

    /**
     * @var string
     */
    protected $assetFileType;

    /**
     * @var string
     */
    protected $assetFilePath;

    /**
     * @var string
     */
    protected $assetFileContents;

    /**
     * @var string
     */
    protected $assetFileContentsLastRetrievedTime;

    /**
     * @var string
     */
    protected $assetFileExtension;

    /**
     * @return string
     * @throws \LogicException
     */
    public function getAssetFileType() {
       
        if (self::assetFileTypeExists($this->assetFileType)) {
            return $this->assetFileType;
        }
        throw new \LogicException('Asset file type is undefined');
    }

    /**
     * @param string $sAssetFileType
     * @return \AssetsBundle\Service\AssetFile
     * @throws \InvalidArgumentException
     */
    public function setAssetFileType($sAssetFileType) {
        if (self::assetFileTypeExists($sAssetFileType)) {
            $this->assetFileType = $sAssetFileType;
            return $this;
        }
        throw new \InvalidArgumentException('Asset file type "' . $sAssetFileType . '" does not exist');
    }

    /**
     * @return string
     * @throws \LogicException
     */
    public function getAssetFilePath() {
        if (is_string($this->assetFilePath)) {
            return $this->assetFilePath;
        }
        throw new \LogicException('Asset file path is undefined');
    }

    /**
     * @return boolean
     */
    public function isAssetFilePathUrl() {
        return filter_var($sAssetFilePath = $this->getAssetFilePath(), FILTER_VALIDATE_URL) && preg_match('/^\/|http/', $sAssetFilePath);
    }

    /**
     * @param string $sAssetFilePath
     * @return \AssetsBundle\Service\AssetFile
     * @throws \InvalidArgumentException
     */
    public function setAssetFilePath($sAssetFilePath) {
        if (!is_string($sAssetFilePath)) {
            throw new \InvalidArgumentException('Asset file path expects string, "' . gettype($sAssetFilePath) . '" given');
        }

        // Reset asset file contents
        $this->assetFileContents = null;

        if (is_readable($sAssetFilePath)) {
            $this->assetFilePath = $sAssetFilePath;
            return $this;
        }

        // Asset file path is an url
        if (strpos($sAssetFilePath, '://') === false) {
            throw new \InvalidArgumentException('Asset\'s file "' . $sAssetFilePath . '" does not exist');
        } elseif ($this->getAssetFileType() === self::ASSET_LESS) {
            throw new \InvalidArgumentException('Less assets does not support urls, "' . $sAssetFilePath . '" given');
        }

        if (!($sFilteredAssetFilePath = filter_var($sAssetFilePath, FILTER_VALIDATE_URL))) {
            throw new \InvalidArgumentException('Asset\'s file path "' . $sAssetFilePath . '" is not a valid url');
        }

        \Zend\Stdlib\ErrorHandler::start();
        $oFileHandle = fopen($sFilteredAssetFilePath, 'r');
        \Zend\Stdlib\ErrorHandler::stop(true);
        if (!$oFileHandle) {
            throw new \InvalidArgumentException('Unable to open asset file "' . $sFilteredAssetFilePath . '"');
        }

        \Zend\Stdlib\ErrorHandler::start();
        $aMetaData = stream_get_meta_data($oFileHandle);
        \Zend\Stdlib\ErrorHandler::stop(true);
        if (empty($aMetaData['uri'])) {
            throw new \InvalidArgumentException('Unable to retreive uri metadata from file "' . $sFilteredAssetFilePath . '"');
        }
        $this->assetFilePath = $aMetaData['uri'];

        \Zend\Stdlib\ErrorHandler::start();
        fclose($oFileHandle);
        \Zend\Stdlib\ErrorHandler::stop(true);

        return $this;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getAssetFileContents() {
        if (
                $this->assetFileContents &&
                (($iLastModified = $this->getAssetFileLastModified()) && $iLastModified < $this->assetFileContentsLastRetrievedTime)
        ) {
            return $this->assetFileContents;
        }
        if (!is_readable($sAssetFilePath = $this->getAssetFilePath())) {
            \Zend\Stdlib\ErrorHandler::start();
            $oFileHandle = fopen($sAssetFilePath, 'r');
            \Zend\Stdlib\ErrorHandler::stop(true);

            $this->assetFileContents = '';
            while (($sContent = fgets($oFileHandle)) !== false) {
                $this->assetFileContents .= $sContent . PHP_EOL;
            }
            if (!feof($oFileHandle)) {
                throw new \RuntimeException('Unable to retrieve asset contents from file "' . $sAssetFilePath . '"');
            }
            fclose($oFileHandle);
        } elseif (strtolower(pathinfo($sAssetFilePath, PATHINFO_EXTENSION)) === 'php') {
            ob_start();
            if (false === include $sAssetFilePath) {
                throw new \RuntimeException('Error appends while including asset file "' . $sAssetFilePath . '"');
            }
            $this->assetFileContents = ob_get_clean();
        } elseif (($this->assetFileContents = file_get_contents($sAssetFilePath)) === false) {
            throw new \RuntimeException('Unable to retrieve asset contents from file "' . $sAssetFilePath . '"');
        }

        // Update content last retrieved time
        $this->assetFileContentsLastRetrievedTime = time();

        return $this->assetFileContents;
    }

    /**
     * @param string $sAssetFileContents
     * @param boolean $bFileAppend
     * @return \AssetsBundle\AssetFile\AssetFile
     * @throws \InvalidArgumentException
     */
    public function setAssetFileContents($sAssetFileContents, $bFileAppend = true) {
        if (!is_string($sAssetFileContents)) {
            throw new \InvalidArgumentException('Asset file content expects string, "' . gettype($sAssetFileContents) . '" given');
        }
        if ($bFileAppend) {
            if ($this->assetFileContents) {
                $this->assetFileContents .= $sAssetFileContents;
            }
            \Zend\Stdlib\ErrorHandler::start();
            file_put_contents($this->getAssetFilePath(), $sAssetFileContents, FILE_APPEND);
            \Zend\Stdlib\ErrorHandler::stop(true);
        } else {
            $this->assetFileContents = $sAssetFileContents;
            \Zend\Stdlib\ErrorHandler::start();
            file_put_contents($this->getAssetFilePath(), $sAssetFileContents);
            \Zend\Stdlib\ErrorHandler::stop(true);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getAssetFileExtension() {
        return $this->assetFileExtension ? : $this->assetFileExtension = strtolower(pathinfo($this->getAssetFilePath(), PATHINFO_EXTENSION));
    }

    /**
     * Retrieve asset file last modified timestamp
     * @return int|null
     */
    public function getAssetFileLastModified() {
        if ($this->isAssetFilePathUrl()) {
            if (
            //Retrieve headers
                    ($aHeaders = get_headers($sAssetFilePath = $this->getAssetFilePath(), 1))
                    //Assert return is OK
                    && strstr($aHeaders[0], '200') !== false
                    //Retrieve last modified as DateTime
                    && !empty($aHeaders['Last-Modified']) && $oLastModified = new \DateTime($aHeaders['Last-Modified'])
            ) {
                return $oLastModified->getTimestamp();
            } else {
                $oCurlHandle = curl_init($sAssetFilePath);
                curl_setopt($oCurlHandle, CURLOPT_NOBODY, true);
                curl_setopt($oCurlHandle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($oCurlHandle, CURLOPT_FILETIME, true);
                if (curl_exec($oCurlHandle) === false) {
                    return null;
                }
                return curl_getinfo($oCurlHandle, CURLINFO_FILETIME) ? : null;
            }
        } else {
            \Zend\Stdlib\ErrorHandler::start();
            $iAssetFileFilemtime = filemtime($this->getAssetFilePath());
            \Zend\Stdlib\ErrorHandler::stop(true);
            return $iAssetFileFilemtime ? : null;
        }
    }

    /**
     * Check if asset file's type is valid
     * @param string $sAssetFileType
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public static function assetFileTypeExists($sAssetFileType) {
        if (!is_string($sAssetFileType)) {
            throw new \InvalidArgumentException('Asset file type expects string, "' . gettype($sAssetFileType) . '" given');
        }
        switch ($sAssetFileType) {
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
     * @return string
     */
    public static function getAssetFileDefaultExtension($sAssetFileType) {
        if (!is_string($sAssetFileType)) {
            throw new \InvalidArgumentException('Asset file type expects string, "' . gettype($sAssetFileType) . '" given');
        }
        switch ($sAssetFileType) {
            case self::ASSET_CSS:
                return 'css';
            case self::ASSET_LESS:
                return 'less';
            case self::ASSET_JS:
                return 'js';
            default:
                throw new \DomainException('Asset file type "' . $sAssetFileType . '" has no default extension');
        }
    }

}
