<?php
/**
 *                  _   _             _   
 *  __      ___ __ | | | | ___   ___ | |_ 
 *  \ \ /\ / / '_ \| |_| |/ _ \ / _ \| __|
 *   \ V  V /| |_) |  _  | (_) | (_) | |_ 
 *    \_/\_/ | .__/|_| |_|\___/ \___/ \__|
 *           |_|                          
 *
 * :: Theme's main functions file ::::::::::::
 * :: Initialize and setup the theme :::::::::
 *
 * Hooks, Actions and Filters are used throughout this theme. You should be able to do most of your
 * customizations without touching the main code. For more information on hooks, actions, and filters
 * @see http://codex.wordpress.org/Plugin_API
 *
 * @package    Unos Magazine Vu
 */


/* === Theme Setup === */


/**
 * Theme Setup
 *
 * @since 1.0
 * @access public
 * @return void
 */
function unosmvu_theme_setup(){

	// Load theme's Hootkit functions if plugin is active
	if ( class_exists( 'HootKit' ) && file_exists( hoot_data()->child_dir . 'hootkit/functions.php' ) )
		include_once( hoot_data()->child_dir . 'hootkit/functions.php' );

	// Load the about page.
	if ( apply_filters( 'unosmvu_load_about', file_exists( hoot_data()->child_dir . 'admin/about.php' ) ) )
		require_once( hoot_data()->child_dir . 'admin/about.php' );

}
add_action( 'after_setup_theme', 'unosmvu_theme_setup', 10 );

/**
 * Set dynamic css handle to child stylesheet
 * if HK active : earlier set to hootkit@parent @priority 5; set to hootkit@child @priority 9
 * This is preferred in case of pre-built child themes where we want child stylesheet to come before
 * dynamic css (not after like in the case of user blank child themes purely used for customizations)
 *
 * @since 1.0
 * @access public
 * @return string
 */
if ( !function_exists( 'unosmvu_dynamic_css_child_handle' ) ) :
function unosmvu_dynamic_css_child_handle( $handle ) {
	return 'hoot-child-style';
}
endif;
add_filter( 'hoot_style_builder_inline_style_handle', 'unosmvu_dynamic_css_child_handle', 7 );

/**
 * Unload Template's About Page
 *
 * @since 1.0
 * @access public
 * @return bool
 */
function unosmvu_unload_template_about( $load ) {
	return false;
}
add_filter( 'unos_load_about', 'unosmvu_unload_template_about', 5 );

/**
 * Add child theme's hook for unloading About page
 *
 * @since 1.0
 * @access public
 * @return array
 */
function unosmvu_unload_about( $hooks ) {
	if ( is_array( $hooks ) )
		$hooks[] = 'unosmvu_load_about';
	return $hooks;
}
add_filter( 'unos_unload_upsell', 'unosmvu_unload_about', 5 );

/**
 * Modify custom-header
 * Priority@5 to come before 10 used by unos for adding support
 *    @ref wp-includes/theme.php #2440
 *    // Merge in data from previous add_theme_support() calls.
 *    // The first value registered wins. (A child theme is set up first.)
 * For remove_theme_support, use priority@15
 *
 * @since 1.0
 * @access public
 * @return void
 */
function unosmvu_custom_header() {
	add_theme_support( 'custom-header', array(
		'width' => 1440,
		'height' => 500,
		'flex-height' => true,
		'flex-width' => true,
		'default-image' => '',
		'header-text' => false
	) );
}
add_filter( 'after_setup_theme', 'unosmvu_custom_header', 5 );


/* === Attr === */


/**
 * Topbar meta attributes.
 * Priority@10: 7-> base lite ; 9-> base prim
 *
 * @since 1.0
 * @param array $attr
 * @param string $context
 * @return array
 */
function unosmvu_attr_topbar( $attr, $context ) {
	if ( !empty( $attr['classes'] ) )
		$attr['classes'] = str_replace( 'social-icons-invert', '', $attr['classes'] );
	return $attr;
}
add_filter( 'hoot_attr_topbar', 'unosmvu_attr_topbar', 10, 2 );

/**
 * Loop meta attributes.
 * Priority@10: 7-> base lite ; 9-> base prim
 *
 * @since 1.0
 * @param array $attr
 * @param string $context
 * @return array
 */
function unosmvu_attr_premium_loop_meta_wrap( $attr, $context ) {
	$attr['class'] = ( empty( $attr['class'] ) ) ? '' : $attr['class'];

	/* Overwrite all and apply background class for both */
	$attr['class'] = str_replace( array( 'loop-meta-wrap pageheader-bg-default', 'loop-meta-wrap pageheader-bg-stretch', 'loop-meta-wrap pageheader-bg-incontent', 'loop-meta-wrap pageheader-bg-both', 'loop-meta-wrap pageheader-bg-none', ), '', $attr['class'] );
	$attr['class'] .= ' loop-meta-wrap pageheader-bg-both';

	return $attr;
}
add_filter( 'hoot_attr_loop-meta-wrap', 'unosmvu_attr_premium_loop_meta_wrap', 10, 2 );


