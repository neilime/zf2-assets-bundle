## Table of contents

- [\AssetsBundle\AssetFile\AssetFilesConfiguration](#class-assetsbundleassetfileassetfilesconfiguration)
- [\AssetsBundle\AssetFile\AssetFilesCacheManager](#class-assetsbundleassetfileassetfilescachemanager)
- [\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)
- [\AssetsBundle\AssetFile\AssetFilesManager](#class-assetsbundleassetfileassetfilesmanager)
- [\AssetsBundle\AssetFile\AssetFileFiltersManager](#class-assetsbundleassetfileassetfilefiltersmanager)
- [\AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter (abstract)](#class-assetsbundleassetfileassetfilefilterabstractassetfilefilter-abstract)
- [\AssetsBundle\AssetFile\AssetFileFilter\AbstractMinifierAssetFileFilter (abstract)](#class-assetsbundleassetfileassetfilefilterabstractminifierassetfilefilter-abstract)
- [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface (interface)](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface)
- [\AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter\AbstractImageAssetFileFilter (abstract)](#class-assetsbundleassetfileassetfilefilterimageassetfilefilterabstractimageassetfilefilter-abstract)
- [\AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\AbstractJsAssetFileFilter (abstract)](#class-assetsbundleassetfileassetfilefilterjsassetfilefilterabstractjsassetfilefilter-abstract)
- [\AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JsMinAssetFileFilter](#class-assetsbundleassetfileassetfilefilterjsassetfilefilterjsminassetfilefilter)
- [\AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JShrinkAssetFileFilter](#class-assetsbundleassetfileassetfilefilterjsassetfilefilterjshrinkassetfilefilter)
- [\AssetsBundle\AssetFile\AssetFileFilter\StyleAssetFileFilter\LessphpAssetFileFilter](#class-assetsbundleassetfileassetfilefilterstyleassetfilefilterlessphpassetfilefilter)
- [\AssetsBundle\AssetFile\AssetFileFilter\StyleAssetFileFilter\LesscAssetFileFilter](#class-assetsbundleassetfileassetfilefilterstyleassetfilefilterlesscassetfilefilter)
- [\AssetsBundle\AssetFile\AssetFileFilter\StyleAssetFileFilter\CssAssetFileFilter](#class-assetsbundleassetfileassetfilefilterstyleassetfilefiltercssassetfilefilter)
- [\AssetsBundle\Controller\ToolsController](#class-assetsbundlecontrollertoolscontroller)
- [\AssetsBundle\Factory\ToolsControllerFactory](#class-assetsbundlefactorytoolscontrollerfactory)
- [\AssetsBundle\Factory\ServiceOptionsFactory](#class-assetsbundlefactoryserviceoptionsfactory)
- [\AssetsBundle\Factory\ToolsServiceFactory](#class-assetsbundlefactorytoolsservicefactory)
- [\AssetsBundle\Factory\ServiceFactory](#class-assetsbundlefactoryservicefactory)
- [\AssetsBundle\Factory\JsCustomStrategyFactory](#class-assetsbundlefactoryjscustomstrategyfactory)
- [\AssetsBundle\Factory\AssetFileFilter\GifAssetFileFilterFactory](#class-assetsbundlefactoryassetfilefiltergifassetfilefilterfactory)
- [\AssetsBundle\Factory\AssetFileFilter\PngAssetFileFilterFactory](#class-assetsbundlefactoryassetfilefilterpngassetfilefilterfactory)
- [\AssetsBundle\Factory\AssetFileFilter\JsMinAssetFileFilterFactory](#class-assetsbundlefactoryassetfilefilterjsminassetfilefilterfactory)
- [\AssetsBundle\Factory\AssetFileFilter\LessphpAssetFileFilterFactory](#class-assetsbundlefactoryassetfilefilterlessphpassetfilefilterfactory)
- [\AssetsBundle\Factory\AssetFileFilter\LesscAssetFileFilterFactory](#class-assetsbundlefactoryassetfilefilterlesscassetfilefilterfactory)
- [\AssetsBundle\Factory\AssetFileFilter\JShrinkAssetFileFilterFactory](#class-assetsbundlefactoryassetfilefilterjshrinkassetfilefilterfactory)
- [\AssetsBundle\Factory\AssetFileFilter\CssAssetFileFilterFactory](#class-assetsbundlefactoryassetfilefiltercssassetfilefilterfactory)
- [\AssetsBundle\Factory\AssetFileFilter\JpegAssetFileFilterFactory](#class-assetsbundlefactoryassetfilefilterjpegassetfilefilterfactory)
- [\AssetsBundle\Mvc\Controller\AbstractActionController (abstract)](#class-assetsbundlemvccontrollerabstractactioncontroller-abstract)
- [\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)
- [\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)
- [\AssetsBundle\Service\ToolsService](#class-assetsbundleservicetoolsservice)
- [\AssetsBundle\View\Renderer\JsCustomRenderer](#class-assetsbundleviewrendererjscustomrenderer)
- [\AssetsBundle\View\Strategy\JsCustomStrategy](#class-assetsbundleviewstrategyjscustomstrategy)

<hr />

### Class: \AssetsBundle\AssetFile\AssetFilesConfiguration

| Visibility | Function |
|:-----------|:---------|
| public | <strong>addAssetFile(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesManager](#class-assetsbundleassetfileassetfilesmanager)</em> |
| public | <strong>addAssetFileFromOptions(</strong><em>array</em> <strong>$aAssetFileOptions</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesConfiguration](#class-assetsbundleassetfileassetfilesconfiguration)</em> |
| public | <strong>assetsConfigurationHasChanged(</strong><em>array</em> <strong>$aAssetsType=null</strong>)</strong> : <em>boolean</em><br /><em>Check if assets configuration is the same as last saved configuration</em> |
| public | <strong>getAssetFileFromFilePath(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>, <em>\AssetsBundle\AssetFile\type</em> <strong>$sAssetRealPath</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesConfiguration](#class-assetsbundleassetfileassetfilesconfiguration)</em> |
| public | <strong>getAssetFiles(</strong><em>string</em> <strong>$sAssetFileType=null</strong>)</strong> : <em>array</em> |
| public | <strong>getAssetRelativePath(</strong><em>string</em> <strong>$sAssetPath</strong>)</strong> : <em>string</em><br /><em>Retrieve asset relative path</em> |
| public | <strong>getConfigurationFilePath()</strong> : <em>string</em><br /><em>Retrieve configuration file name for the current request</em> |
| public | <strong>getConfigurationKey()</strong> : <em>string</em> |
| public | <strong>getOptions()</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>saveAssetFilesConfiguration()</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesConfiguration](#class-assetsbundleassetfileassetfilesconfiguration)</em><br /><em>Save current asset configuration into conf file</em> |
| public | <strong>setOptions(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesConfiguration](#class-assetsbundleassetfileassetfilesconfiguration)</em> |
| protected | <strong>getAssetFilesPathFromDirectory(</strong><em>string</em> <strong>$sDirPath</strong>, <em>string</em> <strong>$sAssetType</strong>)</strong> : <em>array</em><br /><em>Retrieve assets from a directory</em> |

<hr />

### Class: \AssetsBundle\AssetFile\AssetFilesCacheManager

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions=null</strong>)</strong> : <em>void</em><br /><em>Constructor</em> |
| public | <strong>cacheAssetFile(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>, <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oSourceAssetFile=null</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> |
| public | <strong>getAssetFileCachePath(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em> |
| public | <strong>getOptions()</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>getProductionCachedAssetFiles(</strong><em>string</em> <strong>$sAssetFileType</strong>)</strong> : <em>array</em> |
| public | <strong>hasProductionCachedAssetFiles(</strong><em>string</em> <strong>$sAssetFileType</strong>)</strong> : <em>boolean</em> |
| public | <strong>isAssetFileCached(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>boolean</em> |
| public | <strong>sanitizeAssetFilePath(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em> |
| public | <strong>setOptions(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesManager](#class-assetsbundleassetfileassetfilesmanager)</em> |

<hr />

### Class: \AssetsBundle\AssetFile\AssetFile

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>assetFileTypeExists(</strong><em>string</em> <strong>$sAssetFileType</strong>)</strong> : <em>boolean</em><br /><em>Check if asset file's type is valid</em> |
| public | <strong>getAssetFileContents()</strong> : <em>string</em> |
| public static | <strong>getAssetFileDefaultExtension(</strong><em>mixed</em> <strong>$sAssetFileType</strong>)</strong> : <em>string</em> |
| public | <strong>getAssetFileExtension()</strong> : <em>string</em> |
| public | <strong>getAssetFileLastModified()</strong> : <em>int/null</em><br /><em>Retrieve asset file last modified timestamp</em> |
| public | <strong>getAssetFilePath()</strong> : <em>string</em> |
| public | <strong>getAssetFileSize()</strong> : <em>integer/null</em><br /><em>Retrieve asset file size</em> |
| public | <strong>getAssetFileType()</strong> : <em>string</em> |
| public | <strong>isAssetFilePathUrl()</strong> : <em>boolean</em> |
| public | <strong>setAssetFileContents(</strong><em>string</em> <strong>$sAssetFileContents</strong>, <em>bool/boolean</em> <strong>$bFileAppend=true</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> |
| public | <strong>setAssetFilePath(</strong><em>string</em> <strong>$sAssetFilePath</strong>)</strong> : <em>\AssetsBundle\Service\AssetFile</em> |
| public | <strong>setAssetFileType(</strong><em>string</em> <strong>$sAssetFileType</strong>)</strong> : <em>\AssetsBundle\Service\AssetFile</em> |

*This class extends \Zend\Stdlib\AbstractOptions*

*This class implements \Zend\Stdlib\ParameterObjectInterface*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFilesManager

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions=null</strong>)</strong> : <em>void</em><br /><em>Constructor</em> |
| public | <strong>__destruct()</strong> : <em>void</em><br /><em>On destruction, delete all existing tmp asset files</em> |
| public | <strong>getAssetFileFiltersManager()</strong> : <em>[\AssetsBundle\AssetFile\AssetFileFiltersManager](#class-assetsbundleassetfileassetfilefiltersmanager)</em><br /><em>Retrieve the asset file filters manager. Lazy loads an instance if none currently set.</em> |
| public | <strong>getAssetFilesCacheManager()</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesCacheManager](#class-assetsbundleassetfileassetfilescachemanager)</em><br /><em>Retrieve the asset files cache manager. Lazy loads an instance if none currently set.</em> |
| public | <strong>getAssetFilesConfiguration()</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesConfiguration](#class-assetsbundleassetfileassetfilesconfiguration)</em><br /><em>Retrieve the asset files configuration. Lazy loads an instance if none currently set.</em> |
| public | <strong>getCachedAssetsFiles(</strong><em>string</em> <strong>$sAssetFileType</strong>)</strong> : <em>array</em> |
| public | <strong>getOptions()</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>rewriteAssetFileUrls(</strong><em>\string</em> <strong>$sAssetFileContent</strong>, <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em><br /><em>Rewrite url of an asset file content to match with cache path if needed</em> |
| public | <strong>rewriteUrl(</strong><em>array</em> <strong>$aMatches</strong>, <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em><br /><em>Rewrite url to match with cache path if needed</em> |
| public | <strong>setAssetFileFiltersManager(</strong><em>[\AssetsBundle\AssetFile\AssetFileFiltersManager](#class-assetsbundleassetfileassetfilefiltersmanager)</em> <strong>$oAssetFileFiltersManager</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesManager](#class-assetsbundleassetfileassetfilesmanager)</em><br /><em>Set the asset file filters manager</em> |
| public | <strong>setAssetFilesCacheManager(</strong><em>[\AssetsBundle\AssetFile\AssetFilesCacheManager](#class-assetsbundleassetfileassetfilescachemanager)</em> <strong>$oAssetFilesCacheManager</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesManager](#class-assetsbundleassetfileassetfilesmanager)</em><br /><em>Set the asset files cache manager</em> |
| public | <strong>setAssetFilesConfiguration(</strong><em>[\AssetsBundle\AssetFile\AssetFilesConfiguration](#class-assetsbundleassetfileassetfilesconfiguration)</em> <strong>$oAssetFilesConfiguration</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesManager](#class-assetsbundleassetfileassetfilesmanager)</em><br /><em>Set the asset files configuration</em> |
| public | <strong>setOptions(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesManager](#class-assetsbundleassetfileassetfilesmanager)</em> |
| protected | <strong>cacheCssAssetFiles()</strong> : <em>array</em><br /><em>Cache Css asset files and retrieve cached asset files</em> |
| protected | <strong>cacheJsAssetFiles()</strong> : <em>array</em><br /><em>Cache Js asset files and retrieve cached asset files</em> |
| protected | <strong>cacheLessAssetFiles()</strong> : <em>array</em><br /><em>Cache Less asset files and retrieve cached asset files</em> |
| protected | <strong>cacheMediaAssetFiles()</strong> : <em>array</em><br /><em>Cache media asset files and retrieve cached asset files</em> |
| protected | <strong>createTmpAssetFile(</strong><em>string</em> <strong>$sAssetFileType</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> |

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFiltersManager

| Visibility | Function |
|:-----------|:---------|
| public | <strong>getOptions()</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setOptions(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFileFiltersManager](#class-assetsbundleassetfileassetfilefiltersmanager)</em> |
| public | <strong>setService(</strong><em>string</em> <strong>$sName</strong>, <em>mixed</em> <strong>$oAssetFileFilter</strong>, <em>bool/boolean</em> <strong>$bShared=true</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFileFiltersManager](#class-assetsbundleassetfileassetfilefiltersmanager)</em> |
| public | <strong>validatePlugin(</strong><em>mixed</em> <strong>$oAssetFileFilter</strong>)</strong> : <em>void</em><br /><em>Validate the plugin. Checks that the filter loaded is an instance of \AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface</em> |

*This class extends \Zend\ServiceManager\AbstractPluginManager*

*This class implements \Zend\ServiceManager\ServiceLocatorAwareInterface, \Zend\ServiceManager\ServiceLocatorInterface, \Psr\Container\ContainerInterface, \Interop\Container\ContainerInterface*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter (abstract)

| Visibility | Function |
|:-----------|:---------|
| public | <strong>cacheFilteredAssetFileContent(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>, <em>string</em> <strong>$sFilteredContent</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter](#class-assetsbundleassetfileassetfilefilterabstractassetfilefilter-abstract)</em> |
| public | <strong>getAssetFileFilterName()</strong> : <em>string</em> |
| public | <strong>getAssetFileFilterProcessedDirPath()</strong> : <em>string</em> |
| public | <strong>getCachedFilteredContent(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>boolean/string</em> |
| public | <strong>getCachedFilteredContentFilePath(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em> |
| public | <strong>getOptions()</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setAssetFileFilterName(</strong><em>string</em> <strong>$sAssetFileFilterName</strong>)</strong> : <em>\AssetsBundle\Service\Filter\AbstractFilter</em> |
| public | <strong>setOptions(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions</strong>)</strong> : <em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em> |

*This class extends \Zend\Stdlib\AbstractOptions*

*This class implements \Zend\Stdlib\ParameterObjectInterface, [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface)*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFilter\AbstractMinifierAssetFileFilter (abstract)

| Visibility | Function |
|:-----------|:---------|
| public | <strong>filterAssetFile(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em> |
| protected | <strong>abstract minifyContent(</strong><em>mixed</em> <strong>$sContent</strong>)</strong> : <em>string</em> |

*This class extends [\AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter](#class-assetsbundleassetfileassetfilefilterabstractassetfilefilter-abstract)*

*This class implements [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface), \Zend\Stdlib\ParameterObjectInterface*

<hr />

### Interface: \AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface

| Visibility | Function |
|:-----------|:---------|
| public | <strong>filterAssetFile(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em> |
| public | <strong>getAssetFileFilterName()</strong> : <em>string</em> |
| public | <strong>getAssetFileFilterProcessedDirPath()</strong> : <em>string</em> |
| public | <strong>getOptions()</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setOptions(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface)</em> |

*This class implements \Zend\Stdlib\ParameterObjectInterface*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFilter\ImageAssetFileFilter\AbstractImageAssetFileFilter (abstract)

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>array</em> <strong>$oOptions=null</strong>)</strong> : <em>void</em><br /><em>Constructor</em> |
| public | <strong>filterAssetFile(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em> |
| protected | <strong>assetFileShouldBeOptimize(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>boolean</em> |
| protected | <strong>abstract optimizeImage(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)Filter\ImageAssetFileFilter\resource</em> <strong>$oImage</strong>)</strong> : <em>string</em> |

*This class extends [\AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter](#class-assetsbundleassetfileassetfilefilterabstractassetfilefilter-abstract)*

*This class implements [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface), \Zend\Stdlib\ParameterObjectInterface*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\AbstractJsAssetFileFilter (abstract)

| Visibility | Function |
|:-----------|:---------|

*This class extends [\AssetsBundle\AssetFile\AssetFileFilter\AbstractMinifierAssetFileFilter](#class-assetsbundleassetfileassetfilefilterabstractminifierassetfilefilter-abstract)*

*This class implements \Zend\Stdlib\ParameterObjectInterface, [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface)*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JsMinAssetFileFilter

| Visibility | Function |
|:-----------|:---------|
| protected | <strong>minifyContent(</strong><em>mixed</em> <strong>$sContent</strong>)</strong> : <em>string</em> |

*This class extends [\AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\AbstractJsAssetFileFilter](#class-assetsbundleassetfileassetfilefilterjsassetfilefilterabstractjsassetfilefilter-abstract)*

*This class implements [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface), \Zend\Stdlib\ParameterObjectInterface*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JShrinkAssetFileFilter

| Visibility | Function |
|:-----------|:---------|
| protected | <strong>minifyContent(</strong><em>mixed</em> <strong>$sContent</strong>)</strong> : <em>string</em> |

*This class extends [\AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\AbstractJsAssetFileFilter](#class-assetsbundleassetfileassetfilefilterjsassetfilefilterabstractjsassetfilefilter-abstract)*

*This class implements [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface), \Zend\Stdlib\ParameterObjectInterface*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFilter\StyleAssetFileFilter\LessphpAssetFileFilter

| Visibility | Function |
|:-----------|:---------|
| public | <strong>filterAssetFile(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em> |
| public | <strong>getLessParser()</strong> : <em>\Less_Parser</em> |
| public | <strong>setLessParser(</strong><em>\Less_Parser</em> <strong>$oParser</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)Filter\LessphpAssetFileFilter</em> |

*This class extends [\AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter](#class-assetsbundleassetfileassetfilefilterabstractassetfilefilter-abstract)*

*This class implements [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface), \Zend\Stdlib\ParameterObjectInterface*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFilter\StyleAssetFileFilter\LesscAssetFileFilter

| Visibility | Function |
|:-----------|:---------|
| public | <strong>filterAssetFile(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>)</strong> : <em>string</em> |

*This class extends [\AssetsBundle\AssetFile\AssetFileFilter\AbstractAssetFileFilter](#class-assetsbundleassetfileassetfilefilterabstractassetfilefilter-abstract)*

*This class implements [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface), \Zend\Stdlib\ParameterObjectInterface*

<hr />

### Class: \AssetsBundle\AssetFile\AssetFileFilter\StyleAssetFileFilter\CssAssetFileFilter

| Visibility | Function |
|:-----------|:---------|
| protected | <strong>getCSSmin()</strong> : <em>\tubalmartin\CssMin\Minifier</em> |
| protected | <strong>minifyContent(</strong><em>mixed</em> <strong>$sContent</strong>)</strong> : <em>string</em> |

*This class extends [\AssetsBundle\AssetFile\AssetFileFilter\AbstractMinifierAssetFileFilter](#class-assetsbundleassetfileassetfilefilterabstractminifierassetfilefilter-abstract)*

*This class implements \Zend\Stdlib\ParameterObjectInterface, [\AssetsBundle\AssetFile\AssetFileFilter\AssetFileFilterInterface](#interface-assetsbundleassetfileassetfilefilterassetfilefilterinterface)*

<hr />

### Class: \AssetsBundle\Controller\ToolsController

| Visibility | Function |
|:-----------|:---------|
| public | <strong>emptyCacheAction()</strong> : <em>void</em><br /><em>Process empty cache action</em> |
| public | <strong>getAssetsBundleToolsService()</strong> : <em>[\AssetsBundle\Service\ToolsService](#class-assetsbundleservicetoolsservice)</em> |
| public | <strong>renderAssetsAction()</strong> : <em>void</em><br /><em>Process render all assets action</em> |
| public | <strong>setAssetsBundleToolsService(</strong><em>[\AssetsBundle\Service\ToolsService](#class-assetsbundleservicetoolsservice)</em> <strong>$oAssetsBundleToolsService</strong>)</strong> : <em>[\AssetsBundle\Controller\ToolsController](#class-assetsbundlecontrollertoolscontroller)</em> |

*This class extends \Zend\Mvc\Controller\AbstractActionController*

*This class implements \Zend\Stdlib\DispatchableInterface, \Zend\EventManager\EventManagerAwareInterface, \Zend\EventManager\EventsCapableInterface, \Zend\Mvc\InjectApplicationEventInterface*

<hr />

### Class: \AssetsBundle\Factory\ToolsControllerFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\Controller\ToolsController](#class-assetsbundlecontrollertoolscontroller)</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\ServiceOptionsFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\ToolsServiceFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\Service\ToolsService](#class-assetsbundleservicetoolsservice)</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\ServiceFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\JsCustomStrategyFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\View\Strategy\JsCustomStrategy](#class-assetsbundleviewstrategyjscustomstrategy)</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\AssetFileFilter\GifAssetFileFilterFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)Filter\ImageAssetFileFilter\GifImageAssetFileFilter</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\AssetFileFilter\PngAssetFileFilterFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)Filter\ImageAssetFileFilter\PngImageAssetFileFilter</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\AssetFileFilter\JsMinAssetFileFilterFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JsMinAssetFileFilter](#class-assetsbundleassetfileassetfilefilterjsassetfilefilterjsminassetfilefilter)</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\AssetFileFilter\LessphpAssetFileFilterFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)Filter\LessPhpAssetFileFilter</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\AssetFileFilter\LesscAssetFileFilterFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)Filter\LesscAssetFileFilter</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\AssetFileFilter\JShrinkAssetFileFilterFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFileFilter\JsAssetFileFilter\JShrinkAssetFileFilter](#class-assetsbundleassetfileassetfilefilterjsassetfilefilterjshrinkassetfilefilter)</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\AssetFileFilter\CssAssetFileFilterFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)Filter\CssAssetFileFilter</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Factory\AssetFileFilter\JpegAssetFileFilterFactory

| Visibility | Function |
|:-----------|:---------|
| public | <strong>createService(</strong><em>\Zend\ServiceManager\ServiceLocatorInterface</em> <strong>$oServiceLocator</strong>)</strong> : <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)Filter\ImageAssetFileFilter\JpegImageAssetFileFilter</em> |

*This class implements \Zend\ServiceManager\FactoryInterface*

<hr />

### Class: \AssetsBundle\Mvc\Controller\AbstractActionController (abstract)

| Visibility | Function |
|:-----------|:---------|
| public | <strong>onDispatch(</strong><em>\Zend\Mvc\MvcEvent</em> <strong>$oEvent</strong>)</strong> : <em>mixed</em> |

*This class extends \Zend\Mvc\Controller\AbstractActionController*

*This class implements \Zend\Stdlib\DispatchableInterface, \Zend\EventManager\EventManagerAwareInterface, \Zend\EventManager\EventsCapableInterface, \Zend\Mvc\InjectApplicationEventInterface*

<hr />

### Class: \AssetsBundle\Service\Service

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions=null</strong>)</strong> : <em>void</em><br /><em>Constructor</em> |
| public | <strong>attach(</strong><em>\Zend\EventManager\EventManagerInterface</em> <strong>$oEventManager</strong>)</strong> : <em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em> |
| public | <strong>consoleError(</strong><em>\Zend\Mvc\MvcEvent</em> <strong>$oEvent</strong>)</strong> : <em>void</em><br /><em>Display errors to the console, if an error appends during a ToolsController action</em> |
| public | <strong>detach(</strong><em>\Zend\EventManager\EventManagerInterface</em> <strong>$oEventManager</strong>)</strong> : <em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em> |
| public | <strong>getAssetFilesManager()</strong> : <em>[\AssetsBundle\AssetFile\AssetFilesManager](#class-assetsbundleassetfileassetfilesmanager)</em> |
| public | <strong>getOptions()</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>renderAssets(</strong><em>\Zend\Mvc\MvcEvent</em> <strong>$oEvent</strong>)</strong> : <em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em><br /><em>Render assets</em> |
| public | <strong>setAssetFilesManager(</strong><em>[\AssetsBundle\AssetFile\AssetFilesManager](#class-assetsbundleassetfileassetfilesmanager)</em> <strong>$oAssetFilesManager</strong>)</strong> : <em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em> |
| public | <strong>setOptions(</strong><em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> <strong>$oOptions</strong>)</strong> : <em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em> |
| protected | <strong>displayAssets(</strong><em>array</em> <strong>$aAssetFiles</strong>)</strong> : <em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em><br /><em>Display assets through renderer</em> |

*This class implements \Zend\EventManager\ListenerAggregateInterface*

<hr />

### Class: \AssetsBundle\Service\ServiceOptions

| Visibility | Function |
|:-----------|:---------|
| public | <strong>allowsRecursiveSearch()</strong> : <em>boolean</em> |
| public | <strong>getActionName()</strong> : <em>string</em> |
| public | <strong>getAssetFileBaseUrl(</strong><em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile</strong>, <em>\AssetsBundle\Service\scalar</em> <strong>$iLastModifiedTime=null</strong>)</strong> : <em>string</em> |
| public | <strong>getAssets()</strong> : <em>array</em> |
| public | <strong>getAssetsPath()</strong> : <em>string</em> |
| public | <strong>getBaseUrl()</strong> : <em>string</em> |
| public | <strong>getCacheFileName()</strong> : <em>string</em><br /><em>Retrieve cache file name for given module name, controller name and action name</em> |
| public | <strong>getCachePath()</strong> : <em>string</em> |
| public | <strong>getCacheUrl()</strong> : <em>string</em> |
| public | <strong>getControllerName()</strong> : <em>string</em> |
| public | <strong>getDirectoriesPermissions()</strong> : <em>integer</em> |
| public | <strong>getFilesPermissions()</strong> : <em>integer</em> |
| public | <strong>getLastModifiedTime()</strong> : <em>\AssetsBundle\Service\scalable/null</em> |
| public | <strong>getMediaExt()</strong> : <em>array</em> |
| public | <strong>getModuleName()</strong> : <em>string</em> |
| public | <strong>getProcessedDirPath()</strong> : <em>string</em> |
| public | <strong>getRealPath(</strong><em>string</em> <strong>$sPathToResolve</strong>, <em>[\AssetsBundle\AssetFile\AssetFile](#class-assetsbundleassetfileassetfile)</em> <strong>$oAssetFile=null</strong>)</strong> : <em>string/boolean</em><br /><em>Try to retrieve realpath for a given path (manage @zfRootPath)</em> |
| public | <strong>getRenderer()</strong> : <em>\Zend\View\Renderer\RendererInterface</em> |
| public | <strong>getTmpDirPath()</strong> : <em>string</em> |
| public | <strong>getViewHelperPluginForAssetFileType(</strong><em>string</em> <strong>$sAssetFileType</strong>)</strong> : <em>\Zend\View\Helper\HelperInterface</em> |
| public | <strong>getViewHelperPlugins()</strong> : <em>array</em> |
| public | <strong>hasAssetsPath()</strong> : <em>boolean</em> |
| public | <strong>isAssetsBundleDisabled()</strong> : <em>boolean</em> |
| public | <strong>isProduction()</strong> : <em>boolean</em> |
| public | <strong>setActionName(</strong><em>string</em> <strong>$sActionName</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setAssets(</strong><em>array</em> <strong>$aAssets</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setAssetsPath(</strong><em>string/null</em> <strong>$sAssetsPath=null</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setBaseUrl(</strong><em>string</em> <strong>$sBaseUrl</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setCachePath(</strong><em>string</em> <strong>$sCachePath</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setCacheUrl(</strong><em>string</em> <strong>$sCacheUrl</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setControllerName(</strong><em>string</em> <strong>$sControllerName</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setDirectoriesPermissions(</strong><em>integer</em> <strong>$iDirectoriesPermissions</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setDisabledContexts(</strong><em>array</em> <strong>$aDisabledContexts</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setFilesPermissions(</strong><em>integer</em> <strong>$iFilesPermissions</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setLastModifiedTime(</strong><em>\AssetsBundle\Service\scalable/null</em> <strong>$sLastModifiedTime=null</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setMediaExt(</strong><em>array</em> <strong>$aMediaExt</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setModuleName(</strong><em>string</em> <strong>$sModuleName</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setProcessedDirPath(</strong><em>string</em> <strong>$sProcessedDirPath</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setProduction(</strong><em>boolean</em> <strong>$bProduction</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setRecursiveSearch(</strong><em>boolean</em> <strong>$bRecursiveSearch</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setRenderer(</strong><em>\Zend\View\Renderer\RendererInterface</em> <strong>$oRenderer</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setTmpDirPath(</strong><em>string</em> <strong>$sTmpDirPath</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| public | <strong>setViewHelperPlugins(</strong><em>array</em> <strong>$aViewHelperPlugins</strong>)</strong> : <em>[\AssetsBundle\Service\ServiceOptions](#class-assetsbundleserviceserviceoptions)</em> |
| protected | <strong>safeFileExists(</strong><em>string</em> <strong>$sFilePath</strong>)</strong> : <em>boolean</em><br /><em>Check if file exists, only search in "open_basedir" path if defined</em> |

*This class extends \Zend\Stdlib\AbstractOptions*

*This class implements \Zend\Stdlib\ParameterObjectInterface*

<hr />

### Class: \AssetsBundle\Service\ToolsService

| Visibility | Function |
|:-----------|:---------|
| public | <strong>emptyCache(</strong><em>bool/boolean</em> <strong>$bDisplayConsoleMessage=true</strong>)</strong> : <em>[\AssetsBundle\Service\ToolsService](#class-assetsbundleservicetoolsservice)</em> |
| public | <strong>getAssetsBundleService()</strong> : <em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em> |
| public | <strong>getConsole()</strong> : <em>\Zend\Console\Adapter\AdapterInterface</em> |
| public | <strong>getMvcEvent()</strong> : <em>\Zend\Mvc\MvcEvent</em> |
| public | <strong>renderAllAssets()</strong> : <em>[\AssetsBundle\Service\ToolsService](#class-assetsbundleservicetoolsservice)</em> |
| public | <strong>setAssetsBundleService(</strong><em>[\AssetsBundle\Service\Service](#class-assetsbundleserviceservice)</em> <strong>$oAssetsBundleService</strong>)</strong> : <em>[\AssetsBundle\Service\ToolsService](#class-assetsbundleservicetoolsservice)</em> |
| public | <strong>setConsole(</strong><em>\Zend\Console\Adapter\AdapterInterface</em> <strong>$oConsole</strong>)</strong> : <em>[\AssetsBundle\Service\ToolsService](#class-assetsbundleservicetoolsservice)</em> |
| public | <strong>setMvcEvent(</strong><em>\Zend\Mvc\MvcEvent</em> <strong>$oMvcEvent</strong>)</strong> : <em>[\AssetsBundle\Service\ToolsService](#class-assetsbundleservicetoolsservice)</em> |

<hr />

### Class: \AssetsBundle\View\Renderer\JsCustomRenderer

| Visibility | Function |
|:-----------|:---------|
| public | <strong>getEngine()</strong> : <em>\AssetsBundle\View\Renderer\JsRenderer</em> |
| public | <strong>render(</strong><em>\AssetsBundle\View\Renderer\ViewModel</em> <strong>$oViewModel</strong>, <em>null/array/\ArrayAccess</em> <strong>$aValues=null</strong>)</strong> : <em>string</em><br /><em>Renders js files contents</em> |
| public | <strong>setResolver(</strong><em>\Zend\View\Resolver\ResolverInterface</em> <strong>$oResolver</strong>)</strong> : <em>\AssetsBundle\View\Renderer\JsRenderer</em><br /><em>Set the resolver used to map a template name to a resource the renderer may consume.</em> |

*This class implements \Zend\View\Renderer\RendererInterface*

<hr />

### Class: \AssetsBundle\View\Strategy\JsCustomStrategy

| Visibility | Function |
|:-----------|:---------|
| public | <strong>attach(</strong><em>\Zend\EventManager\EventManagerInterface</em> <strong>$oEvents</strong>, <em>int</em> <strong>$iPriority=1</strong>)</strong> : <em>void</em><br /><em>Attach the aggregate to the specified event manager</em> |
| public | <strong>detach(</strong><em>\Zend\EventManager\EventManagerInterface</em> <strong>$oEvents</strong>)</strong> : <em>void</em><br /><em>Detach aggregate listeners from the specified event manager</em> |
| public | <strong>getRenderer()</strong> : <em>[\AssetsBundle\View\Renderer\JsCustomRenderer](#class-assetsbundleviewrendererjscustomrenderer)</em> |
| public | <strong>getRouter()</strong> : <em>mixed</em> |
| public | <strong>injectResponse(</strong><em>\Zend\View\ViewEvent</em> <strong>$oEvent</strong>)</strong> : <em>void</em> |
| public | <strong>selectRenderer(</strong><em>\Zend\View\ViewEvent</em> <strong>$oEvent</strong>)</strong> : <em>void/\AssetsBundle\View\Renderer\JsRenderer</em><br /><em>Check if JsCustomStrategy has to be used (MVC action = \AssetsBundle\Mvc\Controller\AbstractActionController::JS_CUSTOM_ACTION)</em> |
| public | <strong>setRenderer(</strong><em>[\AssetsBundle\View\Renderer\JsCustomRenderer](#class-assetsbundleviewrendererjscustomrenderer)</em> <strong>$oRenderer</strong>)</strong> : <em>[\AssetsBundle\View\Strategy\JsCustomStrategy](#class-assetsbundleviewstrategyjscustomstrategy)</em> |
| public | <strong>setRouter(</strong><em>\Zend\Mvc\Router\RouteInterface</em> <strong>$oRouter</strong>)</strong> : <em>void</em> |

*This class implements \Zend\EventManager\ListenerAggregateInterface*

