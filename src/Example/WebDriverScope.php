<?php
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
