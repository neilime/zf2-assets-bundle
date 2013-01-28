<?php
namespace AssetsBundle\Controller;
class ToolsController extends \Zend\Mvc\Controller\AbstractActionController{
    public function renderassetsAction(){
        $oServiceLocator = $this->getServiceLocator();
        try{
            $oModuleManager = $oServiceLocator->get('modulemanager');
        }
        catch(\Zend\ServiceManager\Exception\ServiceNotFoundException $oException){
            return $this->sendError('Cannot get Zend\ModuleManager\ModuleManager instance. Is your application using it?');
        }
        $oConsole = $this->getServiceLocator()->get('console');
        $aModules = array_diff(array_keys($oModuleManager->getLoadedModules(false)), array('AssetsBundle'));
        if(empty($aModules)){
            $oConsole->writeLine('No modules installed. Are you in the root folder of a ZF2 application?');
            return;
        }

        //Initialize AssetsBundle service
        $oAssetsBundleService = $oServiceLocator->get('AssetsBundleService')
        ->setRenderer($oServiceLocator->get('ViewRenderer'))
        ->setLoadedModules($aModules);

        $aModules = $oAssetsBundleService->getLoadedModules();
        if(empty($aModules)){
        	$oConsole->writeLine('No modules have assets configuration.');
        	return;
        }

        //Empty cache directory
        $this->emptycacheAction();

        //Retrieve configuration
        $aConfiguration = $this->getServiceLocator()->get('config');

        $oConsole->writeLine('Start rendering assets : ');
        $aUnwantedKeys = array(self::ASSET_CSS => true, self::ASSET_LESS => true, self::ASSET_JS => true, self::ASSET_MEDIA => true);
        foreach($aModules as $sModuleName){
        	if(!isset($aConfiguration['assets'][$sModuleName]))continue;
        	foreach(array_diff_key($aConfiguration['assets'][$sModuleName], $aUnwantedKeys) as $sControllerName => $aConfig){
        		$oConsole->writeLine($sControllerName.' : '.\AssetsBundle\Service\Service::NO_ACTION, \Zend\Console\ColorInterface::GREEN);

        		//Render assets for no_actions
        		$oAssetsBundleService->setControllerName($sControllerName)
        		->setActionName(\AssetsBundle\Service\Service::NO_ACTION)
        		->renderAssets();

        		foreach(array_diff_key($aConfiguration['assets'][$sModuleName][$sControllerName], $aUnwantedKeys) as $sActionName => $aActionConfiguration){
        			$oConsole->writeLine($sControllerName.' : '.$sActionName, \Zend\Console\ColorInterface::GREEN);
        			$oAssetsBundleService->setActionName()->$sActionNamerenderAssets();
        		}
        	}
        }

        $oConsole->writeLine(\AssetsBundle\Service\Service::NO_CONTROLLER.' : '.\AssetsBundle\Service\Service::NO_ACTION, \Zend\Console\ColorInterface::GREEN);

        //Render assets for no_controller
        $oAssetsBundleService->setControllerName($sControllerName)
        ->setControllerName(\AssetsBundle\Service\Service::NO_CONTROLLER)
        ->setActionName(\AssetsBundle\Service\Service::NO_ACTION)
        ->renderAssets();

        $oConsole->writeLine('Assets rendered');
    }

    public function emptycacheAction(){
    	//Initialize AssetsBundle service
        $oAssetsBundleService = $this->getServiceLocator()->get('AssetsBundleService');

    	//Empty cache directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($oAssetsBundleService->getCachePath(), \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}
    }

    /**
     * @param string $sMessage
     * @return \Zend\View\Model\ConsoleModel
     */
    private function sendError($sMessage){
        $oView = new \Zend\View\Model\ConsoleModel();
        $oView->setErrorLevel(2);
        return $oView->setResult($sMessage.PHP_EOL);
    }

}
