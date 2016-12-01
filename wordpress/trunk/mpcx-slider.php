<?php
/**
 * @link              https://github.com/tronsha/wp-slider-plugin
 * @since             1.0.0
 * @package           wp-slider-plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Slider
 * Plugin URI:        https://github.com/tronsha/wp-slider-plugin
 * Description:       A responsive Slider Plugin.
 * Version:           1.4.0
 * Author:            Stefan Hüsges
 * Author URI:        http://www.mpcx.net/
 * Copyright:         Stefan Hüsges
 * Text Domain:       mpcx-slider
 * Domain Path:       /languages/
 * License:           MIT
 * License URI:       https://raw.githubusercontent.com/tronsha/wp-slider-plugin/master/LICENSE
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'MPCX_SLIDER_VERSION', '1.4.0' );

load_plugin_textdomain( 'mpcx-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/localization' );

register_activation_hook(
	__FILE__,
	function () {
		add_option( 'mpcx_slider', json_encode( array( 0 => array( 'version' => MPCX_SLIDER_VERSION ) ) ) );
	}
);

if ( ! class_exists( 'MpcxSlider' ) ) {

	class MpcxSlider {

		private static $id = 0;
		private static $instance = null;
		private static $posts = null;
		private $att = array();
		private $content = null;

		protected function __construct() {
			add_theme_support( 'post-thumbnails' );
			if ( ! is_admin() ) {
				$this->enqueueScripts();
				$this->addShortcode();
			}
			if ( is_admin() ) {
				$this->addAdminAction();
			}
		}

		protected function addAdminAction () {
			add_action(
				'admin_menu',
				function () {
					add_menu_page(
						'Slider',
						'Slider',
						'manage_options',
						'slider',
						function () {
							include plugin_dir_path( __FILE__ ) . 'admin/options.php';
						},
						'dashicons-clipboard',
						20
					);
				}
			);
		}

		protected function addShortcode() {
			add_shortcode( 'slider', array( 'MpcxSlider', 'renderSlider' ) );
		}

		protected function enqueueScripts() {
			wp_register_style(
				'mpcx-slider',
				plugin_dir_url( __FILE__ ) . 'public/css/slider.min.css',
				array(),
				$this->getVersion()
			);
			wp_register_script(
				'mpcx-slider',
				plugin_dir_url( __FILE__ ) . 'public/js/slider.min.js',
				array( 'jquery' ),
				$this->getVersion()
			);
			wp_enqueue_style( 'mpcx-slider' );
			wp_enqueue_script( 'mpcx-slider' );
		}

		protected function getVersion() {
			return MPCX_SLIDER_VERSION;
		}

		protected function setAttribute($att) {
			$this->att = $att;
		}

		protected function setContent($content) {
			$this->content = $content;
		}

		protected function cleanContent( $content ) {
			preg_match_all( '/<img[^>]+>/i', $content, $matches );
			$content = '';
			foreach ( $matches[0] as $image ) {
				$content .= $image;
			}
			return $content;
		}

		protected function getPosts( $x = true ) {
			if ( self::$posts === null && $x === true ) {
				self::$posts = get_posts( array(
					'offset'         => 0,
					'category_name'  => 'slides',
					'posts_per_page' => - 1,
					'orderby'        => 'ID',
					'order'          => 'ASC',
				) );
			}
			return self::$posts;
		}

		protected function getSlidesFromPosts() {
			$slides = '';
			$posts  = $this->getPosts();
			foreach ( $posts as $post ) {
				$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				$slides .= '<img src="' . $image_src[0] . '" alt="' . $post->post_title . '">';
			}
			return $slides;
		}

		protected function getSlidesFromDir() {
			$slides = '';
			$path   = 'public/slides/';
			$dir    = realpath( __DIR__ . '/' . $path ) . '/';
			$types  = array( 'png', 'jpg' );
			if ( defined( 'GLOB_BRACE' ) === true ) {
				$files = glob( $dir . '*.{' . implode( ',', $types ) . '}', GLOB_BRACE );
			} else {
				$files = array();
				foreach ( $types as $type ) {
					$files = array_merge( $files, glob( $dir . '*.' . $type ) );
				}
				sort( $files );
			}
			if ( $files !== false ) {
				foreach ( $files as $file ) {
					$slides .= '<img src="' . plugin_dir_url( __FILE__ ) . $path . basename( $file ) . '" alt="Slider Image">';
				}
			}
			return $slides;
		}

		protected function getSlides() {
			if ( empty( $this->content ) === false ) {
				$content = do_shortcode( $this->content );
				$slides  = $this->cleanContent( $content );
			} else {
				$slides = $this->getSlidesFromPosts();
				if ( empty( $slides ) === true ) {
					$slides = $this->getSlidesFromDir();
				}
			}
			return $slides;
		}

		protected function getSliderOptions() {
			$options = '';
			if ( isset( $this->att['delay'] ) === true ) {
				$options .= 'delay: ' . $this->att['delay'] . ', ';
			}
			if ( isset( $this->att['interval'] ) === true ) {
				$options .= 'interval: ' . $this->att['interval'] . ', ';
			}
			if ( isset( $this->att['random'] ) === true && $this->att['random'] === 'true' ) {
				$options .= 'random: true, ';
			}
			return $options;
		}

		protected function getSliderStyle() {
			$style = '';
			if ( isset( $this->att['height'] ) === true ) {
				$style .= 'height: ' . $this->att['height'] . 'px; ';
			}
			if ( isset( $this->att['width'] ) === true ) {
				$style .= 'width: ' . $this->att['width'] . 'px; ';
			}
			return $style;
		}

		protected function loadFontAwesome() {
			wp_register_style(
				'fontawesome',
				plugin_dir_url( __FILE__ ) . 'public/css/font-awesome.min.css',
				array(),
				'4.6.1'
			);
			wp_enqueue_style( 'fontawesome' );
		}

		protected function getButtonPrev() {
			if ( isset( $this->att['change'] ) === true && ( $this->att['change'] === 'true' || $this->att['change'] === 'fa' ) ) {
				$prevButton = '<div class="prev">';
				if ( $this->att['change'] === 'fa' ) {
					$this->loadFontAwesome();
					$prevButton .= '<i class="fa ' . ( isset( $this->att['prev'] ) ? $this->att['prev'] : 'fa-chevron-left' ) . '"></i>';
				} elseif ( file_exists( get_template_directory() . '/images/slider/prev.png' ) ) {
					$prevButton .= '<img src="' . get_template_directory_uri() . '/images/slider/prev.png" alt="prev">';
				} elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'public/images/prev.png' ) ) {
					$prevButton .= '<img src="' . plugin_dir_url( __FILE__ ) . 'public/images/prev.png" alt="prev">';
				} else {
					$prevButton .= '<div>&#160;</div>';
				}
				$prevButton .= '</div>';

				return $prevButton;
			}
			return '';
		}

		protected function getButtonNext() {
			if ( isset( $this->att['change'] ) === true && ( $this->att['change'] === 'true' || $this->att['change'] === 'fa' ) ) {
				$nextButton = '<div class="next">';
				if ( $this->att['change'] === 'fa' ) {
					$this->loadFontAwesome();
					$nextButton .= '<i class="fa ' . ( isset( $this->att['next'] ) ? $this->att['next'] : 'fa-chevron-right' ) . '"></i>';
				} elseif ( file_exists( get_template_directory() . '/images/slider/next.png' ) ) {
					$nextButton .= '<img src="' . get_template_directory_uri() . '/images/slider/next.png" alt="next">';
				} elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'public/images/next.png' ) ) {
					$nextButton .= '<img src="' . plugin_dir_url( __FILE__ ) . 'public/images/next.png" alt="next">';
				} else {
					$nextButton .= '<div>&#160;</div>';
				}
				$nextButton .= '</div>';

				return $nextButton;
			}
			return '';
		}

		protected function getPositionBar() {
			if ( isset( $this->att['position'] ) === true && $this->att['position'] === 'true' ) {
				return '<div class="position"></div>';
			}
			return '';
		}

		protected function getText() {
			if ( isset( $this->att['text'] ) === true ) {
				$textArray = explode( '|', $this->att['text'] );
				$textBox   = '<div class="text">';
				foreach ( $textArray as $key => $sliderText ) {
					$textBox .= '<span class="' . ( $key == 0 ? 'active' : '' ) . ( empty( $sliderText ) ? ' hidden' : '' ) . '">' . $sliderText . '</span>';
				}
				$textBox .= '</div>';
				return $textBox;
			} else {
				$posts = $this->getPosts( false );
				if ( empty( $posts ) === false ) {
					$textBox = '<div class="text">';
					$first   = true;
					foreach ( $posts as $post ) {
						$textBox .= '<span class="' . ( $first === true ? 'active' : '' ) . ( empty( $post->post_content ) ? ' hidden' : '' ) . '">' . $post->post_content . '</span>';
						$first = false;
					}
					$textBox .= '</div>';
					return $textBox;
				}
			}
			return '';
		}

		protected function getHtml() {
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
			if ( empty( $slides ) === false ) {
				return $output;
			}

			return '';
		}

		public static function renderSlider( $att = array(), $content = null ) {
			self::$id ++;
			$slider = self::getInstance();
			$slider->setAttribute( $att );
			$slider->setContent( $content );
			return $slider->getHtml();
		}

		public static function getInstance() {
			if ( self::$instance === null ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

	}

}

MpcxSlider::getInstance();
