<?php
namespace AssetsBundleTest\Controller;
class TestController extends \AssetsBundle\Mvc\Controller\AbstractActionController{
	public function testAction(){
		return $this->getResponse();
	}

	public function filerrorAction(){
		return $this->getResponse();
	}

	public function fileErrorAction(){
		return $this->getResponse();
	}

	public function jscustomAction($sAction = null){
		if(empty($sAction)){
			$sAction = $this->params('js_action');
			if(empty($sAction))throw new \InvalidArgumentException('Action param is empty');
			$bReturnFiles = false;
		}
		else $bReturnFiles = true;

		$aJsFiles = array();
		switch(strtolower($sAction)){
			case 'test':
				$aJsFiles[] = 'js/jscustom.js';
				$aJsFiles[] = 'js/jscustom.php';
				break;
			case 'fileerror':
				$aJsFiles[] = 'js/error.js';
				break;
		}
		return $aJsFiles;
	}
}