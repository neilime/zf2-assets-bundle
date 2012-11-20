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

1. Install the [lessphp](https://github.com/leafo/lessphp) (latest master), [CssMin](https://github.com/natxet/CssMin),[JsMin](https://github.com/nick4fake/JsMin) by cloning them into `./vendor/`.
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
	    			'css' => array(
	    				'css/bootstrap-responsive.min.css',
	    				'css/bootstrap.min.css',
	    				'css/style.css'
	    			),
	    			'js' => array(
	    				'js/jquery.min.js',
	    				'js/bootstrap.min.js'
	    			),
	    			'img' => array('images/')
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
