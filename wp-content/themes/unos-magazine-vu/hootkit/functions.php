<?php
/**
 * This file contains functions and hooks for styling Hootkit plugin
 *   Hootkit is a free plugin released under GPL license and hosted on wordpress.org.
 *   It is recommended to the user via wp-admin using TGMPA class
 *
 * This file is loaded at 'after_setup_theme' action @priority 10 ONLY IF hootkit plugin is active
 *
 * @package    Unos Magazine Vu
 * @subpackage HootKit
 */

// Add theme's hootkit styles.
// Changing priority to >11 has added benefit of loading child theme's stylesheet before hootkit style.
// This is preferred in case of pre-built child themes where we want child stylesheet to come before
// dynamic css (not after like in the case of user blank child themes purely used for customizations)
add_action( 'wp_enqueue_scripts', 'unosmvu_enqueue_hootkit', 15 );

// Set dynamic css handle to child theme's hootkit
// if HK active : earlier set to hootkit@parent @priority 5; set to child stylesheet @priority 7
// Dynamic is hooked to child stylesheet in main functions file. We modify it here for when HootKit is
// active to load dynamic after hootkit stylesheet (which is loaded after child stylesheet - see above)
add_filter( 'hoot_style_builder_inline_style_handle', 'unosmvu_dynamic_css_hootkit_handle', 9 );

// Add dynamic CSS for hootkit
// Priority@12: 10-> base hootkit lite/prim
add_action( 'hoot_dynamic_cssrules', 'unosmvu_hootkit_dynamic_cssrules', 12 );

/**
 * Enqueue Scripts and Styles
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'unosmvu_enqueue_hootkit' ) ) :
function unosmvu_enqueue_hootkit() {

	/* 'unos-hootkit' is loaded using 'hoot_locate_style' which loads child theme location. Hence deregister it and load files again */
	wp_deregister_style( 'unos-hootkit' );
	/* Load Hootkit Style - Add dependency so that hotkit is loaded after */
	if ( file_exists( hoot_data()->template_dir . 'hootkit/hootkit.css' ) )
	wp_enqueue_style( 'unos-hootkit', hoot_data()->template_uri . 'hootkit/hootkit.css', array( 'hoot-style' ), hoot_data()->template_version );
	if ( file_exists( hoot_data()->child_dir . 'hootkit/hootkit.css' ) )
	wp_enqueue_style( 'unosmvu-hootkit', hoot_data()->child_uri . 'hootkit/hootkit.css', array( 'hoot-style', 'unos-hootkit' ), hoot_data()->childtheme_version );

}
endif;

/**
 * Set dynamic css handle to hootkit
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'unosmvu_dynamic_css_hootkit_handle' ) ) :
function unosmvu_dynamic_css_hootkit_handle( $handle ) {
	return 'unosmvu-hootkit';
}
endif;

/**
 * Custom CSS built from user theme options for hootkit features
 *
 * @since 1.0
 * @access public
 */
if ( !function_exists( 'unosmvu_hootkit_dynamic_cssrules' ) ) :
function unosmvu_hootkit_dynamic_cssrules() {

	global $hoot_style_builder;

	// Get user based style values
	$styles = unos_user_style(); // echo '<!-- '; print_r($styles); echo ' -->';
	extract( $styles );

	$hoot_style_builder->remove( array(
		'.social-icons-icon',
		'#topbar .social-icons-icon, #page-wrapper .social-icons-icon',
	) );

	/*** Add Dynamic CSS ***/

	hoot_add_css_rule( array(
						'selector'  => '.topbar .social-icons-widget',
						'property'  => 'background',
						'value'     => $accent_font,
						'idtag'     => 'accent_font'
					) );

}
endif;

/**
 * Modify Posts List default style
 *
 * @since 1.0
 * @param array $settings
 * @return string
 */
function unosmvu_posts_list_widget_settings( $settings ) {
	if ( isset( $settings['form_options']['firstpost']['fields']['size']['std'] ) )
		$settings['form_options']['firstpost']['fields']['size']['std'] = 'small';
	return $settings;
}
add_filter( 'hootkit_posts_list_widget_settings', 'unosmvu_posts_list_widget_settings', 5 );

/**
 * Modify Post Grid default style
 *
 * @since 1.0
 * @param array $settings
 * @return string
 */
function unosmvu_post_grid_widget_settings( $settings ) {
	if ( isset( $settings['form_options']['columns']['std'] ) )
		$settings['form_options']['columns']['std'] = '4';
	if ( isset( $settings['form_options']['count']['desc'] ) )
		$settings['form_options']['count']['desc'] = __( 'Default: 5 (posts without a featured image are skipped)', 'unos-magazine-vu' );
	if ( isset( $settings['form_options']['unitheight']['desc'] ) )
		$settings['form_options']['unitheight']['desc'] = __( 'Default: 205 (in pixels)', 'unos-magazine-vu' );
	return $settings;
}
add_filter( 'hootkit_post_grid_widget_settings', 'unosmvu_post_grid_widget_settings', 5 );

/**
 * Modify Post Grid Query Args
 *
 * @since 1.0
 * @param array $query_args
 * @param array $instance
 * @return string
 */
function unosmvu_post_grid_query( $query_args, $instance ) {
	$count = ( isset( $instance['count'] ) ) ? $instance['count'] : 5;
	$count = intval( $count );
	$query_args['posts_per_page'] = ( empty( $count ) ) ? 5 : $count;
	return $query_args;
}
add_filter( 'hootkit_post_grid_query', 'unosmvu_post_grid_query', 5, 2 );

/**
 * Modify Ticker and Ticker Posts default style
 *
 * @since 1.0
 * @param array $settings
 * @return string
 */
function unosmvu_ticker_posts_widget_settings( $settings ) {
	if ( isset( $settings['form_options']['background'] ) )
		$settings['form_options']['background']['std'] = '#f1f1f1';
	if ( isset( $settings['form_options']['fontcolor'] ) )
		$settings['form_options']['fontcolor']['std'] = '#ff4530';
	return $settings;
}
add_filter( 'hootkit_ticker_widget_settings', 'unosmvu_ticker_posts_widget_settings', 5 );
add_filter( 'hootkit_ticker_posts_widget_settings', 'unosmvu_ticker_posts_widget_settings', 5 );

/**
 * Filter Ticker and Ticker Posts display Title markup
 *
 * @since 1.0
 * @param array $settings
 * @return string
 */
function unosmvu_hootkit_widget_title( $display, $title, $context ) {
	if ( $context == 'ticker-posts' || $context == 'ticker' )
		$display = '<div class="ticker-title accent-typo">' . $title . '</div>';
	return $display;
}
add_filter( 'hootkit_widget_ticker_title', 'unosmvu_hootkit_widget_title', 5, 3 );