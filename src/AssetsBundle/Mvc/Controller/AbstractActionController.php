<?php
namespace AssetsBundle\Mvc\Controller;
abstract class AbstractActionController extends \Zend\Mvc\Controller\AbstractActionController{
	public function onDispatch(\Zend\Mvc\MvcEvent $oEvent){
		$oReturn = parent::onDispatch($oEvent);
		if($this->params('action') === 'jscustom')$oEvent->getViewModel()->setVariable('jsCustomFiles', $oReturn);
		elseif(
			!$this->getRequest()->isXmlHttpRequest()
			&& method_exists($this, 'jscustomAction')
		){
			$oAssetsBundleService = $this->getServiceLocator()->get('AssetsBundleService');

			if($oAssetsBundleService->isProduction())$this->layout()->jsCustomUrl = $this->getEvent()->getRouter()->assemble(
				array('controller' => $this->params('controller'), 'js_action' => $this->params('action')),
				array('name' => 'jscustom/definition')
			);
			elseif($aJsFiles = $this->jsCustomAction($this->params('action'))){
				//Check js files
				foreach($aJsFiles as &$sJsFile){
					if($sJsFilePath = $oAssetsBundleService->getRealPath($sJsFile))$sJsFile = $sJsFilePath;
					else throw new \LogicException('File "'.$sJsFile.'" does not exist');
				}
				$this->layout()->jsCustomFiles = array_merge(
					is_array($this->layout()->jsCustomFiles)?$this->layout()->jsCustomFiles:array(),
					$aJsFiles
				);
			}
		}
		$oEvent->setResult($oReturn);
		return $oReturn;
	}
}