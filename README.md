# Omnipay: Redsyshttps://poser.pugx.org

**Redsys driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/AvaiBookSports/omnipay-redsys.png?branch=master)](https://travis-ci.org/AvaiBookSports/omnipay-redsys)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/AvaiBookSports/omnipay-redsys.svg?style=flat)](https://scrutinizer-ci.com/g/AvaiBookSports/omnipay-redsys/code-structure)
[![Code Quality](https://img.shields.io/scrutinizer/g/AvaiBookSports/omnipay-redsys.svg?style=flat)](https://scrutinizer-ci.com/g/AvaiBookSports/omnipay-redsys/?branch=master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Latest Stable Version](https://poser.pugx.org/AvaiBookSports/omnipay-redsys/version.png)](https://packagist.org/packages/AvaiBookSports/omnipay-redsys)
[![Total Downloads](https://poser.pugx.org/AvaiBookSports/omnipay-redsys/d/total.png)](https://packagist.org/packages/AvaiBookSports/omnipay-redsys)


[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.6+. This package implements Redsys support for Omnipay. It includes
support for both redirect (3-party) and webservice (2-party) versions of the gateway.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply run `composer require AvaiBookSports/omnipay-redsys`

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Redsys_Redirect
* Redsys_Webservice

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Translating Redsys messages

Each Redsys response code has a message for it. Spanish messages are fully implemented with the provided ones by Redsys documentation.

You can help improving those messages and adding translations in [avaibooksports/redsys-messages](https://github.com/AvaiBookSports/redsys-messages)

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/AvaiBookSports/omnipay-redsys/issues),
or better yet, fork the library and submit a pull request.
