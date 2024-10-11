<?php

namespace FOF\SVGFAVICON\Services;

class Validate
{
    public function svg($key, $value){

        $check_color = function($value){

            $re = '/#([a-fA-F0-9]{3}){1,2}\b/';

            preg_match($re, $value, $matches, PREG_OFFSET_CAPTURE, 0);

            return !empty($matches[0][0]);
        };

        $svg_check = function($value){

            if( !$value ){
                return true;
            }

            if (filter_var($value, FILTER_VALIDATE_URL)) {

                $filetype = wp_check_filetype($value);

                if( !isset($filetype['type']) ){
                    return false;
                }

                if(  $filetype['type'] == 'image/svg+xml' ){
                    return true;
                }

            } else {

                $mime_type = strpos($value, 'image/svg+xml');

                return !($mime_type === false);
            }

            return false;
        };

        $v = [
            'mask_icon' => $svg_check,

            'svg_favicon' => $svg_check,

            'name' => function($value) { return !empty($value); },

            'mask_color' => $check_color,

            'background_color' => $check_color,

            'theme_color' => $check_color,

            'use_manifest' => function($value) { return $value; },
        ];

        return $v[$key]($value);
    }
}