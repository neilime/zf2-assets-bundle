<?php

//Service manager module config
return array(
    'factories' => array(
        'LesscAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\LesscAssetFileFilterFactory',
        'LessphpAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\LessphpAssetFileFilterFactory',
        'CssAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\CssAssetFileFilterFactory',
        'JsMinAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\JsMinAssetFileFilterFactory',
        'JShrinkAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\JShrinkAssetFileFilterFactory',
        'PngAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\PngAssetFileFilterFactory',
        'JpegAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\JpegAssetFileFilterFactory',
        'GifAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\GifAssetFileFilterFactory',
        'AssetsBundleService' => '\AssetsBundle\Factory\ServiceFactory',
        'AssetsBundleServiceOptions' => '\AssetsBundle\Factory\ServiceOptionsFactory',
        'AssetsBundleToolsService' => '\AssetsBundle\Factory\ToolsServiceFactory',
        'JsCustomStrategy' => '\AssetsBundle\Factory\JsCustomStrategyFactory'
    ),
    'invokables' => array(
        'JsCustomRenderer' => '\AssetsBundle\View\Renderer\JsCustomRenderer'
    )
);
