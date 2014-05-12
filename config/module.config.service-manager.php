<?php

//Service manager module config
return array(
    'factories' => array(
        'LesscAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\LesscAssetFileFilterFactory',
        'LessPhpAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\LessPhpAssetFileFilterFactory',
        'CssAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\CssAssetFileFilterFactory',
        'JsAssetFileFilter' => '\AssetsBundle\Factory\AssetFileFilter\JsAssetFileFilterFactory',
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