/* === Dynamic CSS === */


/**
 * Custom CSS built from user theme options
 * For proper sanitization, always use functions from library/sanitization.php
 * Priority@6: 5-> base lite ; 7-> base prim prepare (rules removed) ;
 *             9-> base prim ; 10-> base hootkit lite/prim
 *
 * @since 1.0
 * @access public
 */
function unosmvu_dynamic_cssrules() {

	global $hoot_style_builder;

	// Get user based style values
	$styles = unos_user_style(); // echo '<!-- '; print_r($styles); echo ' -->';
	extract( $styles );

	$hoot_style_builder->remove( array(
		'.menu-items li.current-menu-item, .menu-items li.current-menu-ancestor, .menu-items li:hover',
		'.menu-items li.current-menu-item > a, .menu-items li.current-menu-ancestor > a, .menu-items li:hover > a',
	) );
	hoot_add_css_rule( array(
						'selector'  => '.menu-items > li.current-menu-item:after, .menu-items > li.current-menu-ancestor:after, .menu-items > li:hover:after',
						'property'  => 'border-color',
						'value'     => $accent_color,
						'idtag'     => 'accent_color'
					) );
	hoot_add_css_rule( array(
						'selector'  => '.menu-items ul li.current-menu-item, .menu-items ul li.current-menu-ancestor, .menu-items ul li:hover',
						'property'  => 'background',
						'value'     => $accent_font,
						'idtag'     => 'accent_font'
					) );
	hoot_add_css_rule( array(
						'selector'  => '.menu-items ul li.current-menu-item > a, .menu-items ul li.current-menu-ancestor > a, .menu-items ul li:hover > a',
						'property'  => 'color',
						'value'     => $accent_color,
						'idtag'     => 'accent_color'
					) );

	$halfwidgetmargin = false;
	if ( intval( $widgetmargin ) )
		$halfwidgetmargin = ( intval( $widgetmargin ) / 2 > 25 ) ? ( intval( $widgetmargin ) / 2 ) . 'px' : '25px';
	if ( $halfwidgetmargin )
		hoot_add_css_rule( array(
						'selector'  => '.main > .main-content-grid:first-child' . ',' . '.content-frontpage > .frontpage-area-boxed:first-child',
						'property'  => 'margin-top',
						'value'     => $halfwidgetmargin,
					) );

}
add_action( 'hoot_dynamic_cssrules', 'unosmvu_dynamic_cssrules', 6 );


/* === Customizer Options === */


/**
 * Update theme defaults
 * Prim @priority 5
 * Prim child @priority 9
 *
 * @since 1.0
 * @access public
 * @return array
 */
if ( !function_exists( 'unosmvu_default_style' ) ) :
function unosmvu_default_style( $defaults ){
	$defaults = array_merge( $defaults, array(
		'accent_color'         => '#ff4530',
		'accent_font'          => '#ffffff',
		'widgetmargin'         => 35,
	) );
	return $defaults;
}
endif;
add_filter( 'unos_default_style', 'unosmvu_default_style', 7 );

/**
 * Add Options (settings, sections and panels) to Hoot_Customize class options object
 *
 * Parent Lite/Prim add options using 'init' hook both at priority 0. Currently there is no way
 * to hook in between them. Hence we hook in later at 5 to be able to remove options if needed.
 * The only drawback is that options involving widget areas cannot be modified/created/removed as
 * those have already been used during widgets_init hooked into init at priority 1. For adding options
 * involving widget areas, we can alterntely hook into 'after_setup_theme' before lite/prim options
 * are built. Modifying/removing such options from lite/prim still needs testing.
 *
 * @since 1.0
 * @access public
 */
if ( !function_exists( 'unosmvu_add_customizer_options' ) ) :
function unosmvu_add_customizer_options() {

	$hoot_customize = Hoot_Customize::get_instance();

	// Modify Options
	$hoot_customize->remove_settings( array( 'logo_tagline_size', 'logo_tagline_style' ) );
	$hoot_customize->remove_settings( 'pageheader_background_location' );

	// Define Options
	$options = array(
		'settings' => array(),
		'sections' => array(),
		'panels' => array(),
	);

	// Add Options
	$hoot_customize->add_options( array(
		'settings' => $options['settings'],
		'sections' => $options['sections'],
		'panels' => $options['panels'],
		) );

}
endif;
add_action( 'init', 'unosmvu_add_customizer_options', 5 );

