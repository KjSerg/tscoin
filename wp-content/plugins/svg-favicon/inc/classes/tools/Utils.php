<?php

namespace FOF\SVGFAVICON\Tools;

class Utils
{
    public function error_keys(): array
    {
        return [
            'background_color' => 'Background Color',
            'theme_color' => 'Theme Color',
            'mask_color' => 'Mask Color',
        ];
    }

    public function array_map_recursive( $callback, $array ) {
        if(empty($array)) return null;

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->array_map_recursive($callback, $value);
            }
            else {
                $array[$key] = call_user_func($callback, $value);
            }
        }

        return $array;
    }
}