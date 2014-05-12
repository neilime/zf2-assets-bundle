<?php

namespace AssetsBundle\Controller;

class ToolsController extends \Zend\Mvc\Controller\AbstractActionController {

    /**
     * Process render all assets action
     */
    public function renderAssetsAction() {
        //Retrieve configuration
        $aConfiguration = $this->getServiceLocator()->get('Config');
        if (!isset($aConfiguration['assets_bundle'])) {
            $oView = new \Zend\View\Model\ConsoleModel();
            $oView->setErrorLevel(1);
            return $oView->setResult('AssetsBundle configuration is undefined' . PHP_EOL);
        }

        $this->getServiceLocator()->get('AssetsBundleToolsService')->renderAllAssets();
    }

    /**
     * Process empty cache action
     */
    public function emptyCacheAction() {
        $this->getServiceLocator()->get('AssetsBundleToolsService')->emptyCache();
    }

}