/**
 * Modify Lite customizer options
 * Prim hooks in later at priority 9
 *
 * @since 1.0
 * @access public
 */
function unosmvu_modify_customizer_options( $options ){
	if ( isset( $options['settings']['widgetmargin'] ) )
		$options['settings']['widgetmargin']['input_attrs']['placeholder'] = esc_html__( 'default: 35', 'unos-magazine-vu' );
	if ( isset( $options['settings']['menu_location'] ) )
		$options['settings']['menu_location']['default'] = 'bottom';
	if ( isset( $options['settings']['logo_side'] ) )
		$options['settings']['logo_side']['default'] = 'widget-area';
	if ( isset( $options['settings']['fullwidth_menu_align'] ) )
		$options['settings']['fullwidth_menu_align']['default'] = 'left';
	if ( isset( $options['settings']['logo_size'] ) )
		$options['settings']['logo_size']['default'] = 'medium';
	if ( isset( $options['settings']['logo_custom']['choices'] ) )
		$options['settings']['logo_custom']['choices'] = array(
			'line1' => esc_html__( 'Line 1 (Left Block)', 'unos-magazine-vu' ),
			'line2' => esc_html__( 'Line 2 (Left Block)', 'unos-magazine-vu' ),
			'line3' => esc_html__( 'Line 1 (Right Block)', 'unos-magazine-vu' ),
			'line4' => esc_html__( 'Line 2 (Right Block)', 'unos-magazine-vu' ),
		);
	if ( isset( $options['settings']['logo_custom'] ) )
		$options['settings']['logo_custom']['default'] = array(
			'line1'  => array( 'text' => wp_kses_post( __( '<b>Hoot</b> <em>Unos</em>', 'unos-magazine-vu' ) ), 'size' => '16px', 'font' => 'standard' ),
			'line2'  => array( 'text' => esc_html__( 'MAGAZINE', 'unos-magazine-vu' ), 'size' => '28px' ),
			'line3'  => array( 'text' => esc_html__( 'VU', 'unos-magazine-vu' ), 'size' => '45px', 'accentbg' => 1 ),
			'line4'  => array( 'sortitem_hide' => 1, ),
		);
	if ( isset( $options['settings']['logo_custom']['options'] ) ) {
		foreach ( $options['settings']['logo_custom']['options'] as $linekey => $linevalue ) {
			$options['settings']['logo_custom']['options'][$linekey] = array_merge( $options['settings']['logo_custom']['options'][$linekey], array(
				'accentbg' => array(
					'label'       => esc_html__( 'Accent Background', 'unos-magazine-vu' ),
					'type'        => 'checkbox',
				),
			) );
		}
	}
	if ( isset( $options['settings']['logo_fontface_style'] ) )
		$options['settings']['logo_fontface_style']['default'] = 'standard';
	return $options;
}
add_filter( 'unos_customizer_options', 'unosmvu_modify_customizer_options', 7 );

/**
 * Modify Customizer Link Section
 *
 * @since 1.0
 * @access public
 */
