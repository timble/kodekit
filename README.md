## Koowa Framework

### What is Koowa?
Koowa is an open-source extension framework for Joomla. Developed and maintained by [Joomlatools](http://joomlatools.com).  

By doing most of the repetitive work, Koowa greatly reduces the time to develop Joomla extensions, allowing you to focus on the things that matter: features.

Koowa uses a [component based architecture](http://en.wikipedia.org/wiki/Component-based_software_engineering) and includes everything needed to create Joomla components according to the [Hierarchical Model-View-Contoller](http://en.wikipedia.org/wiki/Hierarchical_model%E2%80%93view%E2%80%93controller) (HMVC) pattern.

### Why Koowa?

Koowa is built to help Joomlatools create complex Joomla extensions and tries to solve following problems:

* Abstract differences between Joomla versions 2.x and 3.x.
* Act as a solid base for building extensions.
* Allow extensions to be extended easily.

### Requirements
* Joomla 2.5 or newer
* PHP 5.2 or newer

### Installation

* Create a `composer.json` file in the root directory of your Joomla installation and require the `joomlatools/koowa` package:

```json
	{
    	"require": {    	
    		"joomlatools/koowa": "dev-develop"
    	},
    	"minimum-stability": "dev"
	}
```

* Install by executing `composer install`.
* Enable the plugin called `System - Joomlatools framework` in Joomla.

### License

Koowa framework is open-source software licensed under the [GPLv3 license](develop/LICENSE.md).