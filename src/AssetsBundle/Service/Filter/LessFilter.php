<?php
namespace AssetsBundle\Service\Filter;
class LessFilter implements \AssetsBundle\Service\Filter\FilterInterface{
	/**
	 * @var \lessc
	 */
	protected $lessParser;

	/**
	 * @var string
	 */
	protected $assetsPath;

	/**
	 * Constructor
	 * @param array $aConfiguration
	 * @throws \Exception
	 */
	public function __construct(array $aConfiguration){
		//Check configuration entries
		if(isset($aConfiguration['assetsPath']))$this->setAssetsPath($aConfiguration['assetsPath']);

		$this->lessParser = new \lessc();
		if($this->hasAssetsPath())$this->lessParser->addImportDir($this->getAssetsPath());

		$this->lessParser->addImportDir(getcwd());
		$this->lessParser->setAllowUrlRewrite(true);
	}

	/**
	 * @param string $sContent
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \Exception
	 * @return string
	 */
	public function run($sContent){
		if(!is_string($sContent))throw new \Exception('Content is not a string : '.gettype($sContent));
		return trim($this->lessParser->compile($sContent));
	}

	/**
	 * @param string $sAssetsPath
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 * @return \AssetsBundle\Service\Filter\LessFilter
	 */
	public function setAssetsPath($sAssetsPath){
		if(!is_string($sAssetsPath))throw new \InvalidArgumentException('Assets path expects string, "'.gettype($sAssetsPath).'" given');
		if(is_dir($sAssetsPath = $this->getRealPath($sAssetsPath)))$sAssetsPath .= DIRECTORY_SEPARATOR;
		else throw new \Exception('Assets path "'.$sAssetsPath.'"is not a valid directory');
		$this->assetsPath = $sAssetsPath;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function hasAssetsPath(){
		return is_string($this->assetsPath);
	}

	/**
	 * @throws \LogicException
	 * @return string
	 */
	public function getAssetsPath(){
		if($this->hasAssetsPath())return $this->assetsPath;
		throw new \LogicException('Assets path is undefined');
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