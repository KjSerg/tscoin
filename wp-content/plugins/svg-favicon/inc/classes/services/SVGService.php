<?php

namespace FOF\SVGFAVICON\Services;

use FOF\SVGFAVICON\Models\PluginInfo;
use enshrined\svgSanitize\Sanitizer;
use FOF\SVGFAVICON\Tools\SVGAllowedTags;

class SVGService
{
    private $pluginInfo;

    public function __construct(PluginInfo $pluginInfo)
    {
        $this->pluginInfo = $pluginInfo;

    }

    public function getSVGFromUrl(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) return null;

        $name = basename($url);
        $upload_dir = wp_upload_dir();

        $svg = file_get_contents($upload_dir['basedir'] . '/svg_favicon/' . $name);

        if (!$svg) return null;

        $sanitizer = new Sanitizer();
        $sanitizer->setAllowedTags(new SVGAllowedTags());
        return $sanitizer->sanitize($svg);
    }

    public function handle_manifest($options, $key = '', $value = '', $svg = ''): string
    {

        $manifest = [
            "name" => empty($options['name']) ? get_bloginfo('name') : $options['name'],
            "short_name" => empty($options['name']) ? get_bloginfo('name') : $options['name'],
            "icons" => [
                "src" => empty($svg) ?? $options['svg_favicon'] ?? '',
                "sizes" => "512x512"
            ],
            "background_color" => empty($options['background_color']) ? '#ffffff' : $options['background_color'],
            "theme_color" => empty($options['theme_color']) ? '#ffffff' : $options['theme_color'],
            "display" => "fullscreen"
        ];

        if (isset($manifest[$key])) {
            $manifest[$key] = $value;
        }

        if ($key == 'name') {
            $manifest['short_name'] = $value;
        }

        if (!empty($svg)) {
            $manifest['icons']['src'] = $svg;
        }

        $upload_dir = wp_upload_dir();

        $base_dir = $upload_dir['basedir'] . '/';
        $base_url = $upload_dir['baseurl'] . '/';

        $file_dir = $base_dir . 'svg_favicon';
        $file_svg_manifest = $base_dir . 'svg_favicon/manifest.json';
        $file_svg_manifest_url = $base_url . 'svg_favicon/manifest.json';

        $manifest = json_encode($manifest);

        if (!is_dir($file_dir)) {
            wp_mkdir_p($file_dir);
        }

        if (file_exists($file_svg_manifest)) {
            wp_delete_file($file_svg_manifest);
        }

        file_put_contents($file_svg_manifest, $manifest, FILE_APPEND);

        return $file_svg_manifest_url;
    }

    private function decode_chunk($svg)
    {

        $svg = explode(';base64,', $svg);

        if (!is_array($svg) || !isset($svg[1])) {
            return false;
        }

        $svg = base64_decode($svg[1]);

        if (!$svg) {
            return false;
        }

        return $svg;
    }

    public function handle_svg($svg, $key = 'svg_favicon')
    {

        if (filter_var($svg, FILTER_VALIDATE_URL)) {
            return $svg;
        }

        $this->remove_svg($key);

        $svg = $this->decode_chunk($svg);

        if ($svg === false) {
            return false;
        }

        $upload_dir = wp_upload_dir();

        $base_dir = $upload_dir['basedir'] . '/';
        $base_url = $upload_dir['baseurl'] . '/';

        //$test = date( DATE_ATOM, time() );

        $file_dir = $base_dir . 'svg_favicon';
        $file_svg_favicon = $base_dir . 'svg_favicon/' . $this->pluginInfo->svg_map[$key] . '.svg';
        $file_svg_favicon_url = $base_url . 'svg_favicon/' . $this->pluginInfo->svg_map[$key] . '.svg';

        if (!is_dir($file_dir)) {
            wp_mkdir_p($file_dir);
        }

        if (file_exists($file_svg_favicon)) {
            wp_delete_file($file_svg_favicon);
        }

        $sanitizer = new Sanitizer();

        $sanitizer->setAllowedTags(new SVGAllowedTags());
        $svg = $sanitizer->sanitize($svg);

        if (!$svg) {
            return null;
        }

        $results = file_put_contents($file_svg_favicon, $svg, FILE_APPEND);

        return $file_svg_favicon_url;
    }

    public function upload_svg_files($allowed)
    {

        if (!current_user_can('manage_options')) {
            return $allowed;
        }

        $allowed['svg'] = 'image/svg+xml';
        $allowed['json'] = 'application/json';

        return $allowed;
    }

    public function enable_svg_upload(): bool
    {
        $parse_url = parse_url(wp_get_referer(), PHP_URL_QUERY);

        if ($this->pluginInfo->referrer_check == $parse_url) {

            add_filter('upload_mimes', [$this, 'upload_svg_files']);
            add_filter('wp_check_filetype_and_ext', [$this, 'file_and_ext'], 10, 4);

            $allowed = get_allowed_mime_types();

            if (isset($allowed['svg'])) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    public function remove_manifest()
    {

        $upload_dir = wp_upload_dir();

        $base_dir = $upload_dir['basedir'] . '/';

        $file_svg_favicon_manifest = $base_dir . 'svg_favicon/manifest.json';

        if (file_exists($file_svg_favicon_manifest)) {
            wp_delete_file($file_svg_favicon_manifest);
        }

        return null;
    }

    public function remove_svg($type)
    {

        if (!isset($this->pluginInfo->svg_map[$type])) {
            return;
        }

        $upload_dir = wp_upload_dir();

        $base_dir = $upload_dir['basedir'] . '/';

        $file_svg_favicon = $base_dir . 'svg_favicon/' . $this->pluginInfo->svg_map[$type] . '.svg';

        if (file_exists($file_svg_favicon)) {
            wp_delete_file($file_svg_favicon);
        }

        return null;
    }

    private function prepForUpload(string $file_name, string $ext): array
    {
        $upload_dir = wp_upload_dir();

        $base_dir = $upload_dir['basedir'].'/';
        $base_url = $upload_dir['baseurl'].'/';

        return [
            'file_dir' => $base_dir . 'svg_favicon',
            'base_dir' => $base_dir . "svg_favicon/$file_name.$ext",
            'base_url' => $base_url."svg_favicon/$file_name.$ext",
        ];
    }

    private function upload(array $paths, $data) {

        if (!is_dir($paths['file_dir'])) {
            wp_mkdir_p($paths['file_dir']);
        }

        if (file_exists($paths['base_dir'])) {
            wp_delete_file($paths['base_dir']);
        }

        return file_put_contents($paths['base_dir'], $data, FILE_APPEND);
    }

    public function svgToImage(string $img): array {

        $img = str_replace('data:image/png;base64,', '', $img);//replace the name of image
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        $paths = $this->prepForUpload('apple-touch-icon', 'png');

        return [
            'status' => $this->upload($paths, $data),
            'link' => $paths['base_url'],
        ];
    }
}