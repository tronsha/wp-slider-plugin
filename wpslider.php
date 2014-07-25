<?php
/*
Plugin Name: Slider
Plugin URI: https://github.com/tronsha/wpslider
Description: Slider Plugin
Version: 1.0
Author: Stefan Hüsges
Author URI: http://www.mpcx.net/
Copyright: Stefan Hüsges
License: MIT
*/

function initSlider()
{
    wp_enqueue_script('slider.javascript', WP_PLUGIN_URL . '/wpslider/slider/js/slider.js', array('jquery'));
    wp_enqueue_script('responsive.javascript', WP_PLUGIN_URL . '/wpslider/slider/js/responsive.js', array('jquery'));
    wp_enqueue_style('slider.styles', WP_PLUGIN_URL . '/wpslider/slider/css/slider.css');
}

function getSlider()
{
    $files = array();
    if ($handle = opendir(realpath(dirname(__FILE__) . '/slider/images/'))) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $files[] = $file;
            }
        }
        closedir($handle);
    }

    sort($files);

    $output = '
<div id="slider">
    <div class="slides">
    ';

    $counter = 0;
    foreach ($files as $file) {
        $output .= '<img class="image' . (++$counter) . '" src="' . WP_PLUGIN_URL . '/wpslider/slider/images/' . $file . '" />';
    }

    $output .= '
    </div>
    <div class="prev"></div>
    <div class="next"></div>
    <div class="position"></div>
</div>
<script>
    jQuery(document).ready(function () {
        jQuery("#slider").slider({delay: 1000, interval: 5000});
    });
</script>
    ';

    if ($counter > 0) {
        return $output;
    }
    return '';
}

add_action('init', 'initSlider');
