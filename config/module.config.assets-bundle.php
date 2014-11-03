<?php

// Assets Bundle module config
return array(
    'production' => true, // Application environment (Developpement => false)
    'lastModifiedTime' => null, // Arbitrary last modified time in production
    'cachePath' => '@zfRootPath/public/cache', // Cache directory absolute path
    'assetsPath' => '@zfRootPath/public', // Assets directory absolute path (allows you to define relative path for assets config)
    'tmpDirPath' => sys_get_temp_dir(), // Temp directory absolute path
    'processedDirPath' => '@zfRootPath/data/AssetsBundle/processed', // Processed files directory absolute path
    'baseUrl' => null, // Base URL of the application
    'cacheUrl' => '@zfBaseUrl/cache/', // Cache directory base url
    'mediaExt' => array('jpeg', 'jpg', 'png', 'gif', 'cur', 'ttf', 'eot', 'svg', 'woff'), // Put here all media extensions to be cached
    'recursiveSearch' => false, // Allows search for matching assets in required folder and its subfolders
    'filters' => array(
        \AssetsBundle\AssetFile\AssetFile::ASSET_LESS => 'LessphpAssetFileFilter',
        \AssetsBundle\AssetFile\AssetFile::ASSET_CSS => 'CssAssetFileFilter',
        \AssetsBundle\AssetFile\AssetFile::ASSET_JS => 'JsAssetFileFilter',
        'png' => 'PngAssetFileFilter',
        'jpg' => 'JpegAssetFileFilter', 'jpeg' => 'JpegAssetFileFilter',
        'gif' => 'GifAssetFileFilter'
    ),
    'view_helper_plugins' => array(
        \AssetsBundle\AssetFile\AssetFile::ASSET_CSS => 'headlink',
        \AssetsBundle\AssetFile\AssetFile::ASSET_JS => 'headscript',
    )
);
