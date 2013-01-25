<?php
namespace AssetsBundle\Service\Filter;
class CssFilter implements \AssetsBundle\Service\Filter\FilterInterface{

	/**
	 * @param string $sContent
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @throws \Exception
	 * @return string
	 */
	public function run($sContent){
		if(!is_string($sContent))throw new \Exception('Content is not a string : '.gettype($sContent));
		return \CssMin::minify(
			$sContent,
			array(
				'ConvertLevel3AtKeyframes' => array('RemoveSource' => false),
				'ConvertLevel3Properties' => true
			),
			array(
		       	'ConvertFontWeight' => true,
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