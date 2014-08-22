<?php
/*
Plugin Name: Slider
Plugin URI: https://github.com/tronsha/wp-slider
Description: Slider Plugin
Version: 1.0
Author: Stefan Hüsges
Author URI: http://www.mpcx.net/
Copyright: Stefan Hüsges
License: MIT
*/

defined('ABSPATH') or die("No script kiddies please!");

function initSlider()
{
    wp_enqueue_style('slider', WP_PLUGIN_URL . '/wp-slider/slider/css/slider.css');
    wp_enqueue_script('slider', WP_PLUGIN_URL . '/wp-slider/slider/js/slider.js', array('jquery'));
    wp_enqueue_script('slider-responsive', WP_PLUGIN_URL . '/wp-slider/slider/js/responsive.js', array('jquery'));
}

function getSlider($att = array(), $content = null)
{
    if (empty($content) === true) {
        $content = '';
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

        foreach ($files as $file) {
            $content .= '<img src="' . WP_PLUGIN_URL . '/wp-slider/slider/images/' . $file . '" />';
        }
    } else {
        preg_match_all('/<img[^>]+>/i', do_shortcode($content), $matches);
        $content = '';
        foreach ($matches[0] as $image) {
            $content .= $image;
        }
    }

    $options = '';

    if (isset($att['delay']) === true) {
        $options .= 'delay: ' . $att['delay'] . ', ';
    }
    if (isset($att['interval']) === true) {
        $options .= 'interval: ' . $att['interval'] . ', ';
    }

    $style = '';
    $change = '';
    $position = '';

    if (isset($att['height']) === true) {
        $style .= 'height: ' . $att['height'] . 'px; ';
    }
    if (isset($att['width']) === true) {
        $style .= 'width: ' . $att['width'] . 'px; ';
    }
    if (isset($att['change']) === true && $att['change'] === 'false') {
        $change .= 'display: none; ';
    }
    if (isset($att['position']) === true && $att['position'] === 'false') {
        $position .= 'display: none; ';
    }

    $textBox = '';
    if (isset($att['text']) === true) {
        $textArray = explode('|', $att['text']);
        $textBox = '<div class="text">';
        foreach($textArray as $key => $sliderText) {
            $textBox .= '<span class="' . ($key == 0 ? 'active' : '') . (empty($sliderText) ? ' hidden' : '') . '">' . $sliderText . '</span>';
        }
        $textBox .= '</div>';
    }

    $output = '
<div id="slider" class="slider" style="' . $style . '">
    <div class="slides">
    ' . $content . '
    </div>
    <div class="prev" style="' . $change . '"><div></div></div>
    <div class="next" style="' . $change . '"><div></div></div>
    <div class="position" style="' . $position . '"></div>
    ' . $textBox . '
</div>
<script>
    jQuery(document).ready(function () {
        jQuery("#slider").slider({' . $options . '});
    });
</script>
    ';

    if (empty($content) === false) {
        return $output;
    }
    return '';
}

add_action('init', 'initSlider');
add_shortcode('slider', 'getSlider');
