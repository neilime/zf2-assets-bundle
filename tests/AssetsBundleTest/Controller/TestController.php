<?php
namespace AssetsBundleTest\Controller;
class TestController extends \AssetsBundle\Mvc\Controller\AbstractActionController{
	public function testAction(){
		$oView = new \Zend\View\Model\ViewModel();
		return $oView->setTemplate('test')->setTerminal(true);
	}

	public function filerrorAction(){
		return $this->getResponse();
	}

	public function fileErrorAction(){
		return $this->getResponse();
	}

	public function emptyAction(){
		return $this->getResponse();
	}

	public function jscustomAction($sAction = null){
		if(empty($sAction)){
			$sAction = $this->params('js_action');
			if(empty($sAction))throw new \InvalidArgumentException('Action param is empty');
		}

		$aJsFiles = array();
		switch(strtolower($sAction)){
			case 'test':
				$aJsFiles[] = 'js/jscustom.js';
				$aJsFiles[] = 'js/jscustom.php';
				break;
			case 'fileerror':
				$aJsFiles[] = 'js/error.js';
				break;
			case 'empty':
				break;
		}
		return $aJsFiles;
	}
}