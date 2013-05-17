<?php
namespace AssetsBundle\Service\Filter;
class JsFilter implements \AssetsBundle\Service\Filter\FilterInterface{
	const EXEC_TIME_PER_CHAR = 7E-5;

	/**
	 * @param string $sContent
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \Exception
	 * @return string
	 */
	public function run($sContent){
		if(!is_string($sContent))throw new \Exception('Content is not a string : '.gettype($sContent));
		$iExecTime = strlen($sContent)*self::EXEC_TIME_PER_CHAR;
		if($iExecTime > ini_get('max_execution_time'))set_time_limit(0);
		return trim(\JsMin\Minify::minify($sContent));
	}
}