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

Nooku Framework is built to help developers create complex Joomla extensions and tries to solve following problems:

* Abstract differences between Joomla versions 2.x and 3.x.
* Provide a solid modern architecture for building extensions.
* Allow extensions to be extended easily.

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
