<?php
namespace AssetsBundle\Service;
class Service{
	const ASSET_CSS = 'css';
	const ASSET_JS = 'js';
	const ASSET_LESS = 'less';
	const ASSET_MEDIA = 'media';

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var string
	 */
	protected $controllerName;

	/**
	 * @var string
	 */
	protected $actionName;

	/**
	 * @var \Zend\View\Renderer\RendererInterface $oRenderer
	 */
	protected $renderer;

	/**
	 * @var \AssetsBundle\View\Strategy\StrategyInterface[]
	 */
	protected $strategy = array();

	/**
	 * @var array
	 */
	protected $assetFilters = array(
		self::ASSET_CSS => null,
		self::ASSET_JS => null,
		self::ASSET_LESS => null
	);

	/**
	 * Constructor
	 * @param array $aConfiguration
	 * @throws \Exception
	 */
	public function __construct(array $aConfiguration){
		//Check configuration entries
		if(!isset($aConfiguration['cachePath'],$aConfiguration['cacheUrl'],$aConfiguration['assetsPath'],$aConfiguration['rendererToStrategy'],$aConfiguration['mediaExt']))throw new \Exception('Error in configuration');

		//Check configuration values
		if(strpos($aConfiguration['cacheUrl'],'@zfBaseUrl') !== false){
			if(!isset($aConfiguration['basePath']))throw new \Exception('Base path is undefined in configuration');
			$aConfiguration['basePath'] = rtrim($aConfiguration['basePath'], '/');
			$aConfiguration['cacheUrl'] = $aConfiguration['basePath'].'/'.ltrim(str_ireplace('@zfBaseUrl','', $aConfiguration['cacheUrl']),'/');
		}

		if(!is_dir($sCachePath = $this->getRealPath($aConfiguration['cachePath'])))throw new \Exception('cachePath is not a valid directory : '.$aConfiguration['cachePath']);
		else $aConfiguration['cachePath'] = $sCachePath.DIRECTORY_SEPARATOR;

		if(!is_dir($sAssetsPath = $this->getRealPath($aConfiguration['assetsPath'])))throw new \Exception('assetsPath is not a valid directory : '.$aConfiguration['assetsPath']);
		else $aConfiguration['assetsPath'] = $sAssetsPath.DIRECTORY_SEPARATOR;

		if(!is_array($aConfiguration['rendererToStrategy']))throw new \Exception('rendererToStrategy is not an array : '.gettype($aConfiguration['rendererToStrategy']));
		if(!is_array($aConfiguration['mediaExt']))throw new \Exception('mediaExt is not an array : '.gettype($aConfiguration['mediaExt']));
		$this->configuration = $aConfiguration;
	}

