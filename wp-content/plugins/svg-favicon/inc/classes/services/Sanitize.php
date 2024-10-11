<?php

namespace FOF\SVGFAVICON\Services;

class Sanitize
{
    public function string(string $string) : string {
        return sanitize_textarea_field( $string );
    }
}