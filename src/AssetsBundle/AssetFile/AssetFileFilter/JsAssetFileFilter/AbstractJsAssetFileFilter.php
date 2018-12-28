<?php

namespace AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter;

abstract class AbstractJsAssetFileFilter extends \AssetsBundle\AssetFile\AssetFileFilter\AbstractMinifierAssetFileFilter
{

    /**
     * @var string
     */
    protected $assetFileFilterName = \AssetsBundle\AssetFile\AssetFile::ASSET_JS;
}
