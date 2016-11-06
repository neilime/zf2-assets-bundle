[![Build Status](https://travis-ci.org/neilime/zf2-assets-bundle.png?branch=master)](https://travis-ci.org/neilime/zf2-assets-bundle)
[![Latest Stable Version](https://poser.pugx.org/neilime/zf2-assets-bundle/v/stable.png)](https://packagist.org/packages/neilime/zf2-assets-bundle)
[![Total Downloads](https://poser.pugx.org/neilime/zf2-assets-bundle/downloads.png)](https://packagist.org/packages/neilime/zf2-assets-bundle)

_AssetsBundle_ is a module for Zend Framework providing assets management (bundling & caching) like Css, Js and Less, dependent on modules, controllers and actions .
This module supports the concept of the "development/production" environment.

In development :
 - Files are not bundled for easier debugging.
 - Less files are compiled when updated or if an "@import" inside is updated

In production :
 - All files are bundled and cached once only if needed.
 - Assets path are encrypted to mask file tree (with the exception of files in the "assets" directory)

# Helping Project 

If this project helps you reduce time to develop and/or you want to help the maintainer of this project, you can make a donation, thank you.

<a href='https://pledgie.com/campaigns/26668'><img alt='Click here to lend your support to: Zend Framework AssetsBundle and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/26668.png?skin_name=chrome' border='0' ></a>

# Contributing

If you wish to contribute to TwbBundle, please read the [CONTRIBUTING.md](CONTRIBUTING.md) file.
NOTE : If you want to contribute don't hesitate, I'll review any PR.

# Requirements

Name | Version
-----|--------
[php](https://secure.php.net/) | >=5.3.3
[zendframework/zend-eventmanager](https://github.com/zendframework/zend-eventmanager) | 2.*
[zendframework/zend-http](https://github.com/zendframework/zend-http) | 2.*
[zendframework/zend-mvc](https://github.com/zendframework/zend-mvc) | 2.*
[zendframework/zend-modulemanager](https://github.com/zendframework/zend-modulemanager) | 2.*
[zendframework/zend-config](https://github.com/zendframework/zend-config) | 2.*
[zendframework/zend-console](https://github.com/zendframework/zend-console) | 2.*
[zendframework/zend-view](https://github.com/zendframework/zend-view) | 2.*
[zendframework/zend-serializer](https://github.com/zendframework/zend-serializer) | 2.*
[zendframework/zend-log](https://github.com/zendframework/zend-log) | 2.*
[zendframework/zend-i18n](https://github.com/zendframework/zend-i18n) | 2.*
[oyejorge/less.php](https://github.com/oyejorge/less.php) | 1.*
[mrclay/minify](https://github.com/mrclay/minify) | 2.*
[tedivm/jshrink](https://github.com/tedivm/jshrink) | 1.*

# Pages

1. [Installation](https://github.com/neilime/zf2-assets-bundle/wiki/Installation)
2. [Use with Zend Skeleton Application](https://github.com/neilime/zf2-assets-bundle/wiki/Use-with-Zend-Skeleton-Application)
3. [Configuration](https://github.com/neilime/zf2-assets-bundle/wiki/Configuration)
4. [Custom Js](https://github.com/neilime/zf2-assets-bundle/wiki/Custom-Js)
5. [Console tools](https://github.com/neilime/zf2-assets-bundle/wiki/Console-tools)
6. [FAQ](https://github.com/neilime/zf2-assets-bundle/wiki/FAQ)
7. [PHP Doc](http://neilime.github.io/zf2-assets-bundle/phpdoc/)
8. [Code Coverage](http://neilime.github.io/zf2-assets-bundle/coverage/)
