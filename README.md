AssetsBundle
=======
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

1. Install the [lessphp fork](https://github.com/Nodge/lessphp) (latest master), [CssMin](https://github.com/natxet/CssMin),[JsMin](https://github.com/nick4fake/JsMin) by cloning them into `./vendor/`.
2. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "neilime/zf2-assets-bundle": "dev-master"
    }
    ```

2. Due to bug in lessphp you have to use Nodge's fork, add this repository in your composer.json:
	
	```json
    "repositories": [{
        "type": "vcs",
        "url": "http://github.com/Nodge/lessphp"
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
            'Neilime\AssetsBundle',
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
	    			'img' => array('images')
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
    - string assetPath : assets directory absolute path, allows you to define relative path for assets config. You can use the constant "@zfRootPath" corresponding to current working directory. Default "@zfRootPath/public".
    - string cacheUrl : cache directory base url, you can use the constant "@zfBaseUrl" corresponding to application base url . Default "@zfBaseUrl/assets/cache/".
    - array imgExt : Put here all images extensions to be cached. Default array('png','gif','cur').

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
    			'img' => array() //Define images to manage
    			    			
    			'Test/Controller/Name' => array(
    				'css' => array(),
	    			'js' => array(),
	    			'less' => array(), 
	    			'img' => array()
	    			
	    			'ActionName'=> array(
	    				'css' => array(),
		    			'js' => array(),
		    			'less' => array(), 
		    			'img' => array()
    				)
    			)
    			//...
    		)
	    ),
	    //...
	);
	```
	
	For each of assets type you can set files or directories. All of these are relative to asset path by default, but you can set absolute path or use "@zfAssetPath" and "@zfRootPath" constants.
	If you set directory all files that matching asset type are included.
	You can set an includes order you can do this : 
	
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
    ```php
    
   	This example includes the file "firstFile.js" first, and all other javascript files in the folder "js"
