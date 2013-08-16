<?php
namespace AssetsBundle\Service\Filter;
class JsFilter implements \AssetsBundle\Service\Filter\FilterInterface{
	const EXEC_TIME_PER_CHAR = 7E-5;

	/**
	 * @param string $sContent
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \LogicException
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	public function run($sContent){
		if(!class_exists('JSMin'))throw new \LogicException('"JSMin" class does not exist');
		if(!is_string($sContent))throw new \InvalidArgumentException('Content is not a string : '.gettype($sContent));
		$iExecTime = strlen($sContent)*self::EXEC_TIME_PER_CHAR;
		if($iExecTime > ini_get('max_execution_time'))set_time_limit(0);
		return trim(\JSMin::minify($sContent));
	}
}