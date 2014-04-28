<?php

namespace AssetsBundle\Controller;

class ToolsController extends \Zend\Mvc\Controller\AbstractActionController {

    public function renderAssetsAction() {
        //Retrieve configuration
        $aConfiguration = $this->getServiceLocator()->get('Config');
        if (!isset($aConfiguration['assets_bundle'])) {
            $oView = new \Zend\View\Model\ConsoleModel();
            $oView->setErrorLevel(1);
            return $oView->setResult('AssetsBundle configuration is undefined' . PHP_EOL);
        }

        $oServiceLocator = $this->getServiceLocator();
        $oConsole = $this->getServiceLocator()->get('console');

        //Initialize AssetsBundle service
        $oAssetsBundleService = $oServiceLocator->get('AssetsBundleService');
        $oAssetsBundleService->getOptions()->setRenderer(new \Zend\View\Renderer\PhpRenderer());

        //Start process
        $oConsole->writeLine('');
        $oConsole->writeLine('======================================================================', \Zend\Console\ColorInterface::GRAY);
        $oConsole->writeLine('Render all assets for ' . ($oAssetsBundleService->getOptions()->isProduction() ? 'production' : 'development'), \Zend\Console\ColorInterface::GREEN);
        $oConsole->writeLine('======================================================================', \Zend\Console\ColorInterface::GRAY);
        $oConsole->writeLine('');

        //Empty cache directory
        $this->emptycacheAction();

        $oConsole->writeLine('');
        $oConsole->writeLine('Start rendering assets : ', \Zend\Console\ColorInterface::GREEN);
        $oConsole->writeLine('-------------------------', \Zend\Console\ColorInterface::GRAY);
        $oConsole->writeLine('');
        $aUnwantedKeys = array(
            \AssetsBundle\AssetFile\AssetFile::ASSET_CSS => true,
            \AssetsBundle\AssetFile\AssetFile::ASSET_LESS => true,
            \AssetsBundle\AssetFile\AssetFile::ASSET_JS => true,
            \AssetsBundle\AssetFile\AssetFile::ASSET_MEDIA => true
        );

        //Retrieve MvcEvent
        $oMvcEvent = clone $this->getServiceLocator()->get('Application')->getMvcEvent();
        /* @var $oMvcEvent \Zend\Mvc\MvcEvent */

        //Reset route match and request
        $oMvcEvent->setRouteMatch(new \Zend\Mvc\Router\RouteMatch(array()))->setRequest(new \Zend\Http\Request());

        //Retrieve AssetsBundle service options
        $oOptions = $oAssetsBundleService->getOptions();

        //Render all assets
        foreach (array_diff_key($oOptions->getAssets(), $aUnwantedKeys) as $sModuleName => $aModuleConfig) {
            //Render module assets
            $oOptions->setModuleName($sModuleName);

            //If module has global assets
            if (array_intersect_key($aModuleConfig, $aUnwantedKeys)) {
                $oConsole->write(' * ', \Zend\Console\ColorInterface::GRAY);
                $oConsole->write('[' . $sModuleName . ']', \Zend\Console\ColorInterface::LIGHT_CYAN);
                $oConsole->write('[No controller]', \Zend\Console\ColorInterface::LIGHT_BLUE);
                $oConsole->write('[No action]' . PHP_EOL, \Zend\Console\ColorInterface::LIGHT_WHITE);

                //Render assets for no_controller and no_action
                $oOptions->setControllerName(\AssetsBundle\Service\ServiceOptions::NO_CONTROLLER)
                        ->setActionName(\AssetsBundle\Service\ServiceOptions::NO_ACTION);
                $oAssetsBundleService->renderAssets($oMvcEvent);
            }

            foreach (array_diff_key($aConfiguration['assets_bundle']['assets'][$sModuleName], $aUnwantedKeys) as $sControllerName => $aControllerConfig) {
                //Render controller assets
                $oOptions->setControllerName($sControllerName);

                //If controller has global assets
                if (array_intersect_key($aControllerConfig, $aUnwantedKeys)) {
                    $oConsole->write(' * ', \Zend\Console\ColorInterface::GRAY);
                    $oConsole->write('[' . $sModuleName . ']', \Zend\Console\ColorInterface::LIGHT_CYAN);
                    $oConsole->write('[' . $sControllerName . ']', \Zend\Console\ColorInterface::LIGHT_BLUE);
                    $oConsole->write('[No action]' . PHP_EOL, \Zend\Console\ColorInterface::LIGHT_WHITE);

                    //Render assets for no_action
                    $oOptions->setActionName(\AssetsBundle\Service\ServiceOptions::NO_ACTION);
                    $oAssetsBundleService->renderAssets($oMvcEvent);
                }

                foreach (array_diff_key($aConfiguration['assets_bundle']['assets'][$sModuleName][$sControllerName], $aUnwantedKeys) as $sActionName => $aActionConfig) {
                    //Render assets for action
                    if (array_intersect_key($aActionConfig, $aUnwantedKeys)) {
                        $oConsole->write(' * ', \Zend\Console\ColorInterface::GRAY);
                        $oConsole->write('[' . $sModuleName . ']', \Zend\Console\ColorInterface::LIGHT_CYAN);
                        $oConsole->write('[' . $sControllerName . ']', \Zend\Console\ColorInterface::LIGHT_BLUE);
                        $oConsole->write('[' . $sActionName . ']' . PHP_EOL, \Zend\Console\ColorInterface::LIGHT_WHITE);

                        $oAssetsBundleService->getOptions()->setActionName($sActionName);
                        $oAssetsBundleService->renderAssets($oMvcEvent);
                    }
                }
            }
        }
        //Render global assets
        $oConsole->write(' * ', \Zend\Console\ColorInterface::GRAY);
        $oConsole->write('[No module]', \Zend\Console\ColorInterface::LIGHT_CYAN);
        $oConsole->write('[No controller]', \Zend\Console\ColorInterface::LIGHT_BLUE);
        $oConsole->write('[No action]' . PHP_EOL, \Zend\Console\ColorInterface::LIGHT_WHITE);
        $oAssetsBundleService->getOptions()
                ->setModuleName(\AssetsBundle\Service\ServiceOptions::NO_MODULE)
                ->setControllerName(\AssetsBundle\Service\ServiceOptions::NO_CONTROLLER)
                ->setActionName(\AssetsBundle\Service\ServiceOptions::NO_ACTION);
        $oAssetsBundleService->renderAssets($oMvcEvent);

        $oConsole->writeLine('');
        $oConsole->writeLine('---------------', \Zend\Console\ColorInterface::GRAY);
        $oConsole->writeLine('Assets rendered', \Zend\Console\ColorInterface::GREEN);
        $oConsole->writeLine('');
    }

