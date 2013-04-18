<?php
namespace AssetsBundle\Service\Filter;
class CssFilter implements \AssetsBundle\Service\Filter\FilterInterface{

	/**
	 * @see \AssetsBundle\Service\Filter\FilterInterface::run()
	 * @param string $sContent
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @return string
	 */
	public function run($sContent){
		if(!is_string($sContent))throw new \InvalidArgumentException('Content is not a string : '.gettype($sContent));
		if(!class_exists('CssMin'))throw new \LogicException('"CssMin" class does not exist');

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