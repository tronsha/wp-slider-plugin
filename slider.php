<?php
/**
 * @link              https://github.com/tronsha/wp-slider-plugin
 * @since             1.0.0
 * @package           wp-slider-plugin
 *
 * @wordpress-plugin
 * Plugin Name:       MPCX Slider
 * Plugin URI:        https://github.com/tronsha/wp-slider-plugin
 * Description:       Slider Plugin
 * Version:           1.0.0
 * Author:            Stefan Hüsges
 * Author URI:        http://www.mpcx.net/
 * Copyright:         Stefan Hüsges
 * License:           MIT
 * License URI:       https://raw.githubusercontent.com/tronsha/wp-slider-plugin/master/LICENSE
 */

defined('ABSPATH') or (@include_once explode('wp-content', __DIR__)[0] . '/wp-hide.php') or die;

class Slider
{
    private $att = array();
    private $content = null;

    public function __construct($att = array(), $content = null)
    {
        $this->att = $att;
        $this->content = $content;
    }

    protected function cleanContent($content)
    {
        preg_match_all('/<img[^>]+>/i', $content, $matches);
        $content = '';
        foreach ($matches[0] as $image) {
            $content .= $image;
        }
        return $content;
    }

    protected function getSlides()
    {
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
            $content .= '<img src="' . plugin_dir_url(__FILE__) . 'slider/images/' . $file . '" alt="Slider Image" />';
        }
        return $content;
    }

    public function render()
    {
        $att = $this->att;

        if (empty($this->content) === false) {
            $content = do_shortcode($this->content);
            $content = $this->cleanContent($content);
        } else {
            $content = $this->getSlides();
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
            foreach ($textArray as $key => $sliderText) {
                $textBox .= '<span class="' . ($key == 0 ? 'active' : '') . (empty($sliderText) ? ' hidden' : '') . '">' . $sliderText . '</span>';
            }
            $textBox .= '</div>';
        }

        $output = '
            <div id="slider" class="slider" style="' . $style . '">
                <div class="slides">
                ' . $content . '
                </div>
                <div class="prev" style="' . $change . '"><div>&#160;</div></div>
                <div class="next" style="' . $change . '"><div>&#160;</div></div>
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
}

add_action(
    'init',
    function () {
        if (!is_admin()) {
            wp_register_style(
                'slider',
                plugin_dir_url(__FILE__) . 'slider/css/slider.css',
                array(),
                '1.0.0'
            );
            wp_register_script(
                'slider',
                plugin_dir_url(__FILE__) . 'slider/js/slider.js',
                array('jquery'),
                '1.0.0'
            );
            wp_register_script(
                'slider-responsive',
                plugin_dir_url(__FILE__) . 'slider/js/responsive.js',
                array('jquery', 'slider'),
                '1.0.0'
            );
            wp_enqueue_style('slider');
            wp_enqueue_script('slider');
            wp_enqueue_script('slider-responsive');
        }
    }
);

add_shortcode(
    'slider',
    function ($att = array(), $content = null) {
        $slider = new Slider($att, $content);
        return $slider->render();
    }
);
