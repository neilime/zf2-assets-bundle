<?php

namespace AssetsBundle\Controller;

class ToolsController extends \Zend\Mvc\Controller\AbstractActionController
{

    /**
     * @var \AssetsBundle\Service\ToolsService
     */
    protected $assetsBundleToolsService;

    /**
     * Process render all assets action
     */
    public function renderAssetsAction()
    {
        $this->getAssetsBundleToolsService()->renderAllAssets();
    }

    /**
     * Process empty cache action
     */
    public function emptyCacheAction()
    {
        $this->getAssetsBundleToolsService()->emptyCache();
    }

    /**
     * @return \AssetsBundle\Service\ToolsService
     * @throws \LogicException
     */
    public function getAssetsBundleToolsService()
    {
        if ($this->assetsBundleToolsService instanceof \AssetsBundle\Service\ToolsService) {
            return $this->assetsBundleToolsService;
        }
        throw new \LogicException('Property "assetsBundleService" expects an instance of "\AssetsBundle\Service\ToolsService", "' . (is_object($this->assetsBundleToolsService) ? get_class($this->assetsBundleToolsService) : gettype($this->assetsBundleToolsService)) . '" defined');
    }

    /**
     * @param \AssetsBundle\Service\ToolsService $oAssetsBundleToolsService
     * @return \AssetsBundle\Controller\ToolsController
     */
    public function setAssetsBundleToolsService(\AssetsBundle\Service\ToolsService $oAssetsBundleToolsService)
    {
        $this->assetsBundleToolsService = $oAssetsBundleToolsService;
        return $this;
    }
}
