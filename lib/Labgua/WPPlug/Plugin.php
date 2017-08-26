<?php

namespace Labgua\WPPlug;


/**
 * Class Plugin
 * @package LabguaFrameworkWP
 *
 * This is a basic implementation of a Plugin for wordpress.
 * It like a bundle with basic services, named components.
 *
 * Every component is a Registrable object, like Plugin!
 * So in reality, a plugin can made up of other plugin
 *
 * The plugin is a component made of component
 * OR the plugin is a plugin the contains plugins
 *
 * Use an instance of this class in the main <<plugin>>.php and then register it!
 */
class Plugin implements Registrable
{

    private $codename;
    private $version;
    private $filevar;

    private $components;

    function __construct($codename, $version, $filevar)
    {

        PathFactory::init($codename, $filevar);

        $this->codename = $codename;
        $this->version = $version;
        $this->filevar = $filevar;

        $this->components["setup"] = new Setup($codename, $version);
        $this->components["front"] = new FrontController($codename);
        $this->components["admin"] = new AdminController($codename);
        $this->components["ctp"] = new CustomPostTypes($codename);
        $this->components["services"] = new Services();
        $this->components["widgets"] = new Widgets($codename);
        $this->components["cron"] = new Cron($codename);
    }

    public function add($name_component, $component)
    {
        $this->components[$name_component] = $component;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->components)) {
            return $this->components[$name];
        }
        throw new \Exception("Component '$name' not found in " . $this->codename . " Plugin");
    }

    /**
     * @return mixed
     */
    public function getCodename()
    {
        return $this->codename;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getFilevar()
    {
        return $this->filevar;
    }

    /**
     * @return mixed
     */
    public function getComponents()
    {
        return $this->components;
    }


    /**
     *
     * Shortcut for EventDispatcher
     *
     * @param $event_name   string of the event
     * @param $closure      \Closure have one argument, the event!
     */
    public function addListener($event_name, $closure, $priority = 10)
    {
        EventDispatcher::addListener($event_name, $closure, $priority);
    }

    public function register()
    {
        foreach ($this->components as $cp) {
            $cp->register();
        }
    }

}