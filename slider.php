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
    private static $id = 0;
    private static $posts = null;
    private $att = array();
    private $content = null;

    public function __construct($att = array(), $content = null)
    {
        $this->att = $att;
        $this->content = $content;
        self::$id++;
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

    protected function getPosts($x = true)
    {
        if (self::$posts === null && $x === true) {
            self::$posts = get_posts(array(
                'offset' => 0,
                'category_name' => 'slides',
                'posts_per_page' => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
            ));
        }
        return self::$posts;
    }

    protected function getSlidesFromPosts()
    {
        $slides = '';
        $posts = $this->getPosts();
        foreach ($posts as $post) {
            $image_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
            $slides .= '<img src="' . $image_src[0] . '" alt="' . $post->post_title . '">';
        }
        return $slides;
    }

    protected function getSlidesFromDir()
    {
        $slides = '';
        $path = 'slider/slides/';
        $dir = realpath(dirname(__FILE__) . '/' . $path) . '/';
        $type = array('png', 'jpg');
        $files = glob($dir . '*.{' . implode(',', $type) . '}', GLOB_BRACE);
        if ($files !== false) {
            foreach ($files as $file) {
                $slides .= '<img src="' . plugin_dir_url(__FILE__) . $path . basename($file) . '" alt="Slider Image">';
            }
        }
        return $slides;
    }

    protected function getSlides()
    {
        if (empty($this->content) === false) {
            $content = do_shortcode($this->content);
            $slides = $this->cleanContent($content);
        } else {
            $slides = $this->getSlidesFromPosts();
            if (empty($slides) === true) {
                $slides = $this->getSlidesFromDir();
            }
        }
        return $slides;
    }

    protected function getSliderOptions()
    {
        $options = '';
        if (isset($this->att['delay']) === true) {
            $options .= 'delay: ' . $this->att['delay'] . ', ';
        }
        if (isset($this->att['interval']) === true) {
            $options .= 'interval: ' . $this->att['interval'] . ', ';
        }
        return $options;
    }

    protected function getSliderStyle()
    {
        $style = '';
        if (isset($this->att['height']) === true) {
            $style .= 'height: ' . $this->att['height'] . 'px; ';
        }
        if (isset($this->att['width']) === true) {
            $style .= 'width: ' . $this->att['width'] . 'px; ';
        }
        return $style;
    }

    protected function getButtonPrev()
    {
        if (isset($this->att['change']) === true && $this->att['change'] === 'false') {
            return '';
        }
        $prevButton = '<div class="prev">';
        if (file_exists(get_template_directory() . '/images/slider/prev.png')) {
            $prevButton .= '<img src="' . get_template_directory_uri() . '/images/slider/prev.png" alt="prev">';
        } elseif (file_exists(plugin_dir_path(__FILE__) . 'slider/images/prev.png')) {
            $prevButton .= '<img src="' . plugin_dir_url(__FILE__) . 'slider/images/prev.png" alt="prev">';
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
        } elseif (file_exists(plugin_dir_path(__FILE__) . 'slider/images/next.png')) {
            $nextButton .= '<img src="' . plugin_dir_url(__FILE__) . 'slider/images/next.png" alt="next">';
        } else {
            $nextButton .= '<div>&#160;</div>';
        }
        $nextButton .= '</div>';
        return $nextButton;
    }

    protected function getPositionBar()
    {
        if (isset($this->att['position']) === true && $this->att['position'] === 'false') {
            return '';
        }
        return '<div class="position"></div>';
    }

    protected function getText()
    {
        if (isset($this->att['text']) === true) {
            $textArray = explode('|', $this->att['text']);
            $textBox = '<div class="text">';
            foreach ($textArray as $key => $sliderText) {
                $textBox .= '<span class="' . ($key == 0 ? 'active' : '') . (empty($sliderText) ? ' hidden' : '') . '">' . $sliderText . '</span>';
            }
            $textBox .= '</div>';
            return $textBox;
        } else {
            $posts = $this->getPosts(false);
            if (empty($posts) === false) {
                $textBox = '<div class="text">';
                $first = true;
                foreach ($posts as $post) {
                    $textBox .= '<span class="' . ($first === true ? 'active' : '') . (empty($post->post_content) ? ' hidden' : '') . '">' . $post->post_content . '</span>';
                    $first = false;
                }
                $textBox .= '</div>';
                return $textBox;
            }
        }
        return '';
    }

    public function render()
    {
        $slides = $this->getSlides();
        $output = '
            <div id="slider-' . self::$id . '" class="slider" style="' . $this->getSliderStyle() . '">
                <div class="slides">
                ' . $slides . '
                </div>
                ' . $this->getButtonPrev() . '
                ' . $this->getButtonNext() . '
                ' . $this->getPositionBar() . '
                ' . $this->getText() . '
            </div>
            <script>
                jQuery(document).ready(function () {
                    jQuery("#slider-' . self::$id . '").slider({' . $this->getSliderOptions() . '});
                });
            </script>
        ';
        if (empty($slides) === false) {
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
    add_theme_support('post-thumbnails');
}

function shortcodeSlider($att = array(), $content = null)
{
    $slider = new Slider($att, $content);
    return $slider->render();
}

add_action('init', 'initSlider');
add_shortcode('slider', 'shortcodeSlider');
