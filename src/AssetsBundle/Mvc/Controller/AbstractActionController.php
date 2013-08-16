<?php
namespace AssetsBundle\Mvc\Controller;
abstract class AbstractActionController extends \Zend\Mvc\Controller\AbstractActionController{
	public function onDispatch(\Zend\Mvc\MvcEvent $oEvent){
		$oReturn = parent::onDispatch($oEvent);
		if($this->params('action') === 'jscustom'){
			if(!is_array($oReturn))throw new \LogicException('jscustomAction return expects an array, "'.gettype($oReturn).'" given');
			$oEvent->getViewModel()->setVariable('jsCustomFiles', $oReturn);
		}
		elseif(
			!$this->getRequest()->isXmlHttpRequest()
			&& method_exists($this, 'jscustomAction')
		){
			$oAssetsBundleService = $this->getServiceLocator()->get('AssetsBundleService');

			if($oAssetsBundleService->getOptions()->isProduction())$this->layout()->jsCustomUrl = $this->getEvent()->getRouter()->assemble(
				array('controller' => $this->params('controller'), 'js_action' => $this->params('action')),
				array('name' => 'jscustom/definition')
			);
			else{
				if($aJsFiles = $this->jsCustomAction($this->params('action'))){
					if(!is_array($aJsFiles))throw new \LogicException('Js files expects an array, "'.gettype($aJsFiles).'" given');
					//Check js files
					$sCachePath = $oAssetsBundleService->getOptions()->getCachePath();
					$sCacheUrl = $oAssetsBundleService->getOptions()->getCacheUrl();
					foreach($aJsFiles as &$sJsFile){
						if($sJsFilePath = $oAssetsBundleService->getOptions()->getRealPath($sJsFile)){
							//Retrieve js file relative path
							$sJsFileRelativePath = $oAssetsBundleService->getAssetRelativePath($sJsFilePath);

							//Copy js file into cache
							$oAssetsBundleService->copyIntoCache($sJsFilePath, $sCachePath.$sJsFileRelativePath);

							//Define last modified
							$iLastModified = file_exists($sAbsolutePath = $sCachePath.DIRECTORY_SEPARATOR.$sJsFileRelativePath)?filemtime($sAbsolutePath):time();

							//Define js file relative url
							$sJsFile = $sCacheUrl.$sJsFileRelativePath.(strpos($sJsFileRelativePath, '?')?'&':'?').($iLastModified?:time());
						}
						else throw new \LogicException('File "'.$sJsFile.'" does not exist');
					}
				}
				else $aJsFiles = array();

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