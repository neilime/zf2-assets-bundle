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
