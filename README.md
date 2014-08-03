Nooku Framework
===============

What is Nooku Framework?
-------------------------

Nooku Framework is an open-source extension framework for [Joomla](http://www.joomla.orh). Developed and maintained by [Timble](http://timble.net) with the help of passionate developers from all over the world.

Nooku Framework can be installed in Joomla as a plugin and lets you focus on the "business" logic of your extension. By doing most of the repetitive work for you [boilerplate code](http://en.wikipedia.org/wiki/Boilerplate_code) is eliminated which greatly reduces the time to develop Joomla extensions. 

Nooku Framework provides you with excellent out-of-the-box security features. The design pattern based architecture makes your extension more flexible: your code becomes more re-usable, extensible and more easily replaceable. 

Nooku Framework uses a [component based architecture](http://en.wikipedia.org/wiki/Component-based_software_engineering) and includes everything needed to create Joomla extensions according to the [Hierarchical Model-View-Contoller](http://en.wikipedia.org/wiki/Hierarchical_model%E2%80%93view%E2%80%93controller) (HMVC) pattern.

Why Nooku Framework?
--------------------

Nooku Framework is built to help developers create complex custom Joomla extensions and tries to solve following problems:

* Abstract differences between Joomla versions 2.x and 3.x.
* Provide a solid modern and lean architecture for building extensions.
* Allow extensions to be extended easily.

Who is Nooku Framework for ?
----------------------------

Nooku Framework is for developers creating custom Joomla extensions. The framework can only be installed using Composer and cannot be installed using the Joomla installer. If you are a developer who wishes to distribute the framework as part of an installable package please [contact us](http://www.timble.net/contact/) first. 

Production
----------

The framework uses a conservative development approach focussed on the lowest common denominator, at time of writing this is Joomla 2.5 and PHP 5.2. The framework is fully forwards compatible with Joomla 3.x and PHP 5.x. 

The framework follows the [semantic versioning standard](http://semver.org/). Rest assured : it's designed for stability and compatibility. We promise that all minor versions will be 100% backwards compatible. Only in major versions backwards compatibility is not guarenteed.

Note : If you are looking for the the greatest and latest stuff we are working on check out [Nooku Platform](https://github.com/nooku/nooku-platform), a fork from Joomla 1.5 that we are completely rebuilding using Nooku Framework. 

Requirements
------------

* Joomla 2.5 and 3.x 
* PHP 5.2 or newer
* MySQL 5.x

Installation
------------

* Create a `composer.json` file in the root directory of your Joomla installation and require the `nooku/nooku-framework` package:

```json
{
    "require": {    	
        "nooku/nooku-framework": "dev-develop"
    },
    "minimum-stability": "dev"
}
```

* Install by executing `composer install`.
* Enable the plugin called `System - Nooku Framework` in Joomla.

License
-------

Nooku Framework is open-source software licensed under the [GPLv3 license](develop/LICENSE.md).
