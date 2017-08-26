<?php

namespace Labgua\WPPlug;

/**
 * Class PathFactory
 *
 * This class is a service that take track of the __FILE__ in to
 * all the plugn definition.
 * Designed for usign with more than one instance of plugin, it allows your plugins
 * to track __FILE__ via a codename, the code-name that define the plugin.
 *
 * So when you need the __FILE__ (for the wordpress functions) you can call getFile.
 * Instead when you need the path of the your plugin, you can call getPath.
 * But when you need the URL path for the frontend imports (like JS or CSS), you can call urlAsset
 */
class PathFactory
{

    private static $file = [];

    public static function init($codename, $in_file)
    {
        PathFactory::$file[$codename] = $in_file;
    }


    public static function getFile($codename)
    {
        return PathFactory::$file[$codename];
    }

    public static function getPath($codename)
    {
        return dirname(PathFactory::$file[$codename]);
    }

    public static function urlAsset($codename)
    {
        return plugins_url("", PathFactory::$file[$codename]);
    }

    private function __construct()
    {
    }

}