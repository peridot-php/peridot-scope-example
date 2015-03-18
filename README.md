Peridot Scope Example
=====================

This repo demonstrates using custom scopes to mix in test functionality for the [Peridot](https://github.com/peridot-php/peridot) testing framework for PHP.

Read more [here](http://peridot-php.github.io/#scopes).

##The custom scope

We first create a class that extends `Scope` so we can mix in functionality to our tests:

```php
<?php // src/Example/WebDriverScope
namespace Peridot\Example;

use Evenement\EventEmitter;
use Peridot\Core\Scope;

class WebDriverScope extends Scope
{
    /**
     * @var \RemoteWebDriver
     */
    protected $driver;

    /**
     * @var \Evenement\EventEmitter
     */
    protected $emitter;

    /**
     * @param \RemoteWebDriver $driver
     */
    public function __construct(\RemoteWebDriver $driver, EventEmitter $emitter)
    {
        $this->driver = $driver;
        $this->emitter = $emitter;

        //when the runner has finished lets quit the driver
        $this->emitter->on('runner.end', function() {
            $this->driver->quit();
        });
    }

    /**
     * Add a getPage method to our tests
     *
     * @param $url
     */
    public function getPage($url)
    {
        $this->driver->get($url);
    }

    /**
     * Adds a findElementById method to our tests
     *
     * @param $id
     * @return \WebDriverElement
     */
    public function findElementById($id)
    {
        return $this->driver->findElement(\WebDriverBy::id($id));
    }
}
```

##Configuring Peridot

We can mix in our `WebDriverScope` via the Peridot configuration file:

```php
<?php // peridot.php
use Peridot\Core\Suite;
use Peridot\Example\WebDriverScope;

require_once __DIR__ . '/vendor/autoload.php';

return function($emitter) {

    //create a single WebDriverScope to port around
    $driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', array(
        WebDriverCapabilityType::BROWSER_NAME => WebDriverBrowserType::FIREFOX
    ));
    $webDriverScope = new WebDriverScope($driver, $emitter);

    /**
     * We want all suites and their children to have the functionality provided
     * by WebDriverScope, so we hook into the suite.start event. Suites will pass their child
     * scopes to all child tests and suites.
     */
    $emitter->on('suite.start', function(Suite $suite) use ($webDriverScope) {
        $scope = $suite->getScope();
        $scope->peridotAddChildScope($webDriverScope);
    });
};
```

##Using mixed in behavior

By mixing in scopes, we can use the methods provided by them:

```php
<?php
describe('The home page', function() {
    it('should have a greeting', function() {
        $this->getPage('http://localhost:4000');
        $greeting = $this->findElementById('greeting');
        assert($greeting->getText() === "Hello", "should be Hello");
    });
});
```

##Running the tests

You will need a selenium server running, and you will want to run the `web` directory. Thankfully, running selenium is a snap using [webdriver-manager](https://github.com/peridot-php/webdriver-manager). From the project root:

```
$ vendor/bin/manager start
```

then fire up the built-in PHP server:

```
$ php -S localhost:4000 -t web/
```

then run peridot tests:

```
$ vendor/bin/peridot specs/
```
