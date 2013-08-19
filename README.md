AssetsBundle, v2.0
=======

[![Build Status](https://travis-ci.org/neilime/zf2-assets-bundle.png?branch=master)](https://travis-ci.org/neilime/zf2-assets-bundle)
![Code coverage](https://raw.github.com/zf2-boiler-app/app-test/master/ressources/100%25-code-coverage.png "100% code coverage")

NOTE : If you want to contribute don't hesitate, I'll review any PR.

Introduction
------------

AssetsBundle is a module for ZF2 allowing asset management (bundling & caching) like Css, Js and Less, dependent on modules, controllers and actions (di). 
This module manages the concept of the environment/production development.

In development : 
 - Files are not bundled for easier debugging.
 - Less files are compiled when updated or if an "@import" inside is updated
 
In production :
 
 - All files are bundled and cached once only if needed.

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)
* [lessphp](https://github.com/neilime/lessphp) (latest master).
* [Minify](https://github.com/mrclay/minify) (latest master).

Installation
------------

### Main Setup

#### By cloning project

1. Install the [lessphp fork](https://github.com/Nodge/lessphp), [Minify](https://github.com/mrclay/minify) by cloning them into `./vendor/`.
2. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "neilime/zf2-assets-bundle": "dev-master"
    }
    ```

2. Now tell composer to download AssetsBundle by running the command:

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
    			'css' => array('css'),
    			'js' => array(
    				'js/jquery.min.js',
    				'js/bootstrap.min.js'
    			),
    			'media' => array('images')
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
5. Save & Refresh.

# Configuration

The default configuration is setup to run with "Application ZF2 Skeleton"

1. _AssetsBundle_ :

 * boolean `production`: Define the application environment (development => false). Default `true`.
 * mixed `lastModifiedTime`: (optionnal) Allows you to define an arbitrary asset's last modified time in production.  Default `null` : last modified time is calculated for each asset.
 * string `basePath` : (optionnal) only needed if `cacheUrl` use `@zfBaseUrl`. If undefined, \Zend\Http\PhpEnvironment\Request::getBasePath() is used.
 * string `cachePath` : cache directory absolute path, you can use the `@zfRootPath` constant corresponding to current working directory. Default `@zfRootPath/public/cache`.
 * string `assetsPath` : (optionnal) assets directory absolute path, allows you to define relative path for assets config. You can use the constant `@zfRootPath` corresponding to current working directory. Default `@zfRootPath/public`.
 * string `cacheUrl` : cache directory base url, you can use the constant `@zfBaseUrl` corresponding to application base url . Default `@zfBaseUrl/assets/cache/`.
 * array `mediaExt` : Put here all medias extensions to be cached. Default `array('jpg','png','gif','cur','ttf','eot','svg','woff')`.
 * boolean `recursiveSearch`: If you define a folder as required asset, it will search for matching assets in that folder and its subfolders. Default `false`.

2. Assets :

 You can define assets for modules / controllers / action
 
 Exemple : 
 
 	```php
	<?php
	return array(
		//...
    	'assets' => array(
    			//Common assets    			
    			'css' => array(), //Define css files to include
    			'js' => array(), //Define js files to include
    			'less' => array(), //Define less files to include
    			'media' => array(), //Define images to manage
    			
    			//Module assets
    			'Test' =>  => array(
    				'css' => array(),
	    			'js' => array(),
	    			'less' => array(), 
	    			'media' => array(),
    			    			
	    			//Controller assets
	    			'Test\Controller\Name' => array(
	    				'css' => array(),
		    			'js' => array(),
		    			'less' => array(), 
		    			'media' => array(),
		    			
		    			//Action assets
		    			'ActionName'=> array(
		    				'css' => array(),
			    			'js' => array(),
			    			'less' => array(), 
			    			'media' => array()
	    				),
	    				//...
	    			),
	    			//...
	    		),
	    		//...
    		)
	    ),
	    //...
	);
	```
	
- For each asset, you can specify files or directories. All these elements are related to the asset path by default, 
but you can specify an absolute path or use the constants "@zfAssetsPath" and "@zfRootPath".
If you specify a directory, all files matching the asset type (css, less, js, media) will be included.
	
- You can use `.php` files as assets, there will be interpret.

- You can use url for `js` and `css` assets :

	```php
	<?php
	return array(
		//...
    	'assets' => array(
			'js' => array('http://ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools.js'),
			//...
    	)    			
    	//...
    );
    ```
    
    This example includes `Mootools` from _Google Hosted Libraries_
	
- You can define an inclusion order like this :
	
	```php
	<?php
	return array(
		//...
    	'assets' => array(
			'js' => array('js/firstFile.js','js'),
			//...
    	)    			
    	//...
    );
    ```
    
   	This example includes the file "firstFile.js" first, and all other javascript files in the folder "js"
   	
3. Custom Js :

	This function allows you to dynamically include javascript files. For exemple, files specific to user settings.
	In this case, your controller that need these file have to extend `AssetsBundle\Mvc\Controller\AbstractActionController`.
	
	__Attention !__ Jscustom process does not cache javascript, due to performance reasons
	
	Then create a jscustomAction function into your controller : 
	
	```php
	public function jscustomAction($sAction = null){
		//Check params, it's not mandatory
		if(empty($sAction)){
			$sAction = $this->params('js_action');
			if(empty($sAction))throw new \InvalidArgumentException('Action param is empty');
		}

		$aJsFiles = array();
		switch(strtolower($sAction)){
			case 'myActionName':
				//Put here all js files needed for "myActionName" action
				$aJsFiles[] = 'js/test.js';
				$aJsFiles[] = 'js/test.php';
				break;
		}
		return $aJsFiles;
	}
	```	

	Edit layout file:
		
	```php
	//Into head
	
	//Production case
	if(!empty($this->jsCustomUrl))$this->plugin('InlineScript')->appendFile($this->jsCustomUrl.'?'.time()); //Set time() force browser not to cache file, it's not mandatory
	//Development case
	elseif(is_array($this->jsCustomFiles))foreach($this->jsCustomFiles as $sJsCustomFile){
		$this->plugin('InlineScript')->appendFile($sJsCustomFile);
	}
	```
	
# Tools

_AssetsBundle_ provides console tools.

## Features

    Rendering all assets
    Empty cache directory

## Usage

### Rendering all assets

    php public/index.php render
    
### Empty cache directory

    php public/index.php empty     
