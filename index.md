---
layout: default
title: Home
---
# AssetsBundle - Zend Framework 2 module

[![Build Status](https://travis-ci.org/neilime/zf2-assets-bundle.svg?branch=master)](https://travis-ci.org/neilime/zf2-assets-bundle)
[![Latest Stable Version](https://poser.pugx.org/neilime/zf2-assets-bundle/v/stable.svg)](https://packagist.org/packages/neilime/zf2-assets-bundle)
[![Total Downloads](https://poser.pugx.org/neilime/zf2-assets-bundle/downloads.svg)](https://packagist.org/packages/neilime/zf2-assets-bundle)
[![Coverage Status](https://coveralls.io/repos/github/neilime/zf2-assets-bundle/badge.svg?branch=master)](https://coveralls.io/github/neilime/zf2-assets-bundle?branch=master)
[![Beerpay](https://beerpay.io/neilime/zf2-assets-bundle/badge.svg)](https://beerpay.io/neilime/zf2-assets-bundle)

****

__⚠️ For Zend Framework 3+, please use [zf-assets-bundle](https://github.com/neilime/zf-assets-bundle) ⚠️__

****

_AssetsBundle_ is a module for Zend Framework 2 providing assets management (bundling & caching) like Css, Js and Less, dependent on modules, controllers and actions .
This module supports the concept of the "development/production" environment.

In development :
 - Files are not bundled for easier debugging.
 - Less files are compiled when updated or if an "@import" inside is updated

In production :
 - All files are bundled and cached once only if needed.
 - Assets path are encrypted to mask file tree (with the exception of files in the "assets" directory)

# Helping Project

❤️ If this project helps you reduce time to develop and/or you want to help the maintainer of this project, you can support him on [![Beerpay](https://beerpay.io/neilime/zf2-assets-bundle/badge.svg)](https://beerpay.io/neilime/zf2-assets-bundle) Thank you !

# Contributing

If you wish to contribute to this project, please read the [CONTRIBUTING.md](CONTRIBUTING.md) file.
NOTE : If you want to contribute don't hesitate, I'll review any PR.

# Pages

1. [Installation](https://github.com/neilime/zf2-assets-bundle/wiki/Installation)
2. [Use with Zend Skeleton Application](https://github.com/neilime/zf2-assets-bundle/wiki/Use-with-Zend-Skeleton-Application)
3. [Configuration](https://github.com/neilime/zf2-assets-bundle/wiki/Configuration)
4. [Custom Js](https://github.com/neilime/zf2-assets-bundle/wiki/Custom-Js)
5. [Console tools](https://github.com/neilime/zf2-assets-bundle/wiki/Console-tools)
6. [FAQ](https://github.com/neilime/zf2-assets-bundle/wiki/FAQ)
7. [PHP Doc](https://neilime.github.io/zf2-assets-bundle/phpdoc/)
8. [Code Coverage](https://coveralls.io/github/neilime/php-css-lint)
