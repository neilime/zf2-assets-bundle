<?php

namespace AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter;

class JShrinkAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\AbstractJsAssetFileFilter
{
        
    /**
     * @var string $sContent
     * @return string
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    protected function minifyContent($sContent)
    {
        if (!is_string($sContent)) {
            throw new \InvalidArgumentException('Argument "$sContent" expects a string, "'.(is_object($sContent)?get_class($sContent):gettype($sContent)).'" given');
        }
        if (!class_exists('JShrink\Minifier')) {
            throw new \LogicException('"JShrink\Minifier" class does not exist');
        }
        return \JShrink\Minifier::minify($sContent);
    }

}
