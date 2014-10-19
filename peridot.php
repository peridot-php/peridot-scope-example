<?php
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
