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
 * Version:           1.3.7
 * Author:            Stefan Hüsges
 * Author URI:        http://www.mpcx.net/
 * Copyright:         Stefan Hüsges
 * License:           MIT
 * License URI:       https://raw.githubusercontent.com/tronsha/wp-slider-plugin/master/LICENSE
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'MPCX_SLIDER_VERSION', '1.3.7' );

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
		}

		protected function addShortcode() {
			add_shortcode( 'slider', array( 'MpcxSlider', 'renderSlider' ) );
		}

		protected function enqueueScripts() {
			add_action( 'wp_enqueue_scripts', function () {
				wp_register_style(
					'mpcx-slider',
					plugin_dir_url( __FILE__ ) . 'public/css/slider.min.css',
					array(),
					MPCX_SLIDER_VERSION
				);
				wp_register_script(
					'mpcx-slider',
					plugin_dir_url( __FILE__ ) . 'public/js/slider.min.js',
					array( 'jquery' ),
					MPCX_SLIDER_VERSION
				);
				wp_enqueue_style( 'mpcx-slider' );
				wp_enqueue_script( 'mpcx-slider' );
			} );
		}

		protected function getAttribute( $key ) {
			if ( isset( $this->att[ $key ] ) === true ) {
				return $this->att[ $key ];
			}

			return null;
		}

		protected function setAttribute( $key, $value ) {
			$this->att[ $key ] = $value;
		}

		protected function setAttributes( $att ) {
			if ( is_array( $att ) === true ) {
				foreach ( $att as $key => $value ) {
					$this->setAttribute( $key, $value );
				}
			}
		}

		protected function setContent( $content ) {
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

		protected function getSlidesById( $id = null ) {
			return;
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

		protected function getFilesFromDir( $dir ) {
			$types = array( 'png', 'jpg' );
			if ( defined( 'GLOB_BRACE' ) === true ) {
				$files = glob( $dir . '*.{' . implode( ',', $types ) . '}', GLOB_BRACE );
			} else {
				$files = array();
				foreach ( $types as $type ) {
					$files = array_merge( $files, glob( $dir . '*.' . $type ) );
				}
				sort( $files );
			}

			return $files;
		}

		protected function getSlidesFromTemplateDir() {
			$slides = '';
			$path   = 'images/slider/slides/';
			$dir    = realpath( get_template_directory() . '/' . $path ) . '/';
			$files  = $this->getFilesFromDir( $dir );
			if ( $files !== false ) {
				foreach ( $files as $file ) {
					$slides .= '<img src="' . get_template_directory_uri() . '/' . $path . basename( $file ) . '" alt="Slider Image">';
				}
			}

			return $slides;
		}

		protected function getSlidesFromPluginDir() {
			$slides = '';
			$path   = 'public/slides/';
			$dir    = realpath( __DIR__ . '/' . $path ) . '/';
			$files  = $this->getFilesFromDir( $dir );
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
				$id = $this->getAttribute( 'id' );
				if ( $id !== null ) {
					$slides = $this->getSlidesById( $id );
				}
				if ( empty( $slides ) === true ) {
					$slides = $this->getSlidesFromPosts();
				}
				if ( empty( $slides ) === true ) {
					$slides = $this->getSlidesFromTemplateDir();
				}
				if ( empty( $slides ) === true ) {
					$slides = $this->getSlidesFromPluginDir();
				}
			}
			$slides = $this->addLinks($slides);

			return $slides;
		}

		protected function getSliderOptions() {
			$options = '';
			$delay   = $this->getAttribute( 'delay' );
			if ( $delay !== null ) {
				$options .= 'delay: ' . $delay . ', ';
			}
			$interval = $this->getAttribute( 'interval' );
			if ( $interval !== null ) {
				$options .= 'interval: ' . $interval . ', ';
			}
			if ( $this->getAttribute( 'random' ) === 'true' ) {
				$options .= 'random: true, ';
			}
			if ( $this->getAttribute( 'resize' ) === 'true' ) {
				$options .= 'resize: true, ';
			}

			return $options;
		}

		protected function getSliderStyle() {
			$style  = '';
			$height = $this->getAttribute( 'height' );
			if ( $height !== null ) {
				$style .= 'height: ' . $height . 'px; ';
			}
			$width = $this->getAttribute( 'width' );
			if ( $width !== null ) {
				$style .= 'width: ' . $width . 'px; ';
			}

			return $style;
		}

		protected function loadFontAwesome() {
			wp_register_style(
				'fontawesome',
				plugin_dir_url( __FILE__ ) . 'public/css/font-awesome.min.css',
				array(),
				'4.7.0'
			);
			wp_enqueue_style( 'fontawesome' );
		}

		protected function getButtonPrev() {
			$change = $this->getAttribute( 'change' );
			if ( $change === 'true' || $change === 'fa' ) {
				$prevButton = '<div class="prev">';
				if ( $change === 'fa' ) {
					$this->loadFontAwesome();
					$prev = $this->getAttribute( 'prev' );
					$prevButton .= '<i class="fa ' . ( $prev !== null ? $prev : 'fa-chevron-left' ) . '"></i>';
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
			$change = $this->getAttribute( 'change' );
			if ( $change === 'true' || $change === 'fa' ) {
				$nextButton = '<div class="next">';
				if ( $change === 'fa' ) {
					$this->loadFontAwesome();
					$next = $this->getAttribute( 'next' );
					$nextButton .= '<i class="fa ' . ( $next !== null ? $next : 'fa-chevron-right' ) . '"></i>';
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
			if ( $this->getAttribute( 'position' ) === 'true' ) {
				return '<div class="position"></div>';
			}

			return '';
		}

		protected function getText() {
			$text = $this->getAttribute( 'text' );
			if ( $text !== null ) {
				$textArray = explode( '|', $text );
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

		protected function getLinks() {
			$links = $this->getAttribute( 'link' );
			if ( $links !== null ) {
				if ( strpos( $links, '|' ) !== false ) {
					$links = explode( '|', $links );
				}
			}

			return $links;
		}

		protected function addLinks( $slides ) {
			$links = $this->getLinks();
			preg_match_all( '/<img[^<>]+>/i', $slides, $matches );
			$slides = '';
			foreach ( $matches[0] as $key => $image ) {
				$url = is_array( $links ) ? $links[ $key ] : $links;
				if ( empty( $url ) === false ) {
					$attribute = ' data-href="' . $url . '"';
					$slides .= str_replace( '>', $attribute . '>', $image );
				} else {
					$slides .= $image;
				}
			}

			return $slides;
		}

		protected function getHtml() {
			$slides = $this->getSlides();
			$output = '
            <div id="slider-' . self::$id . '" class="slider no-js" style="' . $this->getSliderStyle() . '">
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
			$slider->setAttributes( $att );
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
