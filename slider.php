<?php
/**
 * Plugin Name: UPA Slider
 * Plugin URI:  https://github.com/tronsha/wp-slider-plugin/blob/upa/README.md
 * Description: Slider Plugin
 * Version:     1.0.0
 * Author:      UPA-Webdesign
 * Author URI:  http://www.upa-webdesign.de/
 * Copyright:   Stefan Hüsges
 * License:     MIT
 * License URI: https://raw.githubusercontent.com/tronsha/wp-slider-plugin/master/LICENSE
 */

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
        $path = 'slider/images/';
        $dir = realpath(dirname(__FILE__) . '/' . $path) . '/';
        $type = array('png', 'jpg');
        $files = glob($dir . '*.{' . implode(',', $type) . '}', GLOB_BRACE);
        foreach ($files as $file) {
            $content .= '<img src="' . plugin_dir_url(__FILE__) . $path . basename($file) . '" alt="Slider Image" />';
        }
        return $content;
    }
    
        protected function getButtonPrev()
    {
        if (isset($this->att['change']) === true && $this->att['change'] === 'false') {
            return '';
        }
        $prevButton = '<div class="prev">';
        if (file_exists(get_template_directory() . '/images/slider/prev.png')) {
            $prevButton .= '<img src="' . get_template_directory_uri() . '/images/slider/prev.png" alt="prev">';
        } else {
            $prevButton .= '<div>&#160;</div>';
        }
        $prevButton .= '</div>';
        return $prevButton;
    }

    protected function getButtonNext()
    {
        if (isset($this->att['change']) === true && $this->att['change'] === 'false') {
            return '';
        }
        $nextButton = '<div class="next">';
        if (file_exists(get_template_directory() . '/images/slider/next.png')) {
            $nextButton .= '<img src="' . get_template_directory_uri() . '/images/slider/next.png" alt="next">';
        } else {
            $nextButton .= '<div>&#160;</div>';
        }
        $nextButton .= '</div>';
        return $nextButton;
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
                ' . $this->getButtonPrev() . ' 
                ' . $this->getButtonNext() . '
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

function initSlider()
{
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

function shortcodeSlider($att = array(), $content = null)
{
    $slider = new Slider($att, $content);
    return $slider->render();
}

add_action('init', 'initSlider');
add_shortcode('slider', 'shortcodeSlider');
