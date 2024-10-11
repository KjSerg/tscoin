<?php

namespace FOF\SVGFAVICON\Tools;

class SVGAllowedTags extends \enshrined\svgSanitize\data\AllowedTags
{
    public static function getTags() {
        return parent::getTags(['style']);
    }
}