<?php
namespace Neilime\AssetsBundle\Service\Minifier;
class JsMinifier implements \Neilime\AssetsBundle\Service\Minifier\MinifierInterface{
	public function minify($sContent){
		return \JSMin::minify($sContent);
	}
}