function unosmvu_customizer_option_linksection( $lcontent ){
	if ( is_array( $lcontent ) ) {
		if ( !empty( $lcontent['demo'] ) )
			$lcontent['demo'] = str_replace( 'demo.wphoot.com/unos', 'demo.wphoot.com/unos-magazine-vu', $lcontent['demo'] );
		if ( !empty( $lcontent['install'] ) )
			$lcontent['install'] = str_replace( 'wphoot.com/support/unos', 'wphoot.com/support/unos-magazine-vu', $lcontent['install'] );
		if ( !empty( $lcontent['rateus'] ) )
			$lcontent['rateus'] = str_replace( 'wordpress.org/support/theme/unos', 'wordpress.org/support/theme/unos-magazine-vu', $lcontent['rateus'] );
	}
	return $lcontent;
}
add_filter( 'unos_customizer_option_linksection', 'unosmvu_customizer_option_linksection' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since 1.0
 * @return void
 */
function unosmvu_customize_preview_js() {
	if ( file_exists( hoot_data()->child_dir . 'admin/customize-preview.js' ) )
		wp_enqueue_script( 'unosmvu-customize-preview', hoot_data()->child_uri . 'admin/customize-preview.js', array( 'hoot-customize-preview', 'customize-preview' ), hoot_data()->childtheme_version, true );
}
add_action( 'customize_preview_init', 'unosmvu_customize_preview_js', 12 );

/**
 * Add style tag to support dynamic css via postMessage script in customizer preview
 *
 * @since 2.7
 * @access public
 */

function unosmvu_customize_dynamic_selectors( $settings ) {
	if ( !is_array( $settings ) ) return $settings;
	$hootpload = ( function_exists( 'hoot_lib_premium_core' ) ) ? 1 : '';

	$modify = array(
		'box_background_color' => array(
			'color'			=> array( 'remove' => array(), 'add' => array(), ),
			'background'	=> array( 'remove' => array(), 'add' => array(), ),
		),
		'accent_color' => array(
			'color' => array(
				'remove' => array(
				),
				'add' => array(
					'.menu-items ul li.current-menu-item > a, .menu-items ul li.current-menu-ancestor > a, .menu-items ul li:hover > a',
				),
			),
			'background' => array(
				'remove' => array(
					'.menu-items li.current-menu-item, .menu-items li.current-menu-ancestor, .menu-items li:hover',
					'.social-icons-icon',
				),
			),
			'border-color' => array(
				'add' => array(
					'.menu-items > li.current-menu-item:after, .menu-items > li.current-menu-ancestor:after, .menu-items > li:hover:after',
				),
			),
		),
		'accent_font' => array(
			'color' => array(
				'remove' => array(
					'.menu-items li.current-menu-item > a, .menu-items li.current-menu-ancestor > a, .menu-items li:hover > a',
					'#topbar .social-icons-icon, #page-wrapper .social-icons-icon',
				),
			),
			'background' => array(
				'remove' => array(
				),
				'add' => array(
					'.menu-items ul li.current-menu-item, .menu-items ul li.current-menu-ancestor, .menu-items ul li:hover',
					'.topbar .social-icons-widget',
				),
			),
		),
	);

	foreach ( $modify as $id => $props ) {
		foreach ( $props as $prop => $ops ) {
			foreach ( $ops as $op => $values ) {
				if ( $op == 'remove' ) {
					foreach ( $values as $val ) {
						$akey = array_search( $val, $settings[$id][$prop] );
						if ( $akey !== false ) unset( $settings[$id][$prop][$akey] );
					}
				} elseif ( $op == 'add' ) {
					foreach ( $values as $val ) {
						$settings[$id][$prop][] = $val;
					}
				}
			}
		}
	}

	return $settings;
}
add_filter( 'hoot_customize_dynamic_selectors', 'unosmvu_customize_dynamic_selectors', 5 );


/* === Misc === */


/**
 * Custom Logo Template Function
 *
 * @since 1.0
 * @access public
 * @return string
 */
function unosmvu_get_custom_text_logo( $title, $logo_custom ){
	if ( is_array( $logo_custom ) && !empty( $logo_custom ) ) {
		$title = '';
		$dcount = $lcount = 1;
		$display = array_fill( 1, 4, false );
		foreach ( $logo_custom as $logo_custom_line ) {
			$display[ $dcount ] = ( !$logo_custom_line['sortitem_hide'] && !empty( $logo_custom_line['text'] ) );
			$dcount++;
		}
		foreach ( $logo_custom as $logo_custom_line ) {
			if ( $lcount == 1 && ( $display[1] || $display[2] ) ) $title .= '<span class="customblogname-left">';
			if ( $lcount == 3 && ( $display[3] || $display[4] ) ) $title .= '<span class="customblogname-right">';
			if ( $display[ $lcount ] ) {
				$line_class = 'site-title-line site-title-line' . $lcount;
				$line_class .= ( !empty( $logo_custom_line['font'] ) && $logo_custom_line['font'] == 'standard' ) ? ' site-title-body-font' : '';
				$line_class .= ( !empty( $logo_custom_line['font'] ) && $logo_custom_line['font'] == 'heading2' ) ? ' site-title-heading-font' : '';
				$line_class .= ( !empty( $logo_custom_line['accentbg'] ) ) ? ' accent-typo' : '';
				$title .= '<span class="' . $line_class . '">' . wp_kses_decode_entities( $logo_custom_line['text'] ) . '</span>';
			}
			if ( $lcount == 2 && ( $display[1] || $display[2] ) ) $title .= '</span>';
			if ( $lcount == 4 && ( $display[3] || $display[4] ) ) $title .= '</span>';
			$lcount++;
		}
	}
	return $title;
}
add_filter( 'unos_get_custom_text_logo', 'unosmvu_get_custom_text_logo', 5, 2 );

/**
 * Modify the image thumbnail size for mosaic 3/4 archive styles
 *
 * @since 1.0
 * @access public
 * @return string
 */
function unosmvu_archive_imgsize( $size, $context='' ){
	if ( $context == 'mosaic3' || $context == 'mosaic4' ) $size = 'hoot-preview-thumb';
	return $size;
}
if ( function_exists( 'hoot_lib_premium_core' ) ){
	// Update Image Sizes
	add_filter( 'unos_archive_imgsize', 'unosmvu_archive_imgsize', 7, 2 );
	// Remove the filter (to append google fonts) and let those fonts occur in their natural order as in hoot_googlefonts_list()
	remove_filter( 'hoot_fonts_list', 'unosmvu_fonts_list', 15 );
}