    public function emptycacheAction() {

        $oConsole = $this->getServiceLocator()->get('console');
        $oConsole->writeLine('');
        $oConsole->writeLine('========================', \Zend\Console\ColorInterface::GRAY);
        $oConsole->writeLine('Empty cache', \Zend\Console\ColorInterface::GREEN);
        $oConsole->writeLine('========================', \Zend\Console\ColorInterface::GRAY);
        $oConsole->writeLine('');

        //Initialize AssetsBundle service
        $oAssetsBundleService = $this->getServiceLocator()->get('AssetsBundleService');
        /* @var $oAssetsBundleService \AssetsBundle\Service\Service */

        //Empty cache directory except .gitignore
        foreach (new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($oAssetsBundleService->getOptions()->getCachePath(), \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        ) as $oFileinfo) {
            if ($oFileinfo->isDir()) {
                rmdir($oFileinfo->getRealPath());
            } elseif ($oFileinfo->getBasename() !== '.gitignore') {
                unlink($oFileinfo->getRealPath());
            }
        }
        $oConsole->writeLine(' * Cache directory is empty', \Zend\Console\ColorInterface::GRAY);

        //Retrieve
        $oAssetFileFiltersManager = $oAssetsBundleService->getAssetFilesManager()->getAssetFileFiltersManager();
        $aRegisteredServices = $oAssetFileFiltersManager->getRegisteredServices();
        //Empty asset file filters cache directory except .gitignore
        foreach ($aRegisteredServices['instances'] as $sFilter) {
            $oFilter = $oAssetFileFiltersManager->get($sFilter);
            if (is_dir($sAssetFileFilterProcessedDirPath = $oFilter->getAssetFileFilterProcessedDirPath())) {
                foreach (new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sAssetFileFilterProcessedDirPath, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
                ) as $oFileinfo) {
                    if ($oFileinfo->isDir()) {
                        rmdir($oFileinfo->getRealPath());
                    } elseif ($oFileinfo->getBasename() !== '.gitignore') {
                        unlink($oFileinfo->getRealPath());
                    }
                }
                $oConsole->writeLine(' * "' . $oFilter->getAssetFileFilterName() . '" filter cache directory is empty', \Zend\Console\ColorInterface::GRAY);
            }
        }

        //Empty config directory except .gitignore
        foreach (new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator(dirname($oAssetsBundleService->getAssetFilesManager()->getAssetFilesConfiguration()->getConfigurationFilePath()), \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        ) as $oFileinfo) {
            if ($oFileinfo->isDir()) {
                rmdir($oFileinfo->getRealPath());
            } elseif ($oFileinfo->getBasename() !== '.gitignore') {
                unlink($oFileinfo->getRealPath());
            }
        }
        $oConsole->writeLine(' * Config cache directory is empty', \Zend\Console\ColorInterface::GRAY);
        $oConsole->writeLine('');
    }

}
