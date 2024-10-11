<?php

namespace FOF\SVGFAVICON\Tools;

class Ajax
{
    public function json_header(array $obj){
        ob_start();
        echo json_encode($obj, JSON_NUMERIC_CHECK);
        header('Content-type: application/json');
        ob_end_flush();
    }
}