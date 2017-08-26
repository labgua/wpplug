<?php

namespace Labgua\WPPlug;


class EventDispatcher
{

    /**
     * @param $event_name   string name of the event
     * @param $event        mixed data of the event
     */
    public static function dispatch($event_name, $event = '')
    {
        do_action($event_name, $event);
    }

    /**
     * @param $event_name   string of the event
     * @param $closure      \Closure have one argument, the event!
     */
    public static function addListener($event_name, $closure, $priority = 10)
    {
        add_action($event_name, [&$closure, "__invoke"], $priority);
    }

}