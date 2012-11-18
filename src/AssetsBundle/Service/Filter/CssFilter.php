<?php
namespace Neilime\AssetsBundle\Service\Filter;
class CssFilter implements \Neilime\AssetsBundle\Service\Filter\FilterInterface{
	
	/**
	 * @param string $sContent
	 * @see \Neilime\AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \Exception
	 * @return string
	 */
	public function run($sContent){
		if(!is_string($sContent))throw new \Exception('Content is not a string : '.gettype($sContent));
		return \CssMin::minify(
			$sContent,
			null,
			array(
				'ConvertHslColors' => true,
				'ConvertRgbColors' => true,
				'ConvertNamedColors' => true,
				'CompressColorValues' => true,
				'CompressUnitValues' => true,
				'CompressExpressionValues' => true
			)
		);
	}
}
