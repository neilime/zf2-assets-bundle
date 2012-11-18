<?php
namespace Neilime\AssetsBundle\Service\Filter;
class LessFilter implements \Neilime\AssetsBundle\Service\Filter\FilterInterface{
	/**
	 * @var \lessc
	 */
	protected $lessParser;
	
	/**
	 * Constructor
	 * @param array $aConfiguration
	 * @throws \Exception
	 */
	public function __construct(array $aConfiguration){
		//Check configuration entries
		if(!isset($aConfiguration['assetPath']))throw new \Exception('Error in configuration');
		if(!is_dir($aConfiguration['assetPath'] = $this->getRealPath($aConfiguration['assetPath'])))throw new \Exception('assetPath is not a valid directory : '.$aConfiguration['assetPath']);
		else $aConfiguration['assetPath'] .= DIRECTORY_SEPARATOR;
		$this->lessParser = new \lessc();
		$this->lessParser->addImportDir($aConfiguration['assetPath']);
		$this->lessParser->addImportDir(getcwd());
		
	}
	
	/**
	 * @param string $sContent
	 * @see \Neilime\AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \Exception
	 * @return string
	 */
	public function run($sContent){
		if(!is_string($sContent))throw new \Exception('Content is not a string : '.gettype($sContent));
		return $this->lessParser->compile($sContent);
	}
	
	/**
	 * Try to retrieve realpath for a given path (manage @zfRootPath)
	 * @param string $sPath
	 * @throws \Exception
	 * @return string|boolean : real path or false if not found
	 */
	private function getRealPath($sPath){
		if(empty($sPath) || !is_string($sPath))throw new \Exception('Path is not valid : '.gettype($sPath));
		if(file_exists($sPath))return realpath($sPath);
	
		if(strpos($sPath,'@zfRootPath') !== false)$sPath = str_ireplace('@zfRootPath',getcwd(),$sPath);		
		if(($sRealPath = realpath($sPath)) !== false)return $sRealPath;
		//Try to guess real path with root path or asset path (if defined)
		if(file_exists($sRealPath = getcwd().DIRECTORY_SEPARATOR.$sPath))return realpath($sRealPath);
		else return false;
	}
}
