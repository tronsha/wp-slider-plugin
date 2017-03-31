<?php
/**
 * @link    https://github.com/tronsha/wp-slider-plugin
 * @package wp-slider-plugin
 */

define( 'MPCX_SLIDER_UPDATE_VERSION', '1.3.9' );
$data = get_option( 'mpcx_slider' );
if ( true === isset( $data['version'] ) && version_compare( $data['version'], MPCX_SLIDER_UPDATE_VERSION, '<' ) ) {
	$data['version'] = MPCX_SLIDER_UPDATE_VERSION;
	update_option( 'mpcx_slider', $data );
}
