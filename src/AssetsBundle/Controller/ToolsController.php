<?php
namespace AssetsBundle\Controller;
class ToolsController extends \Zend\Mvc\Controller\AbstractActionController{
    public function renderAssetsAction(){
    	//Retrieve configuration
    	$aConfiguration = $this->getServiceLocator()->get('Config');
    	if(!isset($aConfiguration['asset_bundle'])){
    		$oView = new \Zend\View\Model\ConsoleModel();
    		$oView->setErrorLevel(1);
    		return $oView->setResult('AssetsBundle configuration is undefined'.PHP_EOL);
    	}
    	$aConfiguration = $aConfiguration['asset_bundle'];

    	$oServiceLocator = $this->getServiceLocator();
        $oConsole = $this->getServiceLocator()->get('console');

        //Initialize AssetsBundle service
        $oAssetsBundleService = $oServiceLocator->get('AssetsBundleService')
        ->setRenderer(new \Zend\View\Renderer\PhpRenderer());

        //Empty cache directory
        $this->emptycacheAction();

       	$oConsole->writeLine('');
       	$oConsole->writeLine('Start rendering assets : ', \Zend\Console\ColorInterface::GREEN);
        $oConsole->writeLine('-------------------------', \Zend\Console\ColorInterface::GRAY);
        $oConsole->writeLine('');
        $aUnwantedKeys = array(
        	\AssetsBundle\Service\Service::ASSET_CSS => true,
        	\AssetsBundle\Service\Service::ASSET_LESS => true,
        	\AssetsBundle\Service\Service::ASSET_JS => true,
        	\AssetsBundle\Service\Service::ASSET_MEDIA => true
        );

        //Render all assets
        foreach(array_diff_key($aConfiguration['assets'], $aUnwantedKeys) as $sControllerName => $aConfig){
        	$oConsole->write(' * ',\Zend\Console\ColorInterface::GRAY);
        	$oConsole->write($sControllerName.' : ',\Zend\Console\ColorInterface::LIGHT_BLUE);
        	$oConsole->write(\AssetsBundle\Service\Service::NO_ACTION.PHP_EOL,\Zend\Console\ColorInterface::LIGHT_WHITE);

        	//Render assets for no_actions
       		$oAssetsBundleService->setControllerName($sControllerName)
       		->setActionName(\AssetsBundle\Service\Service::NO_ACTION)
       		->renderAssets();

       		foreach(array_diff_key($aConfiguration['assets'][$sControllerName], $aUnwantedKeys) as $sActionName => $aActionConfiguration){
       			$oConsole->write(' * ',\Zend\Console\ColorInterface::GRAY);
       			$oConsole->write($sControllerName.' : ',\Zend\Console\ColorInterface::LIGHT_BLUE);
       			$oConsole->write($sActionName.PHP_EOL,\Zend\Console\ColorInterface::LIGHT_WHITE);

       			$oAssetsBundleService->setActionName($sActionName)->renderAssets();
       		}
       	}

       	$oConsole->write(' * ',\Zend\Console\ColorInterface::GRAY);
       	$oConsole->write(\AssetsBundle\Service\Service::NO_CONTROLLER.' : ',\Zend\Console\ColorInterface::LIGHT_BLUE);
       	$oConsole->write(\AssetsBundle\Service\Service::NO_ACTION.PHP_EOL,\Zend\Console\ColorInterface::LIGHT_WHITE);

        //Render assets for no_controller
        $oAssetsBundleService
        ->setControllerName(\AssetsBundle\Service\Service::NO_CONTROLLER)
        ->setActionName(\AssetsBundle\Service\Service::NO_ACTION)
        ->renderAssets();

        $oConsole->writeLine('');
        $oConsole->writeLine('---------------', \Zend\Console\ColorInterface::GRAY);
        $oConsole->writeLine('Assets rendered', \Zend\Console\ColorInterface::GREEN);
        $oConsole->writeLine('');
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
		$oConsole = $this->getServiceLocator()->get('console')->writeLine('Cache directory is empty', \Zend\Console\ColorInterface::GREEN);
    }
}