	/**
	 * @param string $sControllerName
	 * @throws \Exception
	 * @return \AssetsBundle\Service\Service
	 */
	public function setControllerName($sControllerName){
		if(!is_string($sControllerName) || empty($sControllerName))throw new \Exception('Controller name is not valid');
		$this->controllerName = $sControllerName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getControllerName(){
		return $this->controllerName?:'no_controller';
	}

	/**
	 * @param string $sActionName
	 * @throws \Exception
	 * @return \AssetsBundle\Service\Service
	 */
	public function setActionName($sActionName){
		if(!is_string($sActionName) || empty($sActionName))throw new \Exception('Action name is not valid');
		$this->actionName = $sActionName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getActionName(){
		return $this->actionName?:'no_action';
	}

	/**
	 * @param \Zend\View\Renderer\RendererInterface $oRenderer
	 * @return \AssetsBundle\Service\Service
	 */
	public function setRenderer(\Zend\View\Renderer\RendererInterface $oRenderer){
		$this->renderer = $oRenderer;
		return $this;
	}

	/**
	 * @return \Zend\View\Renderer\RendererInterface
	 */
	public function getRenderer(){
		return $this->renderer;
	}

	/**
	 * Set filters for "Css" and "Js" assets
	 * @param array $aFilters
	 * @throws \Exceptions
	 * @return \AssetsBundle\Service\Service
	 */
	public function setFilters(array $aFilters){
		if(!is_array($aFilters) || !isset($aFilters[self::ASSET_CSS],$aFilters[self::ASSET_JS],$aFilters[self::ASSET_LESS])
		|| !($aFilters[self::ASSET_CSS] instanceof \AssetsBundle\Service\Filter\FilterInterface)
		|| !($aFilters[self::ASSET_JS] instanceof \AssetsBundle\Service\Filter\FilterInterface)
		|| !($aFilters[self::ASSET_LESS] instanceof \AssetsBundle\Service\Filter\FilterInterface))throw new \Exception('Filters are not valid');
		$this->assetFilters = $aFilters;
		return $this;
	}

	/**
	 * @param string $sAssetType
	 * @throws \Exception
	 * @return \AssetsBundle\Service\Filter\FilterInterface
	 */
	public function getFilter($sAssetType){
		if(!self::assetTypeExists($sAssetType))throw new \Exception('Asset\'s type is not valid : '.$sAssetType);
		if(!($this->assetFilters[$sAssetType] instanceof \AssetsBundle\Service\Filter\FilterInterface))throw new \Exception('Filters are not defined');
		return $this->assetFilters[$sAssetType];
	}

	/**
	 * Render Js and css assets
	 * @param array $aModules
	 * @return \AssetsBundle\Service\Service
	 */
	public function renderAssets(array $aModules){
		//Production : check already cached files
		if($this->configuration['production']){
			$sCssCacheFile = md5($this->getControllerName().$this->getActionName()).'.'.self::ASSET_CSS;
			$sJsCacheFile = md5($this->getControllerName().$this->getActionName()).'.'.self::ASSET_JS;
			if($this->getRealPath($this->configuration['cachePath'].$sCssCacheFile)
			&& $this->getRealPath($this->configuration['cachePath'].$sJsCacheFile))return $this->displayAssets(array(
				self::ASSET_CSS => $sCssCacheFile,
				self::ASSET_JS => $sJsCacheFile,
			));
		}

		$aAssets = array(self::ASSET_CSS => array(),self::ASSET_LESS => array(),self::ASSET_JS => array(), self::ASSET_MEDIA => array());
		foreach($aModules as $sModuleName){
			if(isset($this->configuration['assets'][$sModuleName = strtolower($sModuleName)])){
				//Module configuration
				$aConfigurationModule = $this->configuration['assets'][$sModuleName];
				if(!empty($aConfigurationModule[self::ASSET_CSS]) && is_array($aConfigurationModule[self::ASSET_CSS]))$aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS],$aConfigurationModule[self::ASSET_CSS]);
				if(!empty($aConfigurationModule[self::ASSET_LESS]) && is_array($aConfigurationModule[self::ASSET_LESS]))$aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS],$aConfigurationModule[self::ASSET_LESS]);
				if(!empty($aConfigurationModule[self::ASSET_JS]) && is_array($aConfigurationModule[self::ASSET_JS]))$aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS],$aConfigurationModule[self::ASSET_JS]);
				if(!empty($aConfigurationModule[self::ASSET_MEDIA]) && is_array($aConfigurationModule[self::ASSET_MEDIA]))$aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA],$aConfigurationModule[self::ASSET_MEDIA]);

				//Controller configuration
				if(isset($aConfigurationModule[$this->getControllerName()])){
					$aConfigurationController = $aConfigurationModule[$this->getControllerName()];
					if(!empty($aConfigurationController[self::ASSET_CSS]) && is_array($aConfigurationController[self::ASSET_CSS]))$aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS],$aConfigurationController[self::ASSET_CSS]);
					if(!empty($aConfigurationController[self::ASSET_LESS]) && is_array($aConfigurationController[self::ASSET_LESS]))$aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS],$aConfigurationController[self::ASSET_LESS]);
					if(!empty($aConfigurationController[self::ASSET_JS]) && is_array($aConfigurationController[self::ASSET_JS]))$aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS],$aConfigurationController[self::ASSET_JS]);
					if(!empty($aConfigurationController[self::ASSET_MEDIA]) && is_array($aConfigurationController[self::ASSET_MEDIA]))$aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA],$aConfigurationController[self::ASSET_MEDIA]);

					//Action configuration
					if(isset($aConfigurationController[$this->getActionName()])){
						$aConfigurationAction = $aConfigurationController[$this->getActionName()];
						if(!empty($aConfigurationAction[self::ASSET_CSS]) && is_array($aConfigurationAction[self::ASSET_CSS]))$aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS],$aConfigurationAction[self::ASSET_CSS]);
						if(!empty($aConfigurationAction[self::ASSET_LESS]) && is_array($aConfigurationAction[self::ASSET_LESS]))$aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS],$aConfigurationAction[self::ASSET_LESS]);
						if(!empty($aConfigurationAction[self::ASSET_JS]) && is_array($aConfigurationAction[self::ASSET_JS]))$aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS],$aConfigurationAction[self::ASSET_JS]);
						if(!empty($aConfigurationAction[self::ASSET_MEDIA]) && is_array($aConfigurationAction[self::ASSET_MEDIA]))$aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA],$aConfigurationAction[self::ASSET_MEDIA]);
					}
				}
			}
		}

		//Manage images caching
		$this->cacheMedias($this->getValidAssets(array_unique($aAssets[self::ASSET_MEDIA]),self::ASSET_MEDIA));

		//Manage less files caching
		$aAssets[self::ASSET_CSS][] = $this->cacheLess($this->getValidAssets(array_unique($aAssets[self::ASSET_LESS]),self::ASSET_LESS));

		//Manage css & js file caching
		return $this->displayAssets(array_unique(array_filter(array_merge(
			$this->cacheAssets($this->getValidAssets(array_unique(array_filter($aAssets[self::ASSET_CSS])),self::ASSET_CSS),self::ASSET_CSS),
			$this->cacheAssets($this->getValidAssets(array_unique(array_filter($aAssets[self::ASSET_JS])),self::ASSET_JS),self::ASSET_JS)
		))));
	}

	/**
	 * Optimise and cache "Css" & "Js" assets
	 * @param array $aAssetsPath : file to cache
	 * @param string $sTypeAsset : asset's type to cache (self::ASSET_CSS or self::ASSET_JS)
	 * @throws \Exception
	 * @return string
	 */
	private function cacheAssets(array $aAssetsPath,$sTypeAsset){
		if(!is_array($aAssetsPath))throw new \Exception('AssetsPath is not an array : '.gettype($aAssetsPath));
		if(!self::assetTypeExists($sTypeAsset))throw new \Exception('Asset\'s type is undefined : '.$sTypeAsset);
		$aReturn = array();

		//No assets to cache
		if(empty($aAssetsPath))return $aReturn;

		//Production cache file
		$sCacheFile = md5($this->getControllerName().$this->getActionName()).'.'.$sTypeAsset;
		$aCacheAssets = array();

		//Allows service store existing assets
		$aAssetsExists = array();

		//Production : check if cache file is up to date
		if($this->configuration['production']
		&& file_exists($this->configuration['cachePath'].$sCacheFile)
		&& ($iLastModifiedCache = filemtime($this->configuration['cachePath'].$sCacheFile)) !== false){
			$bCacheOk = true;
			foreach($aAssetsPath as $sAssetsPath){
				if(!($sAssetsPath = $this->getRealPath($sAssetsPath)))throw new \Exception('File not found : '.$sAssetsPath);
				$aAssetsExists[] = $sAssetsPath;
				if(($iLastModified = filemtime($sAssetsPath)) === false || $iLastModified > $iLastModifiedCache){
					$bCacheOk = false;
					break;
				}
			}
			if($bCacheOk)return array($sCacheFile);
		}

		$bHasContent = false;
		foreach($aAssetsPath as $sAssetsPath){
			//Absolute path
			if(!in_array($sAssetsPath,$aAssetsExists) && !($sAssetsPath = $this->getRealPath($sAssetsPath)))throw new \Exception('File not found : '.$sAssetsPath);

			//Developpement : don't optimize assets
			if(!$this->configuration['production']){
				//If asset is already a cache file
				if(strpos($sAssetsPath,$this->configuration['cachePath']) !== false)$sAssetRelativePath = str_ireplace(
					array($this->configuration['cachePath'],'.less'),
					array('','.css'),
					$sAssetsPath
				);
				else $sAssetRelativePath = str_ireplace(
					array($this->configuration['assetsPath'],getcwd(),DIRECTORY_SEPARATOR),
					array('','','_'),
					$sAssetsPath
				);

				$this->copyIntoCache($sAssetsPath, $this->configuration['cachePath'].$sAssetRelativePath);
				$aCacheAssets[] = $sAssetRelativePath;
				continue;
			}

			//Production : optimize assets
			if(($sAssetContent = file_get_contents($sAssetsPath)) === false)throw new \Exception('Unable to get file content : '.$sAssetsPath);

			switch($sTypeAsset){
				case self::ASSET_CSS:
					$sCacheContent = trim($this->getFilter(self::ASSET_CSS)->run($sAssetContent));
					break;
				case self::ASSET_JS:
					$sCacheContent = trim($this->getFilter(self::ASSET_JS)->run($sAssetContent)).PHP_EOL.'//'.PHP_EOL;
					break;
			}
			$sCacheContent = trim($sCacheContent);
			if(empty($sCacheContent))continue;
			else $bHasContent = true;
			if(!file_put_contents($this->configuration['cachePath'].$sCacheFile,$sCacheContent.PHP_EOL,FILE_APPEND))throw new \Exception('Unable to write in file : '.$this->configuration['cachePath'].$sCacheFile);
		}
		return $this->configuration['production']?($bHasContent?array($sCacheFile):array()):$aCacheAssets;
	}

	/**
	 * Optimise and cache "Medias" assets
	 * @param array $aMediasPath : medias to cache
	 * @throws \Exception
	 * @return \AssetsBundle\Service\Service
	 */
	private function cacheMedias(array $aMediasPath){
		foreach($aMediasPath as $sMediaPath){
			//Absolute path
			if(!($sMediaPath = $this->getRealPath($sMediaPath)))throw new \Exception('File not found : '.$sMediaPath);
			//Define cache path
			$sCacheMediaPath = str_ireplace($this->configuration['assetsPath'],$this->configuration['cachePath'],$sMediaPath);

			//If media is not in asset directory
			if($sCacheMediaPath === $sMediaPath)$sCacheMediaPath = str_ireplace(getcwd(),$this->configuration['cachePath'],$sMediaPath);

			//Media isn't cached or it's deprecated
			if($this->hasToCache($sMediaPath,$sCacheMediaPath))switch($sExtension = strtolower(pathinfo($sMediaPath,PATHINFO_EXTENSION))){
				//Images
				case 'jpg':
				case 'png':
				case 'gif':
				case 'cur':
					$this->copyIntoCache($sMediaPath,$sCacheMediaPath);
					break;
				//Others
				default:
					if(in_array($sExtension,$this->configuration['mediaExt']))$this->copyIntoCache($sMediaPath,$sCacheMediaPath);
					else throw new \Exception('Extension is not valid ('.join(', ',$this->configuration['mediaExt']).') : '.$sExtension);
					break;
			}
		}
		return $this;
	}

	/**
	 * Retrieve assets realpath
	 * @param array $aAssets
	 * @param string $sTypeAsset
	 * @throws \Exception
	 * @return array
	 */
	private function getValidAssets(array $aAssets,$sTypeAsset){
		if(!self::assetTypeExists($sTypeAsset))throw new \Exception('Asset\'s type is undefined : '.$sTypeAsset);
		$aReturn = array();
		foreach($aAssets as $sAssetsPath){
			if(!($sRealAssetsPath =  $this->getRealPath($sAssetsPath)))throw new \Exception('Asset file not found : '.$sAssetsPath);
			if(is_dir($sRealAssetsPath))$aReturn = array_merge($aReturn,$this->getAssetsFromDirectory($sRealAssetsPath, $sTypeAsset));
			else $aReturn[] = $sRealAssetsPath;
		}
		return array_unique(array_filter($aReturn));
	}

	/**
	 * Retrieve assets from a directory
	 * @param string $sDirPath
	 * @param string $sTypeAsset
	 * @throws \Exception
	 * @return array
	 */
	private function getAssetsFromDirectory($sDirPath,$sTypeAsset){
		if(!is_string($sDirPath) || !($sDirPath = $this->getRealPath($sDirPath)) && !is_dir($sDirPath))throw new \Exception('Directory not found : '.$sDirPath);
		if(!self::assetTypeExists($sTypeAsset))throw new \Exception('Asset\'s type is undefined : '.$sTypeAsset);
		$oDirIterator = new \DirectoryIterator($sDirPath);
		$aAssets = array();
		foreach($oDirIterator as $oFile){
			/* @var $oFile \DirectoryIterator */
			if($oFile->isFile())switch($sTypeAsset){
				case self::ASSET_CSS:
				case self::ASSET_JS:
				case self::ASSET_LESS:
					if(strtolower(pathinfo($oFile->getFilename(),PATHINFO_EXTENSION)) === $sTypeAsset)$aAssets[] = $oFile->getPathname();
					break;
				case self::ASSET_MEDIA:
					if(in_array(
						$sExtension = strtolower(pathinfo($oFile->getFilename(),PATHINFO_EXTENSION)),
						$this->configuration['mediaExt']
					))$aAssets[] = $oFile->getPathname();
					break;
			}
		}
		return $aAssets;
	}


	/**
	 * Optimise and cache "Less" assets
	 * @param array $aAssetsPath : assets to cache
	 * @throws \Exception
	 * @return \AssetsBundle\Service\Service
	 */
	private function cacheLess(array $aAssetsPath){
		//Create global import file for Less assets
		$sCacheFile = md5($this->getControllerName().$this->getActionName()).'.'.self::ASSET_LESS;
		if(!$this->configuration['production'])$sCacheFile = 'dev_'.$sCacheFile;

		//Allows service to store existing assets
		$aAssetsExists = array();

		//Check if cache file has to been updated
		if(file_exists($this->configuration['cachePath'].$sCacheFile) && ($iLastModifiedCache = filemtime($this->configuration['cachePath'].$sCacheFile)) !== false){
			$bCacheOk = true;
			foreach($aAssetsPath as $sAssetsPath){
				if(!($sAssetsPath = $this->getRealPath($sAssetsPath)))throw new \Exception('File not found : '.$sAssetsPath);
				$aAssetsExists[] = $sAssetsPath;
				if(($iLastModified = filemtime($sAssetsPath)) === false || $iLastModified > $iLastModifiedCache){
					$bCacheOk = false;
					break;
				}
				//If file is up to date, check if it doesn't contain @imports
				else{
					if(($sAssetContent = file_get_contents($sAssetsPath)) === false)throw new \Exception('Unable to get file content : '.$sAssetsPath);
					if(preg_match_all('/@import([^;]*);/', $sAssetContent, $aImports,PREG_PATTERN_ORDER)){
						$sAssetDirPath = realpath(pathinfo($sAssetsPath,PATHINFO_DIRNAME)).DIRECTORY_SEPARATOR;
						foreach($aImports[1] as $sImport){
							$sImport = trim(str_ireplace(array('"','\'','url','(',')'),'',$sImport));
							//Check if file to be imported exists
							if(
								!($sImportPath = $this->getRealPath($sImport))
								&& !file_exists($sImportPath = $sAssetDirPath.$sImport) //Relative path to less file directory
							)throw new \Exception('File not found : '.$sImportPath);
							if(($iLastModified = filemtime($sImportPath)) === false || $iLastModified > $iLastModifiedCache){
								$bCacheOk = false;
								break;
							}
						}
						if(!$bCacheOk)break;
					}
				}
			}
			if($bCacheOk)return $this->configuration['cachePath'].$sCacheFile;
		}
		$sImportContent = '';
		foreach($aAssetsPath as $sAssetsPath){
			//Absolute path
			if(!in_array($sAssetsPath,$aAssetsExists) && !($sAssetsPath = $this->getRealPath($sAssetsPath)))throw new \Exception('File not found : '.$sAssetsPath);
			$sImportContent .= '@import "'.str_ireplace(getcwd(), '', $sAssetsPath).'";'.PHP_EOL;
		};
		$sImportContent = trim($sImportContent);
		if(empty($sImportContent) || !($sImportContent = $this->getFilter(self::ASSET_LESS)->run($sImportContent)))return null;

		//Rewrite urls
		$sImportContent = preg_replace_callback(
			'/url\(([^\)]+)\)/',
			array($this,'rewriteUrl'),
			$sImportContent
		);

		if(!file_put_contents($sCacheFile = $this->configuration['cachePath'].$sCacheFile,$sImportContent))throw new \Exception('Unable to write in file : '.$sCacheFile);
		return $sCacheFile;
	}

	/**
	 * Show assets through View Helper
	 * @param array $aAssets
	 * @throws \Exception
	 * @return \AssetsBundle\Service\Service
	 */
	public function displayAssets(array $aAssets){
		if(!array_key_exists($sRendererName = get_class($this->getRenderer()), $this->configuration['rendererToStrategy']))throw new \Exception(\Exception::ERREUR_TYPE_ENTITE);
		if(!isset($this->strategy[$sRendererName])) {
			$sStrategyClass = $this->configuration['rendererToStrategy'][$sRendererName];
			if(!class_exists($sStrategyClass, true))throw new \Exception('Strategy Class not found : '.$sStrategyClass);
			$this->strategy[$sRendererName] = new $sStrategyClass();
			if(!($this->strategy[$sRendererName] instanceof \AssetsBundle\View\Strategy\StrategyInterface))throw new \Exception('Strategy doesn\'t implement \AssetsBundle\View\Strategy\StrategyInterface : '.$sStrategyClass);
		}

		/** @var $oStrategy \Neilime\AsseticBundle\View\StrategyInterface */
		$oStrategy = $this->strategy[$sRendererName]->setBaseUrl($this->configuration['cacheUrl'])->setRenderer($this->getRenderer());
		foreach($aAssets as $sAssetsPath){
			$oStrategy->renderAsset(
				$sAssetsPath,
				file_exists($sAbsolutePath = $this->configuration['cachePath'].DIRECTORY_SEPARATOR.$sAssetsPath)?filemtime($sAbsolutePath):time()
			);
		}
		return $this;
	}

	/**
	 * Check if asset's type is valid
	 * @param string $sAssetType
	 * @return boolean
	 */
	private static function assetTypeExists($sAssetType){
		switch($sAssetType){
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
	 * Check if a file is already cached and if it's not outdated
	 * @param string $sFilePath
	 * @param string $sCachePath
	 * @throws \Exception
	 * @return boolean
	 */
	private function hasToCache($sFilePath,$sCachePath){
		if(!file_exists($sFilePath))throw new \Exception('File not found : '.$sFilePath);
		return
		!file_exists($sCachePath)
		|| ($iLastModified = filemtime($sFilePath)) === false
		|| ($iLastModifiedCompare = filemtime($sCachePath)) === false
		|| $iLastModified > $iLastModifiedCompare;
	}

	/**
	 * Allows service to move a file in cache directory, keeping the same directory structure
	 * @param string $sFilePath
	 * @param string $sCachePath
	 * @throws \Exception
	 * @return \AssetsBundle\Service\Service
	 */
	private function copyIntoCache($sFilePath,$sCachePath){
		if(!file_exists($sFilePath))throw new \Exception('File not found : '.$sFilePath);
		if(!$this->hasToCache($sFilePath,$sCachePath))return $this;
		//Create directory structure if it doesn't exist in cache
		if(!is_dir($sDirPath = pathinfo($sCachePath,PATHINFO_DIRNAME))){
			$sCurrentPath = $this->configuration['cachePath'];
			//Directory traversal
			foreach(explode(DIRECTORY_SEPARATOR,str_ireplace($sCurrentPath,'',$sDirPath)) as $sDirPathPart){
				//Create current directory if it doesn't exist
				if(!is_dir($sCurrentPath = $sCurrentPath.DIRECTORY_SEPARATOR.$sDirPathPart)
				&& !mkdir($sCurrentPath))throw new \Exception('Unable to create directory : '.$sCurrentPath);
			}
		}
		if(!copy($sFilePath,$sCachePath) || !file_exists($sCachePath))throw new \Exception('Unable to create file : '.$sCachePath);
		return $this;
	}

	/**
	 * Try to retrieve realpath for a given path (manage @zfRootPath & @zfAssetsPath)
	 * @param string $sPath
	 * @throws \Exception
	 * @return string|boolean : real path or false if not found
	 */
	private function getRealPath($sPath){
		if(empty($sPath) || !is_string($sPath))throw new \Exception('Path is not valid : '.gettype($sPath));

		//If path is "/", assets path is prefered
		if($sPath === '/'){
			if(!isset($this->configuration['assetsPath']))throw new \Exception('Asset Path is undefined');
			return $this->configuration['assetsPath'];
		}

		if(file_exists($sPath))return realpath($sPath);

		if(strpos($sPath,'@zfRootPath') !== false)$sPath = str_ireplace('@zfRootPath',getcwd(),$sPath);
		if(strpos($sPath,'@zfAssetsPath') !== false){
			if(!isset($this->configuration['assetsPath']))throw new \Exception('Asset Path is undefined');
			$sPath = str_ireplace('@zfAssetsPath',$this->configuration['assetsPath'],$sPath);
		}
		if(($sRealPath = realpath($sPath)) !== false)return $sRealPath;
		//Try to guess real path with root path or asset path (if defined)
		if(file_exists($sRealPath = getcwd().DIRECTORY_SEPARATOR.$sPath))return realpath($sRealPath);
		elseif(isset($this->configuration['assetsPath']) && file_exists($sRealPath = $this->configuration['assetsPath'].$sPath))return realpath($sRealPath);
		else return false;
	}

	/**
	 * Rewrite url to match with cache path
	 * @param array $aMatches
	 * @throws \Exception
	 * @return string
	 */
	private function rewriteUrl(array $aMatches){
		if(!isset($aMatches[1]))throw new \Exception('Url match is not valid');

		//Remove quotes & double quotes from url
		$aMatches[1] = str_ireplace(array('"','\''),'', $aMatches[1]);
		$sRootPath = realpath(getcwd().DIRECTORY_SEPARATOR.'..');
		$sAssetRelativePath = str_ireplace(array($sRootPath,DIRECTORY_SEPARATOR),array('','/'), $this->configuration['assetsPath']);

		$sUrl = str_ireplace('..'.$sAssetRelativePath,'', $aMatches[1]);

		//Url does not point to the assets directory
		if($sUrl == $aMatches[1])$sUrl = str_ireplace('..'.str_ireplace(array($sRootPath,DIRECTORY_SEPARATOR),array('','/'), getcwd()).'/','', $aMatches[1]);
		return str_ireplace($aMatches[1],$this->configuration['cacheUrl'].$sUrl,$aMatches[0]);
	}
}
