<?php
namespace Neilime\AssetsBundle\Service;
class Service{
	const ASSET_CSS = 'css';
	const ASSET_JS = 'js';
	const ASSET_LESS = 'less';
	const ASSET_IMG = 'img';
	
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
	protected $minifiers = array(
		self::ASSET_CSS => null,
		self::ASSET_JS => null		
	);
	
	/**
	 * 
	 * @param array $aConfiguration
	 * @throws \Exception
	 */
	public function __construct(array $aConfiguration){
		//Check configuration
		if(!isset($aConfiguration['cachePath'],$aConfiguration['webCachePath'],$aConfiguration['baseUrl'],$aConfiguration['rendererToStrategy'],$aConfiguration['imgExt'])
		|| !is_dir($aConfiguration['cachePath'])
		|| !is_dir($aConfiguration['baseUrl'])
		|| !is_array($aConfiguration['rendererToStrategy'])
		|| !is_array($aConfiguration['imgExt'])
		)throw new \Exception('Error in configuration');
		$this->configuration = $aConfiguration;
	}
	
	/**
	 * @param string $sControllerName
	 * @return \AssetsBundle\Service
	 */
	public function setControllerName($sControllerName){
		$this->controllerName = $sControllerName;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getControllerName(){
		return $this->controllerName;
	}
	
	/**
	 * @param string $sActionName
	 * @return \AssetsBundle\Service
	 */
	public function setActionName($sActionName){
		$this->actionName = $sActionName;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getActionName(){
		return $this->actionName;
	}
	
	/**
	 * @param \Zend\View\Renderer\RendererInterface $oRenderer
	 * @return \AssetsBundle\Service
	 */
	public function setRenderer($oRenderer){
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
	 * Set minifiers for "Css" and "Js" assets
	 * @param array $aMinifiers
	 * @throws \Exception
	 * @return \Neilime\AssetsBundle\Service\Service
	 */
	public function setMinifiers(array $aMinifiers){
		if(!is_array($aMinifiers) || !isset($aMinifiers[self::ASSET_CSS],$aMinifiers[self::ASSET_JS])
		|| !($aMinifiers[self::ASSET_CSS] instanceof \Neilime\AssetsBundle\Service\Minifier\MinifierInterface)
		|| !($aMinifiers[self::ASSET_JSS] instanceof \Neilime\AssetsBundle\Service\Minifier\MinifierInterface))throw new \Exception('Minifiers are not valid');
		$this->minifiers = $aMinifiers;
		return $this;
	}
	
	/**
	 * @param string $sAssetType
	 * @throws \Exception
	 * @return \Neilime\AssetsBundle\Service\Minifier\MinifierInterface
	 */
	public function getMinifier($sAssetType){
		if(self::assetTypeExists($sAssetType))throw new \Exception('Asset\'s type is not valid : '.$sAssetType);
		if(!($this->minifiers[$sAssetType] instanceof \Neilime\AssetsBundle\Service\Minifier\MinifierInterface))throw new \Exception('Minifiers are not defined');
		return $this->minifiers[$sAssetType];
	}
	
	
	public function renderAssets(array $aModules){
		//Production : check des files already in cache
		if($this->configuration['production']){
			$sCssCacheFile = md5($this->getControllerName().$this->getActionName()).'.'.self::ASSET_CSS;
			$sJsCacheFile = md5($this->getControllerName().$this->getActionName()).'.'.self::ASSET_JS;
			if(file_exists($this->configuration['cachePath'].$sCssCacheFile) && file_exists($this->configuration['cachePath'].$sJsCacheFile))return $this->displayAssets(array(
					self::ASSET_CSS => $sCssCacheFile,
					self::ASSET_JS => $sJsCacheFile,
			));
		}
	
		$aAssets = array(self::ASSET_CSS => array(),self::ASSET_LESS => array(),self::ASSET_JS => array(), self::ASSET_IMG => array());
		foreach($aModules as $sModuleName){
			if(isset($this->configuration['assets'][$sModuleName = strtolower($sModuleName)])){
				//Module configuration
				$aConfigurationModule = $this->configuration['assets'][$sModuleName];
				if(!empty($aConfigurationModule[self::ASSET_CSS]) && is_array($aConfigurationModule[self::ASSET_CSS]))$aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS],$aConfigurationModule[self::ASSET_CSS]);
				if(!empty($aConfigurationModule[self::ASSET_LESS]) && is_array($aConfigurationModule[self::ASSET_LESS]))$aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS],$aConfigurationModule[self::ASSET_LESS]);
				if(!empty($aConfigurationModule[self::ASSET_JS]) && is_array($aConfigurationModule[self::ASSET_JS]))$aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS],$aConfigurationModule[self::ASSET_JS]);
				if(!empty($aConfigurationModule[self::ASSET_IMG]) && is_array($aConfigurationModule[self::ASSET_IMG]))$aAssets[self::ASSET_IMG] = array_merge($aAssets[self::ASSET_IMG],$aConfigurationModule[self::ASSET_IMG]);
	
