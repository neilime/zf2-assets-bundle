AssetsBundle
=======

[![Build Status](https://travis-ci.org/neilime/zf2-assets-bundle.png?branch=master)](https://travis-ci.org/neilime/zf2-assets-bundle)

Created by Neilime

Introduction
------------

AssetsBundle is module for ZF2 allowing asset managment (bundling & caching) like Css, Js and Less, dependent on modules, controllers and actions (di). 
This module manages the concept of the environment/production development.

In developpement : 
 - Files are not bundling for easier debugging.
 - Less files are compiled when updated or if an "@import" inside is updated
 
In production :
 
 - All files are bundling and cached one time only if needed.

P.S. Sorry for my english. If You wish to help me with this project or correct my english description - You are welcome :)

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)
* [lessphp](https://github.com/leafo/lessphp) (latest master).
* [CssMin](https://github.com/natxet/CssMin) (latest master).
* [JsMin](https://github.com/nick4fake/JsMin) (latest master).

Installation
------------

### Main Setup

#### By cloning project

1. Install the [lessphp fork](https://github.com/neilime/lessphp) (latest master), [CssMin](https://github.com/natxet/CssMin),[JsMin](https://github.com/nick4fake/JsMin) by cloning them into `./vendor/`.
2. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "neilime/zf2-assets-bundle": "dev-master"
    }
    ```

2. Due to bug in lessphp you have to use neilime's Lessphp fork, add this repository in your composer.json:
	
	```json
    "repositories": [{
        "type": "vcs",
        "url": "http://github.com/neilime/lessphp"
    }],
    ```

3. Now tell composer to download AssetsBundle by running the command:

    ```bash
    $ php composer.phar update
    ```

#### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'AssetsBundle',
        ),
        // ...
    );
    ```
    
# How to use _AssetsBundle_

## Simple configuration example

This example shows how to convert "ZF2 Skeleton Application" to manage assets via AssetsBundle.

1. After installing skeleton application, install _AssetsBundle_ as explained above.

2. Then just create cache directory into "public/".
  ```bash
  cd to/your/project/public/dir/
  mkdir cache
  ```
3. Edit the application module configuration file `module/Application/config/module.config.php`, adding the configuration fragment below:
	
	```php
	<?php
	return array(
		//...
		'asset_bundle' => array(
	    	'assets' => array(
	    		'application' => array(
	    			'css' => array('css'),
	    			'js' => array(
	    				'js/jquery.min.js',
	    				'js/bootstrap.min.js'
	    			),
	    			'media' => array('images')
	    		)
	    	)
	    ),
	    //...
	);
	```
4. Edit layout file `module/Application/view/layout/layout.phtml`, removing prepend function for assets:
	```php
	 <?php
	//Remove these lines
	
	->prependStylesheet($this->basePath() . '/css/bootstrap-responsive.min.css')
	->prependStylesheet($this->basePath() . '/css/style.css')
	->prependStylesheet($this->basePath() . '/css/bootstrap.min.css')
	
	 ->prependFile($this->basePath() . '/js/bootstrap.min.js')
     ->prependFile($this->basePath() . '/js/jquery.min.js')
     ```
5. Save & Resfresh.

# Configuration

The default configuration is setup to run with "Application ZF2 Skeleton"

1. _AssetsBundle_ :

 	- boolean production : Define the application environment (development => false). Default true.
    - string cachePath : cache directory absolute path, you can use the "@zfRootPath" constant corresponding to current working directory. Default "@zfRootPath/public/cache".
    - string assetsPath : assets directory absolute path, allows you to define relative path for assets config. You can use the constant "@zfRootPath" corresponding to current working directory. Default "@zfRootPath/public".
    - string cacheUrl : cache directory base url, you can use the constant "@zfBaseUrl" corresponding to application base url . Default "@zfBaseUrl/assets/cache/".
    - array mediaExt : Put here all medias extensions to be cached. Default array('jpg','png','gif','cur','ttf','eot','svg','woff').

2. Modules :

 You can define assets for modules / controllers / action
 
 Exemple for the application module : 
 
 	```php
	<?php
	return array(
		//...
    	'assets' => array(
    		'application' => array( //Module Name
    			'css' => array(), //Define css files to include
    			'js' => array(), //Define js files to include
    			'less' => array(), //Define less files to include
    			'media' => array() //Define images to manage
    			    			
    			'Test/Controller/Name' => array(
    				'css' => array(),
	    			'js' => array(),
	    			'less' => array(), 
	    			'media' => array()
	    			
	    			'ActionName'=> array(
	    				'css' => array(),
		    			'js' => array(),
		    			'less' => array(), 
		    			'media' => array()
    				)
    			)
    			//...
    		)
	    ),
	    //...
	);
	```
	
	For each asset, you can specify files or directories. All these elements are related to the asset path by default, 
	but you can specify an absolute path or use the constants "@zfAssetsPath" and "@zfRootPath".
	If you specify a directory, all files matching the asset type (css, less, js, media) will be included.
	You can define an inclusion order like this :
	
	```php
	<?php
	return array(
		//...
    	'assets' => array(
    		'application' => array(
    			'js' => array('js/firstFile.js','js'),
    			//...
    		)
    		//...
    	)    			
    	//...
    );
    ```
    
   	This example includes the file "firstFile.js" first, and all other javascript files in the folder "js"
   	
3. Custom Js :

This function allows you to dynamically include javascript files. For exemple, files specific to a user settings.
In this case, your controller that need these file have to extends "AssetsBundle\Mvc\ControllerAbstractActionController".

Then create a jscustomAction function into your controller : 
	
	```php
	<?php
	public function jscustomAction($sAction = null){
    	$aConfiguration = $this->getServiceLocator()->get('config');
    	if(!isset($aConfiguration['asset_bundle']))throw new \Exception('AssetBundle config is not defined');
    	if(empty($sAction)){
    		//Test if you are in production
    		if(!$aConfiguration['asset_bundle']['production'])throw new \Exception('action must be defined in development mode');
    		$sAction = $this->params('js_action');
    		if(empty($sAction))throw new \Exception('Action is not defined');
    		$bReturnFiles = false;
    	}
    	else $bReturnFiles = true;
    	$aJsFiles = array();

    	switch($sAction){
    		case 'application':
				//Here you can specify js files to include
				$aJsFiles[] = 'js/dynamicFile.js';
    			break;
    	}
    	if($bReturnFiles)return $aJsFiles;
    	else{
    		$this->layout()->jsFiles = $aJsFiles;
    		return false;
    	}
    }
	```

Edit layout file:
	
	```php
	//Into head
	if(!empty($this->jsCustomUrl))$this->plugin('InlineScript')->appendFile($this->jsCustomUrl.'?'.time());//Set time() force browser not to cache file, it's not mandatory
	elseif(is_array($this->jsCustomFiles))foreach($this->jsCustomFiles as $sJsCustomFile){
		$this->plugin('InlineScript')->appendFile($sJsCustomFile.'?'.time());//Set time() force browser not to cache file, it's not mandatory
	}
	```
