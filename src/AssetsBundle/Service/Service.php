<?php
namespace AssetsBundle\Service;
class Service{
	//Assets
	const ASSET_CSS = 'css';
	const ASSET_JS = 'js';
	const ASSET_LESS = 'less';
	const ASSET_MEDIA = 'media';

	const NO_ACTION = 'no_action';
	const NO_CONTROLLER = 'no_controller';

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * Clean assets configuration
	 * @var array
	 */
	protected $assetsConfiguration;

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
	 * @throws \InvalidArgumentException
	 */
	public function __construct(array $aConfiguration){
		//Check configuration entries
		if(!isset($aConfiguration['cachePath'],$aConfiguration['cacheUrl'],$aConfiguration['rendererToStrategy'],$aConfiguration['mediaExt']))throw new \InvalidArgumentException('Error in configuration');

		//Check configuration values
		if(!isset($aConfiguration['assets']))$aConfiguration['assets'] = array();
		elseif(!is_array($aConfiguration['assets']))throw new \InvalidArgumentException('Assets configuration expects array, "'.$aConfiguration['assets'].'" given');

		if(strpos($aConfiguration['cacheUrl'],'@zfBaseUrl') !== false){
			if(!isset($aConfiguration['basePath']))throw new \InvalidArgumentException('Base path is undefined in configuration');
			$aConfiguration['basePath'] = rtrim($aConfiguration['basePath'], '/');
			$aConfiguration['cacheUrl'] = $aConfiguration['basePath'].'/'.ltrim(str_ireplace('@zfBaseUrl','', $aConfiguration['cacheUrl']),'/');
		}

		if(!is_dir($sCachePath = $this->getRealPath($aConfiguration['cachePath'])))throw new \InvalidArgumentException('"cachePath" config expects a valid directory path, "'.$aConfiguration['cachePath'].'" given');
		else $aConfiguration['cachePath'] = $sCachePath.DIRECTORY_SEPARATOR;

		if(!is_array($aConfiguration['rendererToStrategy']))throw new \InvalidArgumentException('rendererToStrategy is not an array : '.gettype($aConfiguration['rendererToStrategy']));
		if(!is_array($aConfiguration['mediaExt']))throw new \InvalidArgumentException('mediaExt is not an array : '.gettype($aConfiguration['mediaExt']));

		if(isset($aConfiguration['assetsPath'])){
			$sAssetsPath = $aConfiguration['assetsPath'];
			unset($aConfiguration['assetsPath']);
		}

		$this->configuration = $aConfiguration;
		$this->setAssetsPath(isset($sAssetsPath)?$sAssetsPath:null);

		//Check filters
		if(isset($aConfiguration['filters'])){
			if(!is_array($aConfiguration['filters']))throw new \InvalidArgumentException('Filters configuration expects array, "'.gettype($aConfiguration['filters']).'" given');
			$this->setFilters($aConfiguration['filters']);
		}
	}

	/**
	 * @return boolean
	 */
	public function isProduction(){
		return !!$this->configuration['production'];
	}

