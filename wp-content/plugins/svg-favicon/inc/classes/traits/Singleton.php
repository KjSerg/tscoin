<?php

namespace FOF\SVGFAVICON\Traits;

trait Singleton
{
    private static $instance;

    private final function __construct() {}
    private final function __clone() {}
    public final function __wakeup() {}

    public final static function get_instance()
    {
        if(!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}