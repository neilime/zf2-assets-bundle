<?php
namespace Neilime\AssetsBundle\Service\Minifier;
class CssMinifier implements \Neilime\AssetsBundle\Service\Minifier\MinifierInterface{
	public function minify($sContent){
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