	/**
	 * @param string $sControllerName
	 * @throws \InvalidArgumentException
	 * @return \AssetsBundle\Service\Service
	 */
	public function setControllerName($sControllerName){
		if(!is_string($sControllerName) || empty($sControllerName))throw new \InvalidArgumentException('Controller name is not valid');
		$this->controllerName = $sControllerName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getControllerName(){
		return $this->controllerName?:self::NO_CONTROLLER;
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
		return $this->actionName?:self::NO_ACTION;
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
	 * Set filters
	 * @param array $aFilters
	 * @throws \InvalidArgumentException
	 * @return \AssetsBundle\Service\Service
	 */
	public function setFilters(array $aFilters){
		foreach($aFilters as $sFilterType => $oFilter){
			if(!self::assetTypeExists($sFilterType) && !in_array($sFilterType,$this->configuration['mediaExt']))throw new \InvalidArgumentException(sprintf(
				'Filter\'s type is not valid expects "%s", "%s" given',
				join(', ',array_merge(array(self::ASSET_CSS,self::ASSET_JS,self::ASSET_LESS),$this->configuration['mediaExt'])),
				$sFilterType
			));
			if($oFilter instanceof \AssetsBundle\Service\Filter\FilterInterface)$this->assetFilters[$sFilterType] = $oFilter;
			else throw new \InvalidArgumentException(sprintf(
				'Filter expects \AssetsBundle\Service\Filter\FilterInterface, "%s" given',
				is_object($oFilter)?get_class($oFilter):gettype($oFilter)
			));
		}
		return $this;
	}

	/**
	 * @param string $sFilterType
	 * @throws \InvalidArgumentException
	 * @return \AssetsBundle\Service\Filter\FilterInterface
	 */
	public function getFilter($sFilterType){
		if(!self::assetTypeExists($sFilterType) && !in_array($sFilterType,$this->configuration['mediaExt']))throw new \InvalidArgumentException(sprintf(
			'Filter\'s type expects "%s", "%s" given',
			join(', ',array_merge(array(self::ASSET_CSS,self::ASSET_JS,self::ASSET_LESS),$this->configuration['mediaExt'])),
			$sFilterType
		));
		if(!$this->hasFilter($sFilterType))throw new \InvalidArgumentException('Filter "'.$sFilterType.'" is not defined');
		return $this->assetFilters[$sFilterType];
	}

	/**
	 * @param string $sFilterType
	 * @throws \InvalidArgumentException
	 * @return boolean
	 */
	public function hasFilter($sFilterType){
		if(!self::assetTypeExists($sFilterType) && !in_array($sFilterType,$this->configuration['mediaExt']))throw new \InvalidArgumentException(sprintf(
			'Filter\'s type is not valid expects "%s", "%s" given',
			join(', ',array_merge(array(self::ASSET_CSS,self::ASSET_JS,self::ASSET_LESS),$this->configuration['mediaExt'])),
			$sFilterType
		));
		return isset($this->assetFilters[$sFilterType]) && $this->assetFilters[$sFilterType] instanceof \AssetsBundle\Service\Filter\FilterInterface;
	}

	/**
	 * @throws \LogicException
	 * @return string
	 */
	public function getCachePath(){
		if(!isset($this->configuration['cachePath']))throw new \LogicException('"cachePath" config is undefined');
		return $this->configuration['cachePath'];
	}

	/**
	 * @param string|null $sAssetsPath
	 * @throws \InvalidArgumentException
	 * @return \AssetsBundle\Service\Service
	 */
	public function setAssetsPath($sAssetsPath = null){
		if(is_null($sAssetsPath))$this->configuration['assetsPath'] = null;
		elseif(is_dir($sAssetsRealPath = $this->getRealPath($sAssetsPath)))$this->configuration['assetsPath'] = $sAssetsRealPath.DIRECTORY_SEPARATOR;
		else throw new \InvalidArgumentException('"assetsPath" config expects a valid directory path, "'.$sAssetsRealPath.'" given');
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function hasAssetsPath(){
		return !empty($this->configuration['assetsPath']);
	}

	/**
	 * @throws \LogicException
	 * @return string
	 */
	public function getAssetsPath(){
		if(!$this->hasAssetsPath())throw new \LogicException('"assetsPath" config is undefined');
		return $this->configuration['assetsPath'];
	}

	/**
	 * @return array
	 */
	public function getAssetsConfiguration(){
		if(isset($this->assetsConfiguration[$this->getControllerName().'-'.$this->getActionName()]))return $this->assetsConfiguration[$this->getControllerName().'-'.$this->getActionName()];
		$aAssets = array(
			self::ASSET_CSS => array(),
			self::ASSET_LESS => array(),
			self::ASSET_JS => array(),
			self::ASSET_MEDIA => array()
		);

		//Common configuration
		$aCommonConfiguration = $this->configuration['assets'];
		if(!empty($aCommonConfiguration[self::ASSET_CSS]) && is_array($aCommonConfiguration[self::ASSET_CSS]))$aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS],$aCommonConfiguration[self::ASSET_CSS]);
		if(!empty($aCommonConfiguration[self::ASSET_LESS]) && is_array($aCommonConfiguration[self::ASSET_LESS]))$aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS],$aCommonConfiguration[self::ASSET_LESS]);
		if(!empty($aCommonConfiguration[self::ASSET_JS]) && is_array($aCommonConfiguration[self::ASSET_JS]))$aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS],$aCommonConfiguration[self::ASSET_JS]);
		if(!empty($aCommonConfiguration[self::ASSET_MEDIA]) && is_array($aCommonConfiguration[self::ASSET_MEDIA]))$aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA],$aCommonConfiguration[self::ASSET_MEDIA]);

		//Controller configuration
		if(isset($aCommonConfiguration[$this->getControllerName()])){
			$aControllerConfiguration = $aCommonConfiguration[$this->getControllerName()];
			if(!empty($aControllerConfiguration[self::ASSET_CSS]) && is_array($aControllerConfiguration[self::ASSET_CSS]))$aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS],$aControllerConfiguration[self::ASSET_CSS]);
			if(!empty($aControllerConfiguration[self::ASSET_LESS]) && is_array($aControllerConfiguration[self::ASSET_LESS]))$aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS],$aControllerConfiguration[self::ASSET_LESS]);
			if(!empty($aControllerConfiguration[self::ASSET_JS]) && is_array($aControllerConfiguration[self::ASSET_JS]))$aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS],$aControllerConfiguration[self::ASSET_JS]);
			if(!empty($aControllerConfiguration[self::ASSET_MEDIA]) && is_array($aControllerConfiguration[self::ASSET_MEDIA]))$aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA],$aControllerConfiguration[self::ASSET_MEDIA]);

			//Action configuration
			if(isset($aControllerConfiguration[$this->getActionName()])){
				$aActionConfiguration = $aControllerConfiguration[$this->getActionName()];
				if(!empty($aActionConfiguration[self::ASSET_CSS]) && is_array($aActionConfiguration[self::ASSET_CSS]))$aAssets[self::ASSET_CSS] = array_merge($aAssets[self::ASSET_CSS],$aActionConfiguration[self::ASSET_CSS]);
				if(!empty($aActionConfiguration[self::ASSET_LESS]) && is_array($aActionConfiguration[self::ASSET_LESS]))$aAssets[self::ASSET_LESS] = array_merge($aAssets[self::ASSET_LESS],$aActionConfiguration[self::ASSET_LESS]);
				if(!empty($aActionConfiguration[self::ASSET_JS]) && is_array($aActionConfiguration[self::ASSET_JS]))$aAssets[self::ASSET_JS] = array_merge($aAssets[self::ASSET_JS],$aActionConfiguration[self::ASSET_JS]);
				if(!empty($aActionConfiguration[self::ASSET_MEDIA]) && is_array($aActionConfiguration[self::ASSET_MEDIA]))$aAssets[self::ASSET_MEDIA] = array_merge($aAssets[self::ASSET_MEDIA],$aActionConfiguration[self::ASSET_MEDIA]);
			}
		}

		$aAssets[self::ASSET_MEDIA] = $this->getValidAssets(array_unique($aAssets[self::ASSET_MEDIA]),self::ASSET_MEDIA);
		$aAssets[self::ASSET_LESS] = $this->getValidAssets(array_unique($aAssets[self::ASSET_LESS]),self::ASSET_LESS);
		$aAssets[self::ASSET_CSS] = $this->getValidAssets(array_unique(array_filter($aAssets[self::ASSET_CSS])),self::ASSET_CSS);
		$aAssets[self::ASSET_JS] = $this->getValidAssets(array_unique(array_filter($aAssets[self::ASSET_JS])),self::ASSET_JS);

		return $this->assetsConfiguration[$this->getControllerName().'-'.$this->getActionName()] = $aAssets;
	}

	/**
	 * Check if assets configuration is the same as last saved configuration
	 * @throws \RuntimeException
	 * @return boolean
	 */
	public function assetsConfigurationHasChanged(array $aAssetsType = null){
		$aAssetsType = $aAssetsType?array_unique($aAssetsType):array(self::ASSET_CSS,self::ASSET_LESS,self::ASSET_JS,self::ASSET_MEDIA);

		//Configuration file
		if(($sConfigContent = file_get_contents($sConfigFilePath = $this->getConfigurationFilePath())) === false)throw new \RuntimeException('Unable to get in file content from file "'.$sConfigFilePath.'"');
		if(
			($aConfig = @json_decode($sConfigContent,true)) === false
			|| !is_array($aConfig)
		)throw new \RuntimeException('Configuration is not a well formed json array "'.$sConfigContent.'"');

		//Get assets configuration
		$aAssets = $this->getAssetsConfiguration();

		//Check if configuration has changed for each type of asset
		foreach($aAssetsType as $sAssetType){
			if(!$this->assetTypeExists($sAssetType))throw new \InvalidArgumentException('Asset type "'.$sAssetType.'" does not exist');
			if(empty($aAssets[$sAssetType]) && !empty($aConfig[$sAssetType]))return true;
			elseif(!empty($aAssets[$sAssetType])){
				if(empty($aConfig[$sAssetType]))return true;
				elseif(
					array_diff($aAssets[$sAssetType], $aConfig[$sAssetType])
					|| array_diff($aConfig[$sAssetType],$aAssets[$sAssetType])
				)return true;
			}
		}
		return false;
	}

	/**
	 * @param string $sControllerName
	 * @throws \Exception
	 * @return boolean
	 */
	public function controllerHasAssetConfiguration($sControllerName){
		if(!is_string($sControllerName) || empty($sControllerName))throw new \Exception('Controller name is not valid');
		return isset($this->configuration['assets'][$sControllerName]);
	}

	/**
	 * @param string $sControllerName
	 * @throws \Exception
	 * @return boolean
	 */
	public function actionHasAssetConfiguration($sActionName){
		if(!is_string($sActionName) || empty($sActionName))throw new \Exception('Action name is not valid');
		$aUnwantedKeys = array(self::ASSET_CSS => true, self::ASSET_LESS => true, self::ASSET_JS => true, self::ASSET_MEDIA => true);
		foreach(array_diff_key($this->configuration['assets'], $aUnwantedKeys) as $sControllerName => $aConfig){
			if(isset($this->configuration['assets'][$sControllerName][$sActionName]))return true;
		}
		return false;
	}

	/**
	 * Retrieve cache file name for given controller name and action name
	 * @param string $sControllerName : (optionnal)
	 * @param string $sActionName : (optionnal)
	 * @return string
	 */
	public function getCacheFileName($sControllerName = null, $sActionName = null){
		$sControllerName = $sControllerName?:$this->getControllerName();
		$sActionName = $sActionName?:$this->getActionName();
		return md5(
			($this->controllerHasAssetConfiguration($sControllerName)?$sControllerName:self::NO_CONTROLLER).
			($this->actionHasAssetConfiguration($sActionName)?$sActionName:self::NO_ACTION)
		);
	}


	/**
	 * Retrieve configuration file name for given controller name and action name
	 * @param string $sControllerName : (optionnal)
	 * @param string $sActionName : (optionnal)
	 * @return string
	 */
	public function getConfigurationFilePath($sControllerName = null, $sActionName = null){
		return $this->getCachePath().$this->getCacheFileName($sControllerName,$sActionName).'.conf';
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
			if(!($sRealAssetsPath =  $this->getRealPath($sAssetsPath)))throw new \InvalidArgumentException('Asset\'s file "'.$sAssetsPath.'" does not exist');
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
			elseif($oFile->isDir() && !$oFile->isDot() && $this->configuration['recursiveSearch'])$aAssets = array_merge(
				$aAssets,
				$this->getAssetsFromDirectory($oFile->getPathname(), $sTypeAsset)
			);
		}
		return $aAssets;
	}

	/**
	 * Attempts to retrieve contents from asset file
	 * @param string $sAssetPath
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @return boolean|Ambigous <boolean, string>
	 */
	public function assetGetContents($sAssetPath){
		if(!is_readable($sAssetPath))throw new \InvalidArgumentException('Asset path "'.$sAssetPath.'" is not readable');
		elseif(strtolower(pathinfo($sAssetPath,PATHINFO_EXTENSION)) === 'php'){
			ob_start();
			if(false === include $sAssetPath)throw new \RuntimeException('Error appends while including asset file "'.$sAssetPath.'"');
			$sAssetContents = ob_get_clean();
		}
		elseif(($sAssetContents = file_get_contents($sAssetPath)) === false)throw new \RuntimeException('Unable to retrieve asset contents from file "'.$sAssetPath.'"');
		return $sAssetContents;


	}

	/**
	 * Render assets
	 * @throws \RuntimeException
	 * @return \AssetsBundle\Service\Service
	 */
	public function renderAssets(){
		//Retrieve cache file name
		$sCacheName = $this->getCacheFileName();

		//Production : check if cache files exist
		$sJsCacheFile = $sCacheName.'.'.self::ASSET_JS;
		$sCssCacheFile = $sCacheName.'.'.self::ASSET_CSS;
		if(
			$this->isProduction()
			&& $this->getRealPath($this->getCachePath().$sCssCacheFile)
			&& $this->getRealPath($this->getCachePath().$sJsCacheFile)
		)return $this->displayAssets(array(
			self::ASSET_CSS => $sCssCacheFile,
			self::ASSET_JS => $sJsCacheFile,
		));


		$aAssetsToRender = $aAssetsConfiguration = $this->getAssetsConfiguration();

		//Manage images caching
		$this->cacheMedias($aAssetsToRender[self::ASSET_MEDIA]);

		//Manage less files caching
		$aAssetsToRender[self::ASSET_CSS][] = $this->cacheLess($aAssetsToRender[self::ASSET_LESS],$sCacheName);

		//Manage css & js file caching
		$this->displayAssets(array_unique(array_filter(array_merge(
			$this->cacheAssets(array_filter($aAssetsToRender[self::ASSET_CSS]),self::ASSET_CSS,$sCacheName),
			$this->cacheAssets($aAssetsToRender[self::ASSET_JS],self::ASSET_JS,$sCacheName)
		))));

		//Write assets configuration into configuration file
		if(!file_put_contents($sConfigFilePath = $this->getConfigurationFilePath(),json_encode($aAssetsConfiguration)))throw new \RuntimeException('Unable to write in file "'.$sConfigFilePath.'"');

		return $this;
	}

	/**
	 * Optimise and cache "Css" & "Js" assets
	 * @param array $aAssetsPath : files to cache
	 * @param string $sTypeAsset : asset's type to cache (self::ASSET_CSS or self::ASSET_JS)
	 * @param string $sCacheName : cache file name
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @throws \RuntimeException
	 * @return string
	 */
	private function cacheAssets(array $aAssetsPath,$sTypeAsset,$sCacheName){
		if(!is_array($aAssetsPath))throw new \InvalidArgumentException('AssetsPath expects an array, "'.gettype($aAssetsPath).'" given');
		if(!self::assetTypeExists($sTypeAsset))throw new \InvalidArgumentException('Asset\'s type "'.$sTypeAsset.'" is undefined');
		if(!is_string($sCacheName))throw new \InvalidArgumentException('CacheName expects string, "'.gettype($aAssetsPath).'" given');
		if(empty($sCacheName))throw new \InvalidArgumentException('CacheName is empty');

		$aReturn = array();

		//No assets to cache
		if(empty($aAssetsPath))return $aReturn;

		//Production cache file
		$sCacheFile = $sCacheName.'.'.$sTypeAsset;
		$aCacheAssets = array();

		//Allows service store existing assets
		$aAssetsExists = array();

		$bHasContent = false;
		foreach($aAssetsPath as $sAssetsPath){
			//Absolute path
			if(!in_array($sAssetsPath,$aAssetsExists) && !($sAssetsPath = $this->getRealPath($sAssetsPath)))throw new \LogicException('File "'.$sAssetsPath.'" does not exist');

			//Developpement : don't optimize assets
			if(!$this->isProduction()){
				//If asset is already a cache file
				if(strpos($sAssetsPath,$this->getCachePath()) !== false)$sAssetRelativePath = str_ireplace(
					array($this->getCachePath(),'.less'),
					array('','.css'),
					$sAssetsPath
				);
				else $sAssetRelativePath = $this->hasAssetsPath()?str_ireplace(
					array($this->getAssetsPath(),getcwd(),DIRECTORY_SEPARATOR),
					array('','','_'),
					$sAssetsPath
				):str_ireplace(
					array(getcwd(),DIRECTORY_SEPARATOR),
					array('','_'),
					$sAssetsPath
				);

				//Rewrite urls for CSS files
				if($sTypeAsset === self::ASSET_CSS && !preg_match('/\.less$/', $sAssetsPath)){
					$sAssetContent = $this->assetGetContents($sAssetsPath);
					$aRewriteUrlCallback = array($this,'rewriteUrl');
					if(!file_put_contents($this->getCachePath().$sAssetRelativePath,preg_replace_callback(
						'/url\(([^\)]+)\)/',
						function($aMatches) use($aRewriteUrlCallback,$sAssetsPath){
							return call_user_func($aRewriteUrlCallback,$aMatches,$sAssetsPath);
						},
						$sAssetContent
					)))throw new \RuntimeException('Unable to write in file : '.$this->getCachePath().$sAssetRelativePath);

				}
				else $this->copyIntoCache($sAssetsPath, $this->getCachePath().$sAssetRelativePath);

				$aCacheAssets[] = $sAssetRelativePath;
				continue;
			}

			//Production : optimize assets
			$sAssetContent = $this->assetGetContents($sAssetsPath);

			switch($sTypeAsset){
				case self::ASSET_CSS:
					//Reset time limit
					set_time_limit(30);

					//Rewrite urls for CSS files
					if(!preg_match('/\.less$/', $sAssetsPath)){
						$aRewriteUrlCallback = array($this,'rewriteUrl');
						$sAssetContent = preg_replace_callback(
							'/url\(([^\)]+)\)/',
							function($aMatches) use($aRewriteUrlCallback,$sAssetsPath){
								return call_user_func($aRewriteUrlCallback,$aMatches,$sAssetsPath);
							},
							$sAssetContent
						);
					}
					$sCacheContent = trim($this->hasFilter(self::ASSET_CSS)?$this->getFilter(self::ASSET_CSS)->run($sAssetContent):$sAssetContent);


					break;
				case self::ASSET_JS:
					//Reset time limit
					set_time_limit(30);
					$sCacheContent = trim($this->hasFilter(self::ASSET_JS)?$this->getFilter(self::ASSET_JS)->run($sAssetContent):$sAssetContent).PHP_EOL.'//'.PHP_EOL;
					break;
			}
			$sCacheContent = trim($sCacheContent);
			if(empty($sCacheContent))continue;
			else $bHasContent = true;
			if(!file_put_contents($this->getCachePath().$sCacheFile,$sCacheContent.PHP_EOL,FILE_APPEND))throw new \RuntimeException('Unable to write in file : '.$this->getCachePath().$sCacheFile);
		}
		return $this->isProduction()?($bHasContent?array($sCacheFile):array()):$aCacheAssets;
	}

	/**
	 * Optimise and cache "Less" assets
	 * @param array $aAssetsPath : assets to cache
	 * @param string $sCacheName : cache file name
	 * @throws \LogicException
	 * @return string|null
	 */
	private function cacheLess(array $aAssetsPath, $sCacheName){
		//Create global import file for Less assets
		$sCacheFile = $sCacheName.'.'.self::ASSET_LESS;
		if(!$this->isProduction())$sCacheFile = 'dev_'.$sCacheFile;

		//Allows service to store existing assets
		$aAssetsExists = array();

		//Check if cache file has to been updated
		if(
			is_readable($this->getConfigurationFilePath())
			&& !$this->assetsConfigurationHasChanged(array(self::ASSET_LESS))
			&& file_exists($this->getCachePath().$sCacheFile)
			&& ($iLastModifiedCache = filemtime($this->getCachePath().$sCacheFile)) !== false
		){
			$bCacheOk = true;
			foreach($aAssetsPath as $sAssetsPath){
				if(!($sAssetsPath = $this->getRealPath($sAssetsPath)))throw new \LogicException('File "'.$sAssetsPath.'" does not exist');
				$aAssetsExists[] = $sAssetsPath;
				if(($iLastModified = filemtime($sAssetsPath)) === false || $iLastModified > $iLastModifiedCache){
					$bCacheOk = false;
					break;
				}
				//If file is up to date, check if it doesn't contain @imports
				else{
					$sAssetContent = $this->assetGetContents($sAssetsPath);

					if(preg_match_all('/@import([^;]*);/', $sAssetContent, $aImports,PREG_PATTERN_ORDER)){
						$sAssetDirPath = realpath(pathinfo($sAssetsPath,PATHINFO_DIRNAME)).DIRECTORY_SEPARATOR;
						foreach($aImports[1] as $sImport){
							$sImport = trim(str_ireplace(array('"','\'','url','(',')'),'',$sImport));
							//Check if file to be imported exists
							if(
								!($sImportPath = $this->getRealPath($sImport))
								&& !file_exists($sImportPath = $sAssetDirPath.$sImport) //Relative path to less file directory
							)throw new \LogicException('File "'.$sImportPath.'" referenced in "'.$sAssetsPath.' does not exists');
							if(($iLastModified = filemtime($sImportPath)) === false || $iLastModified > $iLastModifiedCache){
								$bCacheOk = false;
								break;
							}
						}
						if(!$bCacheOk)break;
					}
				}
			}
			if($bCacheOk)return $this->getCachePath().$sCacheFile;
		}

		$sImportContent = '';
		foreach($aAssetsPath as $sAssetsPath){
			//Absolute path
			if(!in_array($sAssetsPath,$aAssetsExists) && !($sAssetsPath = $this->getRealPath($sAssetsPath)))throw new \LogicException('File "'.$sAssetsPath.'" does not exist');
			$sImportContent .= '@import "'.str_ireplace(getcwd(), '', $sAssetsPath).'";'.PHP_EOL;
		};
		$sImportContent = trim($sImportContent);

		//Reset time limit
		set_time_limit(30);

		//If content is empty, stop rendering process
		if(
			empty($sImportContent)
			|| ($this->hasFilter(self::ASSET_LESS) && !($sImportContent = $this->getFilter(self::ASSET_LESS)->run($sImportContent)))
		)return null;

		//Rewrite urls
		$sImportContent = preg_replace_callback(
			'/url\(([^\)]+)\)/',
			array($this,'rewriteUrl'),
			$sImportContent
		);

		if(!file_put_contents($sCacheFile = $this->getCachePath().$sCacheFile,$sImportContent))throw new \LogicException('Unable to write in file "'.$sCacheFile.'"');
		return $sCacheFile;
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
			$sCacheMediaPath = $this->hasAssetsPath()
				?str_ireplace($this->getAssetsPath(),$this->getCachePath(),$sMediaPath)
				:$sMediaPath;

			//If media is not in asset directory
			if($sCacheMediaPath === $sMediaPath)$sCacheMediaPath = str_ireplace(getcwd(),$this->getCachePath(),$sMediaPath);

			//Media isn't cached or it's deprecated
			if($this->hasToCache($sMediaPath,$sCacheMediaPath)){
				$sExtension = strtolower(pathinfo($sMediaPath,PATHINFO_EXTENSION));
				if(!in_array($sExtension,$this->configuration['mediaExt']))throw new \Exception('Extension is not valid ('.join(', ',$this->configuration['mediaExt']).') : '.$sExtension);
				$this->copyIntoCache($sMediaPath,$sCacheMediaPath);

				//If filter is defined for extension
				if($this->hasFilter($sExtension))$this->getFilter($sExtension)->run($sCacheMediaPath);
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
				file_exists($sAbsolutePath = $this->getCachePath().DIRECTORY_SEPARATOR.$sAssetsPath)?filemtime($sAbsolutePath):time()
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
	private static function assetTypeExists($sAssetType){
		if(!is_string($sAssetType))throw new \InvalidArgumentException('Asset type expects string, "'.gettype($sAssetType).'" given');
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
	 * Check if a file is already cached and if it is up to date
	 * @param string $sFilePath
	 * @param string $sCachePath
	 * @throws \InvalidArgumentException
	 * @return boolean
	 */
	private function hasToCache($sFilePath,$sCachePath){
		if(!file_exists($sFilePath))throw new \InvalidArgumentException('File "'.$sFilePath.'" does not exist');
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
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @return \AssetsBundle\Service\Service
	 */
	private function copyIntoCache($sFilePath,$sCachePath){
		if(!file_exists($sFilePath))\InvalidArgumentException('File "'.$sFilePath.'" does not exist');
		if(!$this->hasToCache($sFilePath,$sCachePath))return $this;
		//Create directory structure if it doesn't exist in cache
		if(!is_dir($sDirPath = pathinfo($sCachePath,PATHINFO_DIRNAME))){
			$sCurrentPath = $this->getCachePath();
			//Directory traversal
			foreach(explode(DIRECTORY_SEPARATOR,str_ireplace($sCurrentPath,'',$sDirPath)) as $sDirPathPart){
				//Create current directory if it doesn't exist
				if(!is_dir($sCurrentPath = $sCurrentPath.DIRECTORY_SEPARATOR.$sDirPathPart)
				&& !mkdir($sCurrentPath))throw new \RuntimeException('Unable to create directory : '.$sCurrentPath);
			}
		}
		if(!copy($sFilePath,$sCachePath) || !file_exists($sCachePath))throw new \RuntimeException('Unable to create file : '.$sCachePath);
		return $this;
	}

	/**
	 * Try to retrieve realpath for a given path (manage @zfRootPath & @zfAssetsPath)
	 * @param string $sPath
	 * @throws \InvalidArgumentException
	 * @return string|boolean : real path or false if not found
	 */
	public function getRealPath($sPath){
		if(empty($sPath) || !is_string($sPath))throw new \InvalidArgumentException('Path is not valid : '.gettype($sPath));

		//If path is "/", assets path is prefered
		if($sPath === '/' && $this->hasAssetsPath())return $this->getAssetsPath();

		if(file_exists($sPath))return realpath($sPath);

		if(strpos($sPath,'@zfRootPath') !== false)$sPath = str_ireplace('@zfRootPath',getcwd(),$sPath);
		if(strpos($sPath,'@zfAssetsPath') !== false)$sPath = str_ireplace('@zfAssetsPath',$this->getAssetsPath(),$sPath);
		if(($sRealPath = realpath($sPath)) !== false)return $sRealPath;
		//Try to guess real path with root path or asset path (if defined)
		if(file_exists($sRealPath = getcwd().DIRECTORY_SEPARATOR.$sPath))return realpath($sRealPath);
		elseif($this->hasAssetsPath() && file_exists($sRealPath = $this->getAssetsPath().$sPath))return realpath($sRealPath);
		else return false;
	}

	/**
	 * Rewrite url to match with cache path if needed
	 * @param array $aMatches
	 * @param string $sAssetPath
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @return string
	 */
	public function rewriteUrl(array $aMatches,$sAssetPath = null){
		if(!isset($aMatches[1]))throw new \InvalidArgumentException('Url match is not valid');

		//Remove quotes & double quotes from url
		$sUrl = trim(str_ireplace(array('"','\''),'', $aMatches[1]));

		//Url is absolute or an external links
		if(preg_match('/^\/|http/', $sUrl))return $aMatches[0];

		//Split arguments
		if(strpos($sUrl,'?') !== false)list($sUrl, $sArguments) = explode('?', $sUrl);

		if(strpos($sUrl,'@zfAssetsPath') !== false){
			$sUrlRealPath = str_ireplace('@zfAssetsPath',$this->getAssetsPath(),$sUrl);
			if(!file_exists($sUrlRealPath))throw new \LogicException('File not found : '.$sUrlRealPath);
			$sUrlRealPath = realpath($sUrlRealPath);
		}
		elseif(!is_null($sAssetPath)){
			if(!is_string($sAssetPath))throw new \InvalidArgumentException('Asset path is not valid : '.gettype($sAssetPath));
			if(!file_exists($sAssetPath))throw new \InvalidArgumentException('File not found : '.$sAssetPath);

			if(($sUrlRealPath = realpath(dirname($sAssetPath).DIRECTORY_SEPARATOR.$sUrl)) === false)$sUrlRealPath = $sUrl;
		}
		else{
			if(($sUrlRealPath = realpath(getcwd().DIRECTORY_SEPARATOR.$sUrl)) === false)throw new \LogicException($sUrl.' is not a valid path');
			if($this->hasAssetsPath())$sUrlRealPath = str_ireplace($this->getAssetsPath(),'', $sUrlRealPath);
		}

		return str_ireplace(
			$sUrl,
			$this->configuration['cacheUrl'].str_ireplace(DIRECTORY_SEPARATOR, '/',ltrim(str_ireplace(
				$this->hasAssetsPath()?array($this->getAssetsPath(),getcwd()):getcwd(),
				'',
				$sUrlRealPath
			),DIRECTORY_SEPARATOR)).(empty($sArguments)?'':'?'.$sArguments),
			$aMatches[0]
		);
	}
}