				//Controller configuration 
				if(isset($aConfigurationModule[$this->getControllerName()])){
					$aConfigurationController = $aConfigurationModule[$this->getControllerName()];
					if(!empty($aConfigurationController[self::ASSET_CSS]) && is_array($aConfigurationController[self::ASSET_CSS]))$aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS],$aConfigurationController[self::ASSET_CSS]);
					if(!empty($aConfigurationController[self::ASSET_LESS]) && is_array($aConfigurationController[self::ASSET_LESS]))$aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS],$aConfigurationController[self::ASSET_LESS]);
					if(!empty($aConfigurationController[self::ASSET_JS]) && is_array($aConfigurationController[self::ASSET_JS]))$aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS],$aConfigurationController[self::ASSET_JS]);
					if(!empty($aConfigurationController[self::ASSET_IMG]) && is_array($aConfigurationController[self::ASSET_IMG]))$aAssets[self::ASSET_IMG] = array_merge($aAssets[self::ASSET_IMG],$aConfigurationController[self::ASSET_IMG]);
	
					//Action configuration
					if(isset($aConfigurationController[$this->getActionName()])){
						$aConfigurationAction = $aConfigurationController[$this->getActionName()];
						if(!empty($aConfigurationAction[self::ASSET_CSS]) && is_array($aConfigurationAction[self::ASSET_CSS]))$aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS],$aConfigurationAction[self::ASSET_CSS]);
						if(!empty($aConfigurationAction[self::ASSET_LESS]) && is_array($aConfigurationAction[self::ASSET_LESS]))$aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS],$aConfigurationAction[self::ASSET_LESS]);
						if(!empty($aConfigurationAction[self::ASSET_JS]) && is_array($aConfigurationAction[self::ASSET_JS]))$aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS],$aConfigurationAction[self::ASSET_JS]);
						if(!empty($aConfigurationAction[self::ASSET_IMG]) && is_array($aConfigurationAction[self::ASSET_IMG]))$aAssets[self::ASSET_IMG] = array_merge($aAssets[self::ASSET_IMG],$aConfigurationAction[self::ASSET_IMG]);
					}
				}
			}
		}
	
		//Manage images caching
		$this->cacheImages(array_unique($aAssets[self::ASSET_IMG]));
	
		//Manage less files caching
		$aAssets[self::ASSET_CSS][] = $this->cacheLess(array_unique($aAssets[self::ASSET_LESS]));
	
		//Manage css & js file caching
		return $this->displayAssets(array_merge(
			$this->cacheAssets(array_unique($aAssets[self::ASSET_CSS]),self::ASSET_CSS),
			$this->cacheAssets(array_unique($aAssets[self::ASSET_JS]),self::ASSET_JS)
		));
	}
	
	/**
	 * Optimise and cache "Css" & "Js" assets
	 * @param array $aAssetsPath : file to cache
	 * @param string $sTypeAsset : asset's type to cache (self::ASSET_CSS or self::ASSET_JS)
	 * @throws \Exception
	 * @return string
	 */
	private function cacheAssets(array $aAssetsPath,$sTypeAsset){
		if(!self::assetTypeExists($sTypeAsset))throw new \Exception('Asset\'s type is undefined : '.$sTypeAsset);
		$aReturn = array();
	
		//Fichier de cache en production
		$sCacheFile = md5($this->getControllerName().$this->getActionName()).'.'.$sTypeAsset;
		$aCacheAssets = array();
	
		//Permet de stoquer les assets existants
		$aAssetsExists = array();
	
		//Production : vérification du fichier de cache à jour
		if($this->configuration['production']
		&& file_exists($this->configuration['cachePath'].$sCacheFile)
		&& ($iLastModifiedCache = filemtime($this->configuration['cachePath'].$sCacheFile)) !== false){
			$bCacheOk = true;
			foreach($aAssetsPath as $sAssetPath){
				if(!file_exists($sAssetPath) && !file_exists($sAssetPath = $this->configuration['baseUrl'].$sAssetPath))throw new \Exception('File not found : '.$sAssetPath);
				$aAssetsExists[] = $sAssetPath;
				if(($iLastModified = filemtime($sAssetPath)) === false || $iLastModified > $iLastModifiedCache){
					$bCacheOk = false;
					break;
				}
			}
			if($bCacheOk)return array($sCacheFile);
		}
	
		foreach($aAssetsPath as $sAssetPath){
			//Path absolu
			if(
			!in_array($sAssetPath,$aAssetsExists)
			&& !file_exists($sAssetPath)
			&& !file_exists($sAssetPath = $this->configuration['baseUrl'].$sAssetPath)
			) throw new \Exception('File not found : '.$sAssetPath);
	
			//Developpement : pas d'optimisation sur les assets
			if(!$this->configuration['production']){
				//Si l'asset est déja un fichier de cache
				if(strpos($sAssetPath,$this->configuration['cachePath']) !== false)$sAssetRelativePath = str_ireplace(
					array($this->configuration['cachePath'],'.less'),
					array('','.css'),
					$sAssetPath
				);
				else $sAssetRelativePath = str_ireplace(DIRECTORY_SEPARATOR,'_',str_ireplace(
					strpos($sAssetPath,$this->configuration['baseUrl']) !== false?$this->configuration['baseUrl']:__SRV_RACINE__,
					'',
					$sAssetPath
				));
	
				$this->copyIntoCache($sAssetPath, $this->configuration['cachePath'].$sAssetRelativePath);
				$aCacheAssets[] = $sAssetRelativePath;
				continue;
			}
	
			//Production : optimisation des assets
			if(($sAssetContent = file_get_contents($sAssetPath)) === false)throw new \Exception('Unable to get file content : '.$sAssetPath);
			switch($sTypeAsset){
				case self::ASSET_CSS:
					$sCacheContent = $this->getMinifier(self::ASSET_CSS)->minify($sAssetContent);
					break;
				case self::ASSET_JS:
					$sCacheContent = $this->getMinifier(self::ASSET_JS)->minify($sAssetContent);
					break;
			}
			if(!file_put_contents($this->configuration['cachePath'].$sCacheFile,$sCacheContent.PHP_EOL,FILE_APPEND))throw new \Exception('Unable to write in file : '.$this->configuration['cachePath'].$sCacheFile);
		}
		return $this->configuration['production']?array($sCacheFile):$aCacheAssets;
	}
	
	/**
	 * Optimise and cache "Images" assets 
	 * @param array $aImagesPath : les assets to cache (Directories are allowed)
	 * @throws \Exception
	 * @return \Neilime\AssetsBundle\Service
	 */
	private function cacheImages(array $aImagesPath){
		foreach($aImagesPath as $sImagePath){
			//Absolute path 
			if(!file_exists($sImagePath) && !file_exists($sImagePath = $this->configuration['baseUrl'].$sImagePath))throw new \Exception('File not found : '.$sImagePath);
			if(is_dir($sImagePath)){
				$oDirIterator = new \DirectoryIterator($sImagePath);
				foreach($oDirIterator as $oFile){
					/* @var $oFile \DirectoryIterator */
					if($oFile->isFile() && in_array($sExtension = strtolower(pathinfo($oFile->getFilename(), PATHINFO_EXTENSION)),$this->configuration['imgExt'])){
						$sCacheImgPath = str_ireplace($this->configuration['baseUrl'],$this->configuration['cachePath'],$oFile->getFileInfo()->getPathname());
						//Asset isn't cached or it's deprecated
						if($this->hasToCache($oFile->getPathname(),$sCacheImgPath)){
							switch($sExtension){
								case 'png':
								case 'gif':
								case 'cur':
									$this->copyIntoCache($oFile->getPathname(),$sCacheImgPath);
									break;
								default:
									throw new \Exception('Extension is not valid (png,gif,cur): '.$sExtension);
							}
						}
					}
				}
			}
		}
		return $this;
	}
	
	/**
	 * Optimise and cache "Less" assets
	 * @param array $aAssetsPath : assets to cache
	 * @throws \Exception
	 * @return \Neilime\AssetsBundle\Service
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
			foreach($aAssetsPath as $sAssetPath){
				if(!file_exists($sAssetPath) && !file_exists($sAssetPath = $this->configuration['baseUrl'].$sAssetPath))throw new \Exception('File not found : '.$sAssetPath);
				$aAssetsExists[] = $sAssetPath;
				if(($iLastModified = filemtime($sAssetPath)) === false || $iLastModified > $iLastModifiedCache){
					$bCacheOk = false;
					break;
				}
				//If file is up to date, check if it doesn't contain @imports
				else{
					if(($sAssetContent = file_get_contents($sAssetPath)) === false)throw new \Exception('Unable to get file content : '.$sAssetPath);
					if(preg_match_all('/@import([^;]*);/', $sAssetContent, $aImports,PREG_PATTERN_ORDER)){
						$sAssetDirPath = realpath(pathinfo($sAssetPath,PATHINFO_DIRNAME)).DIRECTORY_SEPARATOR;
						foreach($aImports[1] as $sImport){
							$sImport = trim(str_ireplace(array('"','\'','url','(',')'),'',$sImport));
							//Check if file to be imported exists
							if(
								!file_exists($sImportPath = $sImport) //Absolute path 
								|| !file_exists($sImportPath = $sAssetDirPath.$sImport) //Relative path to less file directory
								|| !file_exists($sImportPath = $this->configuration['baseUrl'].$sImport)//Relative path to baseUrl directory
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
		foreach($aAssetsPath as $sAssetPath){
			//Absolute path 
			if(!in_array($sAssetPath,$aAssetsExists) && !file_exists($sAssetPath) && !file_exists($sAssetPath = $this->configuration['baseUrl'].$sAssetPath))throw new \Exception('File not found : '.$sAssetPath);
			$sImportContent .= '@import "'.str_ireplace($this->configuration['baseUrl'], '', $sAssetPath).'";'.PHP_EOL;
		};
	
		$oLessParser = new \lessc();
		$oLessParser->importDir = $this->configuration['baseUrl'];
		if(!file_put_contents($sCacheFile = $this->configuration['cachePath'].$sCacheFile,$oLessParser->compile($sImportContent)))throw new \Exception('Unable to write in file : '.$sCacheFile);
		return $sCacheFile;
	}
	
	/**
	 * Show assets through View Helper
	 * @param array $aAssets
	 * @throws \Exception
	 * @return \Neilime\AssetsBundle\Service
	 */
	public function displayAssets(array $aAssets){
		if(!array_key_exists($sRendererName = get_class($this->getRenderer()), $this->configuration['rendererToStrategy']))throw new \Exception(\SLN\Exception::ERREUR_TYPE_ENTITE);
		if(!isset($this->strategy[$sRendererName])) {
			$sStrategyClass = $this->configuration['rendererToStrategy'][$sRendererName];
			if(!class_exists($sStrategyClass, true))throw new \Exception('Strategy Class not found : '.$sStrategyClass);
			$this->strategy[$sRendererName] = new $sStrategyClass();
			if(!($this->strategy[$sRendererName] instanceof \Neilime\AssetsBundle\View\Strategy\StrategyInterface))throw new \Exception('Strategy doesn\'t implement \Neilime\AssetsBundle\View\Strategy\StrategyInterface : '.$sStrategyClass);
		}
	
		/** @var $oStrategy \Neilime\AsseticBundle\View\StrategyInterface */
		$oStrategy = $this->strategy[$sRendererName]->setBaseUrl($this->configuration['webCachePath'])->setRenderer($this->getRenderer());
		foreach($aAssets as $sAssetPath){
			$oStrategy->renderAsset($sAssetPath);
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
			case self::ASSET_IMG:
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
	 * @return \Neilime\AssetsBundle\Service
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
}