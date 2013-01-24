<?php
namespace AssetsBundle\Service\Filter;
class JsFilter implements \AssetsBundle\Service\Filter\FilterInterface{
	/**
	 * @param string $sContent
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \Exception
	 * @return string
	 */
	public function run($sContent){
		if(!is_string($sContent))throw new \Exception('Content is not a string : '.gettype($sContent));
		return \JSMin\Minify::minify($sContent);
	}
}
