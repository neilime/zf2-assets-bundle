<?php
namespace Neilime\AssetsBundle\View\Strategy;
class ViewHelperStrategy extends \Neilime\AssetsBundle\View\AbstractStrategy{
    /**
     * Render asset file
     * @param string $sPath
     */
	public function renderAsset($sPath){
    	$sExtension = strtolower(pathinfo($sPath, PATHINFO_EXTENSION));
    	$sPath = $this->getBaseUrl().$sPath.(strpos($sPath, '?')?'&':'?').__LAST_UPDATE__;
        switch($sExtension){
            case 'js':
            	$this->getRenderer()->plugin('InlineScript')->appendFile($sPath);
                break;
            case 'css':
                $this->getRenderer()->plugin('HeadLink')->appendStylesheet($sPath);
                break;
        }
    }
}