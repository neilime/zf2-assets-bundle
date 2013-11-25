<?php
namespace AssetsBundle\View\Strategy;
class ViewHelperStrategy extends \AssetsBundle\View\Strategy\AbstractStrategy{

	/**
     * Render asset file
	 * @param string $sPath
	 * @param string $sLastModified
	 * @param string $sAssetType
	 * @throws \InvalidArgumentException
	 * @throws \DomainException
	 * @return \AssetsBundle\View\Strategy\ViewHelperStrategy
	 */
	public function renderAsset($sPath,$sLastModified,$sAssetType){
		if(!is_string($sPath))throw new \InvalidArgumentException('Path expects a string, "'.gettype($sPath).'" given');
		if(!is_scalar($sLastModified))throw new \InvalidArgumentException('Last modified expects a scalar value, "'.gettype($sLastModified).'" given');
		if(!is_string($sAssetType))throw new \InvalidArgumentException('Asset\'s type expects a string, "'.gettype($sAssetType).'" given');

        // Is an absolute path?
        if(!preg_match('/^https?:\/\//', $sPath))$sPath = $this->getBaseUrl().$sPath.(strpos($sPath, '?')?'&':'?').($sLastModified?:time());

        switch($sAssetType){
            case \AssetsBundle\Service\Service::ASSET_JS:
            	$this->getRenderer()->plugin('InlineScript')->appendFile($sPath);
                break;
            case \AssetsBundle\Service\Service::ASSET_CSS:
                $this->getRenderer()->plugin('HeadLink')->appendStylesheet($sPath,'all');
                break;
            default:
            	throw new \DomainException('Asset\'s type "'.gettype($sAssetType).'" is not supported by '.__CLASS__);
        }

        return $this;
    }
}