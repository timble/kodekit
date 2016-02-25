# Nooku Framework

[ ![Codacy Badge](https://www.codacy.com/project/badge/c6cc3c05cc7a4d13806602c7647f4476) ](https://www.codacy.com/app/timble/nooku-framework)

## What is Nooku Framework?

Nooku Framework is a open-source **extension framework** for **[Joomla](http://www.joomla.org)**. Developed and maintained by
[Timble](http://timble.net) with the help of passionate developers from all over the world.

Nooku Framework can be installed in Joomla as a plugin and lets you focus on the "business" logic of your extension. By
doing most of the repetitive work for you [boilerplate code][boilerplate] is eliminated which greatly reduces the time
to develop Joomla extensions.

The design pattern based architecture makes your extension more flexible: your extension becomes more **re-usable**,
**replaceable** and more easily **extensible**. Additionally Nooku Framework provides you with excellent **out-of-the-box
 security** features.

Nooku Framework uses a [component based architecture](http://en.wikipedia.org/wiki/Component-based_software_engineering)
and includes everything needed to create Joomla extensions according to the [Hierarchical Model-View-Contoller][HMVC]
**(HMVC)** pattern.

## Why Nooku Framework?

Nooku Framework is built to help developers create **custom** Joomla extensions and tries to solve following problems:

* Abstract differences between Joomla versions 2.x and 3.x.
* Provide a solid modern and lean architecture for building extensions.
* Allow extensions to be more flexible and extended easily.

## Who is Nooku Framework for ?

Nooku Framework is for developers creating custom Joomla extensions. The framework can only be installed using Composer
and cannot be installed using the Joomla installer. If you are a developer who wishes to distribute the framework as part
of an installable package please [contact us](http://www.timble.net/contact/) first.

## Production Ready

The framework uses a conservative development approach focused on the lowest common denominator, at time of writing this
is Joomla 2.5 and PHP 5.3. The framework is fully forwards compatible with Joomla 3.x and PHP 5.x.

The framework follows the [semantic versioning standard](http://semver.org/). Rest assured : it's designed for stability
and compatibility. We promise that all minor versions will be 100% backwards compatible. Only in major versions backwards
compatibility is not guaranteed.

*Note* : If you are looking for the the greatest and latest stuff we are working on check out [Nooku Platform][nooku-platform],
a fork from Joomla that is being completely rebuild using Nooku Framework.

## Requirements

* Joomla 2.5 and 3.x
* PHP 5.3 or newer
* MySQL 5.x

## Installation

Go to the root directory of your Joomla installation in command line and execute this command:

```
composer require nooku/nooku-framework:2.*
```

## Contributing

Nooku Framework is an open source, community-driven project. Contributions are welcome from everyone. 
We have [contributing guidelines](CONTRIBUTING.md) to help you get started.

## Contributors

See the list of [contributors](https://github.com/nooku/nooku-framework/contributors).

## License 

Nooku Framework is free and open-source software licensed under the [GPLv3 license](LICENSE.txt).

## Community

Keep track of development and community news.

* Follow [@joomlatoolsdev on Twitter](https://twitter.com/joomlatoolsdev)
* Join [joomlatools/dev on Gitter](http://gitter.im/joomlatools/dev)
* Read the [Joomlatools Developer Blog](https://www.joomlatools.com/developer/blog/)
* Subscribe to the [Joomlatools Developer Newsletter](https://www.joomlatools.com/developer/newsletter/)

[HMVC]: http://en.wikipedia.org/wiki/Hierarchical_model%E2%80%93view%E2%80%93controller
[boilerplate]: http://en.wikipedia.org/wiki/Boilerplate_code

[nooku-platform]: https://github.com/nooku/nooku-platform
[nooku-framework]: https://github.com/nooku/nooku-framework

[gitflow-model]: http://nvie.com/posts/a-successful-git-branching-model/
[gitflow-extensions]: https://github.com/nvie/gitflow
