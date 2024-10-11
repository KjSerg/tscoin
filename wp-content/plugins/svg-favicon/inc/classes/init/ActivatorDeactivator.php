<?php

namespace FOF\SVGFAVICON\Init;

class ActivatorDeactivator
{
    public function activate(){
        flush_rewrite_rules();
    }

    public function deactivate(){
        flush_rewrite_rules();
    }
}