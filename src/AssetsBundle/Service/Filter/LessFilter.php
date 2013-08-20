<?php
namespace AssetsBundle\Service\Filter;
class LessFilter implements \AssetsBundle\Service\Filter\FilterInterface{
	const EXEC_TIME_PER_CHAR = 0.0005;

	/**
	 * @var string
	 */
	protected $assetsPath;

	/**
	 * Constructor
	 * @param array $aConfiguration
	 * @throws \Exception
	 */
	public function __construct(array $aConfiguration = null){
		//Check configuration entries
		if(isset($aConfiguration['assetsPath']))$this->setAssetsPath($aConfiguration['assetsPath']);
	}

	/**
	 * @param string $sContent
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	public function run($sContent){
		if(!is_string($sContent))throw new \InvalidArgumentException('Content expects string, "'.gettype($sContent).'" given');
		$iExecTime = strlen($sContent)*self::EXEC_TIME_PER_CHAR;
		if($iExecTime > ini_get('max_execution_time'))set_time_limit(0);
		$oLessParser = new \lessc();
		if($this->hasAssetsPath())$oLessParser->addImportDir($this->getAssetsPath());

		$oLessParser->addImportDir(getcwd());
		$oLessParser->setAllowUrlRewrite(true);
		return trim($oLessParser->compile($sContent));
	}

	/**
	 * @param string $sAssetsPath
	 * @throws \InvalidArgumentException
	 * @return \AssetsBundle\Service\Filter\LessFilter
	 */
	public function setAssetsPath($sAssetsPath){
		if(!is_string($sAssetsPath))throw new \InvalidArgumentException('Assets path expects string, "'.gettype($sAssetsPath).'" given');
		if(is_dir($sAssetsRealPath = $this->getRealPath($sAssetsPath))){
			$this->assetsPath = $sAssetsRealPath.DIRECTORY_SEPARATOR;
			return $this;
		}
		throw new \InvalidArgumentException('Assets path "'.$sAssetsPath.'" is not a valid directory');
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