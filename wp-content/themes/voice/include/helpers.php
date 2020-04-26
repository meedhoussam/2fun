<?php
/*-----------------------------------------------------------------------------------*/
/*	Helpers and utils functions for theme use
/*-----------------------------------------------------------------------------------*/

/* 	Debug (log) function */
if ( !function_exists( 'vce_log' ) ):
	function vce_log( $mixed ) {

		if ( is_array( $mixed ) ) {
			$mixed = print_r( $mixed, 1 );
		} else if ( is_object( $mixed ) ) {
				ob_start();
				var_dump( $mixed );
				$mixed = ob_get_clean();
			}

		$handle = fopen( THEME_DIR . '/log', 'a' );
		fwrite( $handle, $mixed . PHP_EOL );
		fclose( $handle );
	}
endif;

/* 	Get theme option function */
if ( !function_exists( 'vce_get_option' ) ):
	function vce_get_option( $option ) {

		global $vce_settings;

		if ( empty( $vce_settings ) ) {
			$vce_settings = get_option( 'vce_settings' );
		}

		if ( isset( $vce_settings[$option] ) ) {
			return $vce_settings[$option];
		} else {
			return false;
		}
	}
endif;

/* 	Update theme option function */
if ( !function_exists( 'vce_update_option' ) ):
	function vce_update_option( $key = false, $value = false ) {
		global $Redux_Options;
		if ( !empty( $key ) ) {
			$Redux_Options->set( $key, $value );
		}
	}
endif;

/* 	Merge multidimensional array - similar to wp_parse_args() just a bit extended :) */
if ( !function_exists( 'vce_parse_args' ) ):
	function vce_parse_args( &$a, $b ) {
		$a = (array) $a;
		$b = (array) $b;
		$r = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $r[ $k ] ) ) {
				$r[ $k ] = vce_parse_args( $v, $r[ $k ] );
			} else {
				$r[ $k ] = $v;
			}
		}
		return $r;
	}
endif;


/* Get list of image sizes to generate for theme use */
if ( !function_exists( 'vce_get_image_sizes' ) ):
	function vce_get_image_sizes() {
		$sizes = array(
			'vce-lay-a' => array( 'title' => 'Layout A (also layout G, single post and page)', 'w' => 810, 'h' => 9999, 'crop' => false ),
			'vce-lay-a-nosid' => array( 'title' => 'Layout A (full width - no sidebar)', 'w' => 1140, 'h' => 9999, 'crop' => false ),
			'vce-lay-b' => array( 'title' => 'Layout B (also layout C and post gallery thumbnails)', 'w' => 375, 'h' => 195, 'crop' => true ),
			'vce-lay-d' => array( 'title' => 'Layout D (also layout E and post gallery thumbnails)', 'w' => 145, 'h' => 100, 'crop' => true ),
			'vce-fa-full' => array( 'title' => 'Featured area (big - full width)', 'w' => 9999, 'h' => 500, 'crop' => true ),
			'vce-fa-grid' => array( 'title' => 'Layout H and Featured area (5 grid items)', 'w' => 380, 'h' => 260, 'crop' => true ),
			'vce-fa-big-grid' => array( 'title' => 'Featured area (3 grid items)', 'w' => 634, 'h' => 433, 'crop' => true ),
		);

		$sizes = apply_filters( 'vce_modify_image_sizes', $sizes );

		return $sizes;
	}
endif;

/* Get sidebar layouts */
if ( !function_exists( 'vce_get_sidebar_layouts' ) ):
	function vce_get_sidebar_layouts( $inherit = false ) {

		$layouts = array();

		if ( $inherit ) {
			$layouts['inherit'] = array( 'title' => __( 'Inherit', THEME_SLUG ), 'img' => IMG_URI . '/admin/inherit.png' );
		}

		$layouts['none'] = array( 'title' => __( 'No sidebar (full width)', THEME_SLUG ), 'img' => IMG_URI . '/admin/content_no_sid.png' );
		$layouts['left'] = array( 'title' => __( 'Left sidebar', THEME_SLUG ), 'img' => IMG_URI . '/admin/content_sid_left.png' );
		$layouts['right'] = array( 'title' => __( 'Right sidebar', THEME_SLUG ), 'img' => IMG_URI . '/admin/content_sid_right.png' );

		$layouts = apply_filters( 'vce_modify_sidebar_elements', $layouts ); //Allow child themes or plugins to modify
		return $layouts;
	}
endif;

/* Get single post layout options */
if ( !function_exists( 'vce_get_single_layout_opts' ) ) :

	function vce_get_single_layout_opts( $inherit = false ) {

		$layouts = array();

		if ( $inherit ) :
			$layouts['inherit'] = array( 'title' => __( 'Inherit', THEME_SLUG ), 'img' => IMG_URI . '/admin/inherit.png' );
		endif;

		$layouts['classic'] = array(
			'title' => __( 'Standard', THEME_SLUG ),
			'img' => IMG_URI . '/admin/single_classic.jpg'
		);

		$layouts['cover'] = array(
			'title' => __( 'Cover', THEME_SLUG ),
			'img' => IMG_URI . '/admin/single_cover.jpg'
		);

		$layouts = apply_filters( 'vce_modify_single_layout_opts', $layouts ); //Allow child themes or plugins to modify
		return $layouts;

	}

endif;


/* Get all sidebars */
if ( !function_exists( 'vce_get_sidebars_list' ) ):
	function vce_get_sidebars_list( $inherit = false ) {

		$sidebars = array();

		if ( $inherit ) {
			$sidebars['inherit'] = __( 'Inherit', THEME_SLUG );
		}

		$sidebars['0'] = __( 'None', THEME_SLUG );

		global $wp_registered_sidebars;

		if ( !empty( $wp_registered_sidebars ) ) {

			foreach ( $wp_registered_sidebars as $sidebar ) {
				$sidebars[$sidebar['id']] = $sidebar['name'];
			}

		}


		//Get sidebars from wp_options if global var is not loaded yet
		$fallback_sidebars = get_option( 'vce_registered_sidebars' );
		if ( !empty( $fallback_sidebars ) ) {
			foreach ( $fallback_sidebars as $sidebar ) {
				if ( !array_key_exists( $sidebar['id'], $sidebars ) ) {
					$sidebars[$sidebar['id']] = $sidebar['name'];
				}
			}
		}

		//Check for theme additional sidebars
		$custom_sidebars = vce_get_option( 'add_sidebars' );

		if ( $custom_sidebars ) {
			for ( $i = 1; $i <= $custom_sidebars; $i++ ) {
				if ( !array_key_exists( 'vce_sidebar_'.$i, $sidebars ) ) {
					$sidebars['vce_sidebar_'.$i] = __( 'Sidebar', THEME_SLUG ).' '.$i;
				}
			}
		}


		//Do not display footer sidebars for selection
		unset( $sidebars['vce_footer_sidebar_1'] );
		unset( $sidebars['vce_footer_sidebar_2'] );
		unset( $sidebars['vce_footer_sidebar_3'] );

		$sidebars = apply_filters( 'vce_modify_sidebars_list', $sidebars ); //Allow child themes or plugins to modify
		return $sidebars;
	}
endif;

/* Get current archive layout  */
if ( !function_exists( 'vce_get_archive_layout' ) ):
	function vce_get_archive_layout() {

		$template = vce_detect_template();

		if ( in_array( $template, array( 'search', 'tag', 'author', 'archive', 'posts_page' ) ) ) {

			$layout = vce_get_option( $template.'_layout' );
		}

		if ( empty( $layout ) ) {
			$layout = 'b';
		}

		$layout = apply_filters( 'vce_modify_archive_layout', $layout ); //Allow child themes or plugins to modify
		return $layout;
	}
endif;

/* Get current archive layout  */
if ( !function_exists( 'vce_get_archive_pagination' ) ):
	function vce_get_archive_pagination() {

		$template = vce_detect_template();

		if ( in_array( $template, array( 'search', 'tag', 'author', 'archive', 'posts_page' ) ) ) {

			$pagination = vce_get_option( $template.'_pagination' );
		}

		if ( empty( $pagination ) ) {
			$pagination = 'numeric';
		}

		$pagination = apply_filters( 'vce_modify_archive_pagination', $pagination ); //Allow child themes or plugins to modify
		return $pagination;
	}
endif;

/* Get current archive layout  */
if ( !function_exists( 'vce_get_category_pagination' ) ):
	function vce_get_category_pagination() {

		$pagination = vce_get_option( 'category_pagination' );

		if ( empty( $pagination ) ) {
			$pagination = 'numeric';
		}

		$pagination = apply_filters( 'vce_modify_category_pagination', $pagination ); //Allow child themes or plugins to modify
		return $pagination;
	}
endif;

/* Get current category layout  */
if ( !function_exists( 'vce_get_category_layout' ) ):
	function vce_get_category_layout() {

		$args = array();
		$obj = get_queried_object();
		$meta = vce_get_category_meta( $obj->term_id );
		$args['layout'] = $meta['layout'] == 'inherit' ? vce_get_option( 'category_layout' ) : $meta['layout'];

		$paged = absint( get_query_var( 'paged' ) );

		if ( $paged <= 1 ) {
			if ( $meta['top_layout'] == 'inherit' ) {
				$args['top_layout'] = vce_get_option( 'category_use_top' ) ? vce_get_option( 'category_top_layout' ) : false;
				$args['top_limit'] = vce_get_option( 'category_use_top' ) ? vce_get_option( 'category_top_limit' ) : false;
			} else {
				$args['top_layout'] = $meta['top_layout'];
				$args['top_limit'] = $meta['top_limit'];
			}
		} else {
			$args['top_layout'] = false;
			$args['top_limit'] = false;
		}

		$args = apply_filters( 'vce_modify_category_layout', $args ); //Allow child themes or plugins to modify
		return $args;
	}
endif;

/* Get featured area layouts */
if ( !function_exists( 'vce_get_featured_area_layouts' ) ):
	function vce_get_featured_area_layouts( $inherit = false, $none = false ) {

		if ( $inherit ) {
			$layouts['inherit'] = array( 'title' => __( 'Inherit', THEME_SLUG ), 'img' => IMG_URI . '/admin/inherit.png' );
		}

		if ( $none ) {
			$layouts['0'] = array( 'title' => __( 'None', THEME_SLUG ), 'img' => IMG_URI . '/admin/none.png' );
		}

		$layouts['full_grid'] = array( 'title' => __( 'Big post + slider below', THEME_SLUG ), 'img' => IMG_URI . '/admin/featured_both.png' );
		$layouts['full'] = array( 'title' => __( 'Big post(s) only', THEME_SLUG ), 'img' => IMG_URI . '/admin/featured_big.png' );
		$layouts['grid'] = array( 'title' => __( 'Slider only', THEME_SLUG ), 'img' => IMG_URI . '/admin/featured_grid.png' );
		$layouts['big-grid'] = array( 'title' => __( 'Big Slider only', THEME_SLUG ), 'img' => IMG_URI . '/admin/featured_big_grid.png' );

		$layouts = apply_filters( 'vce_modify_featured_area_layouts', $layouts ); //Allow child themes or plugins to modify
		return $layouts;
	}
endif;

/* Get main post layouts layouts */
if ( !function_exists( 'vce_get_main_layouts' ) ):
	function vce_get_main_layouts( $inherit = false, $none = false ) {

		if ( $inherit ) {
			$layouts['inherit'] = array( 'title' => __( 'Inherit', THEME_SLUG ), 'img' => IMG_URI . '/admin/inherit.png' );
		}

		if ( $none ) {
			$layouts['0'] = array( 'title' => __( 'None', THEME_SLUG ), 'img' => IMG_URI . '/admin/none.png' );
		}

		$layouts['a'] = array( 'title' => __( 'Layout A', THEME_SLUG ), 'img' => IMG_URI . '/admin/layout_a.png' );
		$layouts['b'] = array( 'title' => __( 'Layout B', THEME_SLUG ), 'img' => IMG_URI . '/admin/layout_b.png' );
		$layouts['c'] = array( 'title' => __( 'Layout C', THEME_SLUG ), 'img' => IMG_URI . '/admin/layout_c.png' );
		$layouts['d'] = array( 'title' => __( 'Layout D', THEME_SLUG ), 'img' => IMG_URI . '/admin/layout_d.png' );
		$layouts['e'] = array( 'title' => __( 'Layout E', THEME_SLUG ), 'img' => IMG_URI . '/admin/layout_e.png' );
		$layouts['f'] = array( 'title' => __( 'Layout F', THEME_SLUG ), 'img' => IMG_URI . '/admin/layout_f.png' );
		$layouts['g'] = array( 'title' => __( 'Layout G', THEME_SLUG ), 'img' => IMG_URI . '/admin/layout_g.png' );
		$layouts['h'] = array( 'title' => __( 'Layout H', THEME_SLUG ), 'img' => IMG_URI . '/admin/layout_h.png' );

		$layouts = apply_filters( 'vce_modify_main_layouts', $layouts ); //Allow child themes or plugins to modify
		return $layouts;
	}
endif;


/* Get category module  layouts */
if ( !function_exists( 'vce_get_category_layouts' ) ):
	function vce_get_category_layouts() {

		$layouts['g'] = array( 'title' => __( 'One Column', THEME_SLUG ), 'img' => IMG_URI . '/admin/category_one.png' );
		$layouts['c'] = array( 'title' => __( 'Two Columns', THEME_SLUG ), 'img' => IMG_URI . '/admin/category_two.png' );
		$layouts['e'] = array( 'title' => __( 'Five Columns', THEME_SLUG ), 'img' => IMG_URI . '/admin/category_five.png' );

		$layouts = apply_filters( 'vce_modify_category_layouts', $layouts ); //Allow child themes or plugins to modify
		return $layouts;
	}
endif;


/* Get main post layouts layouts */
if ( !function_exists( 'vce_get_pagination_layouts' ) ):
	function vce_get_pagination_layouts() {
		$layouts = array(
			'prev-next' => array( 'title' => __( 'Prev/Next page links', THEME_SLUG ), 'img' => IMG_URI . '/admin/pag_prev_next.png' ),
			'numeric' => array( 'title' => __( 'Numeric pagination links', THEME_SLUG ), 'img' => IMG_URI . '/admin/pag_numeric.png' ),
			'load-more' => array( 'title' => __( 'Load more button', THEME_SLUG ), 'img' => IMG_URI . '/admin/pag_load_more.png' ),
			'infinite-scroll' => array( 'title' => __( 'Infinite scroll', THEME_SLUG ), 'img' => IMG_URI . '/admin/pag_infinite.png' ),
		);

		$layouts = apply_filters( 'vce_modify_pagination_layouts', $layouts ); //Allow child themes or plugins to modify
		return $layouts;
	}
endif;

/* Get meta data options */

if ( !function_exists( 'vce_get_meta_opts' ) ):
	function vce_get_meta_opts( $default = array() ) {

		$options = array();

		$options['date'] = esc_html__( 'Date', THEME_SLUG );
		$options['comments'] = esc_html__( 'Comments', THEME_SLUG );
		$options['author'] = esc_html__( 'Author', THEME_SLUG );
		$options['views'] = esc_html__( 'Views', THEME_SLUG );
		$options['rtime'] = esc_html__( 'Reading time', THEME_SLUG );
		$options['modified_date'] = esc_html__( 'Modified date', THEME_SLUG );

		if ( vce_is_wp_review_active() ) {
			$options['reviews'] = esc_html__( 'Reviews', THEME_SLUG );
		}

		if ( !empty( $default ) ) {
			foreach ( $options as $key => $option ) {
				if ( in_array( $key, $default ) ) {
					$options[$key] = 1;
				} else {
					$options[$key] = 0;
				}
			}
		}

		$options = apply_filters( 'vce_modify_meta_opts', $options );
		return $options;
	}
endif;




/* Include simple pagination */
if ( !function_exists( 'vce_pagination' ) ):
	function vce_pagination( $prev = '&lsaquo;', $next = '&rsaquo;' ) {
		global $wp_query, $wp_rewrite;
		$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
		$pagination = array(
			'base' => @add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'total' => $wp_query->max_num_pages,
			'current' => $current,
			'prev_text' => $prev,
			'next_text' => $next,
			'type' => 'plain'
		);
		if ( $wp_rewrite->using_permalinks() )
			$pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );

		if ( !empty( $wp_query->query_vars['s'] ) )
			$pagination['add_args'] = array( 's' => str_replace( ' ', '+', get_query_var( 's' ) ) );

		$links = paginate_links( $pagination );

		if ( $links ) {
			return $links;
		}
	}
endif;


/* Convert hexdec color string to rgba */
if ( !function_exists( 'vce_hex2rgba' ) ):
	function vce_hex2rgba( $color, $opacity = false ) {
		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if ( empty( $color ) )
			return $default;

		//Sanitize $color if "#" is provided
		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if ( strlen( $color ) == 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb =  array_map( 'hexdec', $hex );

		//Check if opacity is set(rgba or rgb)
		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) { $opacity = 1.0; }
			$output = 'rgba('.implode( ",", $rgb ).','.$opacity.')';
		} else {
			$output = 'rgb('.implode( ",", $rgb ).')';
		}

		//Return rgb(a) color string
		return $output;
	}
endif;

/* Get array of social options  */
if ( !function_exists( 'vce_get_social' ) ) :
	function vce_get_social( $existing = false ) {
		$social = array(
			'0' => 'None',
			'apple' => 'Apple',
			'behance' => 'Behance',
			'delicious' => 'Delicious',
			'deviantart' => 'DeviantArt',
			'digg' => 'Digg',
			'dribbble' => 'Dribbble',
			'facebook' => 'Facebook',
			'flickr' => 'Flickr',
			'github' => 'Github',
			'google' => 'GooglePlus',
			'instagram' => 'Instagram',
			'linkedin' => 'LinkedIN',
			'pinterest' => 'Pinterest',
			'reddit' => 'ReddIT',
			'rss' => 'Rss',
			'skype' => 'Skype',
			'stumbleupon' => 'StumbleUpon',
			'soundcloud' => 'SoundCloud',
			'spotify' => 'Spotify',
			'tumblr' => 'Tumblr',
			'twitter' => 'Twitter',
			'vimeo' => 'Vimeo',
			'vine' => 'Vine',
			'wordpress' => 'WordPress',
			'xing' => 'Xing' ,
			'yahoo' => 'Yahoo',
			'youtube' => 'Youtube'
		);

		if ( $existing ) {
			$new_social = array();
			foreach ( $social as $key => $soc ) {
				if ( $key && vce_get_option( 'soc_'.$key.'_url' ) ) {
					$new_social[$key] = $soc;
				}
			}
			$social = $new_social;
		}

		$social = apply_filters( 'vce_modify_social', $social );
		return $social;
	}
endif;


/* Get Theme Translated String */
if ( !function_exists( '__vce' ) ):
	function __vce( $string_key ) {
		if ( ( $translated_string = vce_get_option( 'tr_'.$string_key ) ) && vce_get_option( 'enable_translate' ) ) {

			if ( $translated_string == '-1' ) {
				return "";
			}

			return $translated_string;

		} else {

			$translate = vce_get_translate_options();
			return $translate[$string_key]['translated'];
		}
	}
endif;

/* Get All Translation Strings */
if ( !function_exists( 'vce_get_translate_options' ) ):
	function vce_get_translate_options() {
		global $vce_translate;
		get_template_part( 'include/translate' );
		$translate = apply_filters( 'vce_modify_translate_options', $vce_translate );
		return $translate;
	}
endif;

/* Compress CSS Code  */
if ( !function_exists( 'vce_compress_css_code' ) ) :
	function vce_compress_css_code( $code ) {

		// Remove Comments
		$code = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code );

		// Remove tabs, spaces, newlines, etc.
		$code = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $code );

		return $code;
	}
endif;


/* Get image option url */
if ( !function_exists( 'vce_get_option_media' ) ):
	function vce_get_option_media( $option ) {
		$media = vce_get_option( $option );
		if ( isset( $media['url'] ) && !empty( $media['url'] ) ) {
			return $media['url'];
		}
		return false;
	}
endif;

/* Generate font links */
if ( !function_exists( 'vce_generate_font_links' ) ):
	function vce_generate_font_links() {

		$output = array();
		$fonts = array();
		$fonts[] = vce_get_option( 'main_font' );
		$fonts[] = vce_get_option( 'h_font' );
		$fonts[] = vce_get_option( 'nav_font' );
		$unique = array(); //do not add same font links
		$native = vce_get_native_fonts();
		$protocol = is_ssl() ? 'https://' : 'http://';

		foreach ( $fonts as $font ) {
			if ( !in_array( $font['font-family'], $native ) ) {
				$temp = array();
				if ( isset( $font['font-style'] ) ) {
					$temp['font-style'] = $font['font-style'];
				}
				if ( isset( $font['subsets'] ) ) {
					$temp['subsets'] = $font['subsets'];
				}
				if ( isset( $font['font-weight'] ) ) {
					$temp['font-weight'] = $font['font-weight'];
				}
				$unique[$font['font-family']][] = $temp;
			}
		}

		foreach ( $unique as $family => $items ) {

			$link = $protocol.'fonts.googleapis.com/css?family='.str_replace( ' ', '%20', $family ); //valid

			$weight = array( '400' );
			$subsets = array( 'latin' );

			foreach ( $items as $item ) {

				//Check weight and style
				if ( isset( $item['font-weight'] ) && !empty( $item['font-weight'] ) ) {
					$temp = $item['font-weight'];
					if ( isset( $item['font-style'] ) && empty( $item['font-style'] ) ) {
						$temp .= $item['font-style'];
					}

					if ( !in_array( $temp, $weight ) ) {
						$weight[] = $temp;
					}
				}

				//Check subsets
				if ( isset( $item['subsets'] ) && !empty( $item['subsets'] ) ) {
					if ( !in_array( $item['subsets'], $subsets ) ) {
						$subsets[] = $item['subsets'];
					}
				}
			}

			$link .= ':'.implode( ",", $weight );
			$link .= '&subset='.implode( ",", $subsets );

			$output[] = str_replace( '&', '&amp;', $link ); //valid
		}

		return $output;
	}
endif;

/* Generate dynamic CSS */
if ( !function_exists( 'vce_generate_dynamic_css' ) ):
	function vce_generate_dynamic_css() {
		ob_start();
		get_template_part( 'css/dynamic-css' );
		$output = ob_get_contents();
		ob_end_clean();
		return vce_compress_css_code( $output );
	}
endif;


/* Get list of native fonts */
if ( !function_exists( 'vce_get_native_fonts' ) ):
	function vce_get_native_fonts() {

		$fonts = array(
			"Arial, Helvetica, sans-serif",
			"'Arial Black', Gadget, sans-serif",
			"'Bookman Old Style', serif",
			"'Comic Sans MS', cursive",
			"Courier, monospace",
			"Garamond, serif",
			"Georgia, serif",
			"Impact, Charcoal, sans-serif",
			"'Lucida Console', Monaco, monospace",
			"'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
			"'MS Sans Serif', Geneva, sans-serif",
			"'MS Serif', 'New York', sans-serif",
			"'Palatino Linotype', 'Book Antiqua', Palatino, serif",
			"Tahoma,Geneva, sans-serif",
			"'Times New Roman', Times,serif",
			"'Trebuchet MS', Helvetica, sans-serif",
			"Verdana, Geneva, sans-serif"
		);

		return $fonts;
	}
endif;


/* Add class to category links */
if ( !function_exists( 'vce_get_category' ) ):
	function vce_get_category() {
		$output = '';
		$cats = array();

		$primary_category = vce_get_primary_category();

		if ( !empty( $primary_category ) ) {
			$cats[0] = $primary_category;
		}

		if ( empty( $cats ) ) {
			$cats = get_the_category();
		}

		if ( empty( $cats ) ) {
			return $output;
		}

		foreach ( $cats as $k => $cat ) {
			$output.= '<a href="'.get_category_link( $cat->term_id ).'" class="category-'.esc_attr( $cat->term_id ).'">'.$cat->name.'</a>';
			if ( ( $k + 1 ) != count( $cats ) ) {
				$output.= ' <span>&bull;</span> ';
			}
		}

		return $output;

	}
endif;

/* Custom function to limit post content words */
if ( !function_exists( 'vce_get_excerpt' ) ):
	function vce_get_excerpt( $layout = 'lay-a' ) {

		$map = array(
			'lay-a' => 'lay_a',
			'lay-b' => 'lay_b',
			'lay-c' => 'lay_c',
			'lay-g' => 'lay_g',
			'lay-h' => 'lay_h',
			'lay-fa-big' => 'lay_fa_big',
		);

		if ( !array_key_exists( $layout, $map ) ) {
			return '';
		}

		$manual_excerpt = false;

		if ( has_excerpt() ) {
			$content =  get_the_excerpt();
			$manual_excerpt = true;
		} else {
			//$content = apply_filters('the_content',get_the_content(''));
			$text = get_the_content( '' );
			$text = strip_shortcodes( $text );
			$text = apply_filters( 'the_content', $text );
			$content = str_replace( ']]>', ']]&gt;', $text );
		}

		//print_r($content);


		if ( !empty( $content ) ) {
			$limit = vce_get_option( $map[$layout].'_excerpt_limit' );
			if ( !empty( $limit ) || !$manual_excerpt ) {
				$more = vce_get_option( 'more_string' );
				$content = wp_strip_all_tags( $content );
				$content = preg_replace( '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $content );
				$content = vce_trim_chars( $content, $limit, $more );
			}
			return $content;
		}

		return '';

	}
endif;

/* Custom function to limit post title chars for specific layout */
if ( !function_exists( 'vce_get_title' ) ):
	function vce_get_title( $layout = 'lay-a' ) {

		$map = array(
			'lay-a' => 'lay_a',
			'lay-b' => 'lay_b',
			'lay-c' => 'lay_c',
			'lay-d' => 'lay_d',
			'lay-e' => 'lay_e',
			'lay-f' => 'lay_f',
			'lay-h' => 'lay_h',
			'lay-fa-grid' => 'lay_fa_grid',
			'lay-fa-grid-big' => 'lay_fa_grid_big',
		);

		if ( !array_key_exists( $layout, $map ) ) {
			return get_the_title();
		}


		$title_limit = vce_get_option( $map[$layout].'_title_limit' );


		if ( !empty( $title_limit ) ) {
			$output = vce_trim_chars( strip_tags( get_the_title() ), $title_limit, vce_get_option( 'more_string' ) );
		} else {
			$output = get_the_title();
		}


		return $output;

	}
endif;

/* Trim chars of string */
if ( !function_exists( 'vce_trim_chars' ) ):
	function vce_trim_chars( $string, $limit, $more = '...' ) {

		if ( !empty( $limit ) ) {
			$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $string ), ' ' );
			preg_match_all( '/./u', $text, $chars );
			$chars = $chars[0];
			$count = count( $chars );
			if ( $count > $limit ) {

				$chars = array_slice( $chars, 0, $limit );

				for ( $i = ( $limit -1 ); $i >= 0; $i-- ) {
					if ( in_array( $chars[$i], array( '.', ' ', '-', '?', '!' ) ) ) {
						break;
					}
				}

				$chars =  array_slice( $chars, 0, $i );
				$string = implode( '', $chars );
				$string = rtrim( $string, ".,-?!" );
				$string.= $more;
			}
		}

		return $string;
	}
endif;

/* Custom function to get meta data for specific layout */
if ( !function_exists( 'vce_get_meta_data' ) ):
	function vce_get_meta_data( $layout = 'lay-a', $force_meta = false ) {
		
		if ( !$force_meta ) {
			$map = array(
				'lay-a' => 'lay_a',
				'lay-b' => 'lay_b',
				'lay-c' => 'lay_c',
				'lay-d' => 'lay_d',
				'lay-e' => 'lay_e',
				'lay-g' => 'lay_g',
				'lay-h' => 'lay_h',
				'lay-fa-grid' => 'lay_fa_grid',
				'lay-fa-grid-big' => 'lay_fa_grid_big',
				'lay-fa-big' => 'lay_fa_big',
				'single' => 'single',
			);
			//Layouts theme options
			$layout_metas = array_filter( vce_get_option( $map[$layout].'_meta' ) );

		} else {
			//From widget or anywhere you want
			$layout_metas = array( $force_meta => '1' );
		}

		$output = '';

		if ( !empty( $layout_metas ) ) {

			foreach ( $layout_metas as $mkey => $active ) {


				$meta = '';

				switch ( $mkey ) {

				case 'date':
					$meta = '<span class="updated">'.vce_get_date().'</span>';
					break;

				case 'modified_date':
					$meta = '<span class="updated">'.vce_get_modified_date().'</span>';
					break;

				case 'author':
					if ( voice_is_co_authors_active() && $coauthors_meta = get_coauthors() ) {
						$temp = '';
						foreach ( $coauthors_meta as $i => $key ) {
							$separator = $i != ( count( $coauthors_meta ) - 1 ) ? ', ' : '';
							$temp .= '<span class="vcard author">
										<span class="fn">
											<a href="'.get_author_posts_url(  $key->ID, $key->user_nicename ).'">'.$key->display_name.'</a>'. $separator .'
										</span>
									</span>';
						}
						$meta = '<span>' . __vce( 'by_author' ) . '' .  $temp . '</span>';

					} else {
						$post_author_id = get_post_field( 'post_author', get_the_ID() );
						$meta = '<span class="vcard author"><span class="fn">'.__vce( 'by_author' ).' <a href="'.esc_url( get_author_posts_url( get_the_author_meta( 'ID', $post_author_id ) ) ).'">'.get_the_author_meta( 'display_name', $post_author_id ).'</a></span></span>';
					}
					break;

				case 'comments':
					if ( comments_open() || get_comments_number() ) {
						ob_start();
						comments_popup_link( __vce( 'no_comments' ), __vce( 'one_comment' ), __vce( 'multiple_comments' ) );
						$meta = ob_get_contents();
						ob_end_clean();
					} else {
						$meta = '';
					}
					break;

				case 'views':
					global $wp_locale;
					$thousands_sep = isset( $wp_locale->number_format['thousands_sep'] ) ? $wp_locale->number_format['thousands_sep'] : ',';
					if ( strlen( $thousands_sep ) > 1 ) {
						$thousands_sep = trim( $thousands_sep );
					}
					$meta = function_exists( 'ev_get_post_view_count' ) ?  number_format_i18n( absint( str_replace( $thousands_sep, '', ev_get_post_view_count( get_the_ID() ) ) + absint( vce_get_option( 'views_forgery' ) ) ) )  . ' '.__vce( 'views' ) : '';
					break;

				case 'rtime':
					$meta = vce_read_time( get_the_content() );
					if ( !empty( $meta ) ) {
						$meta .= ' '.__vce( 'min_read' );
					}
					break;

				case 'reviews':
					$meta = '';
					if ( vce_is_wp_review_active() ) {
						$meta = function_exists( 'wp_review_show_total' ) ? wp_review_show_total( false, '' ) : '';
					}
					break;

				default:
					break;
				}

				if ( !empty( $meta ) ) {
					$output .= '<div class="meta-item '.$mkey.'">'.$meta.'</div>';
				}
			}
		}


		return $output;

	}
endif;

/* Display featured image, and more :) */
if ( !function_exists( 'vce_featured_image' ) ):
	function vce_featured_image( $size = 'large', $post_id = false ) {


		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		if ( has_post_thumbnail( $post_id ) ) {
			return get_the_post_thumbnail( $post_id, $size );

		} else if ( $placeholder = vce_get_option_media( 'default_fimg' ) ) {

				global $placeholder_img, $placeholder_imgs;

				if ( empty( $placeholder_img ) ) {
					$img_id = vce_get_image_id_by_url( $placeholder );
				} else {
					$img_id = $placeholder_img;
				}

				if ( !empty( $img_id ) ) {
					if ( !isset( $placeholder_imgs[$size] ) ) {
						$def_img = wp_get_attachment_image( $img_id, $size );
					} else {
						$def_img = $placeholder_imgs[$size];
					}

					if ( !empty( $def_img ) ) {
						$placeholder_imgs[$size] = $def_img;
						return $def_img;
					}
				}

				return '<img src="'.$placeholder.'" />';
			}

		return false;
	}
endif;

/* Get image id by url */
if ( !function_exists( 'vce_get_image_id_by_url' ) ):
	function vce_get_image_id_by_url( $image_url ) {
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );

		if ( isset( $attachment[0] ) ) {
			return $attachment[0];
		}

		return false;
	}
endif;

/* Check wheter to display date in standard or "time ago" format */
if ( !function_exists( 'vce_get_date' ) ):
	function vce_get_date() {

		if ( vce_get_option( 'time_ago' ) ) {

			$limits = array(
				'hour' => 3600,
				'day' => 86400,
				'week' => 604800,
				'month' => 2592000,
				'three_months' => 7776000,
				'six_months' => 15552000,
				'year' => 31104000,
				'0' => 0
			);

			$ago_limit = vce_get_option( 'time_ago_limit' );

			if ( array_key_exists( $ago_limit, $limits ) ) {

				if ( ( current_time( 'timestamp' ) - get_the_time( 'U' ) <= $limits[$ago_limit] ) || empty( $ago_limit ) ) {
					if ( vce_get_option( 'ago_before' ) ) {
						return __vce( 'ago' ).' '.human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) );
					} else {
						return human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ).' '.__vce( 'ago' );
					}
				} else {
					return get_the_date();
				}
			} else {
				return get_the_date();
			}
		} else {
			return get_the_date();
		}
	}
endif;

/* Check wheter to display modified date in standard or "time ago" format */
if ( !function_exists( 'vce_get_modified_date' ) ):
	function vce_get_modified_date() {

		if ( vce_get_option( 'time_ago' ) ) {

			$limits = array(
				'hour' => 3600,
				'day' => 86400,
				'week' => 604800,
				'month' => 2592000,
				'three_months' => 7776000,
				'six_months' => 15552000,
				'year' => 31104000,
				'0' => 0
			);

			$ago_limit = vce_get_option( 'time_ago_limit' );

			if ( array_key_exists( $ago_limit, $limits ) ) {

				if ( ( current_time( 'timestamp' ) - get_the_modified_time( 'U' ) <= $limits[$ago_limit] ) || empty( $ago_limit ) ) {
					if ( vce_get_option( 'ago_before' ) ) {
						return __vce( 'ago' ).' '.human_time_diff( get_the_modified_time( 'U' ), current_time( 'timestamp' ) );
					} else {
						return human_time_diff( get_the_modified_time( 'U' ), current_time( 'timestamp' ) ).' '.__vce( 'ago' );
					}
				} else {
					return get_the_modified_date();
				}
			} else {
				return get_the_modified_date();
			}
		} else {
			return get_the_modified_date();
		}
	}
endif;

/* Get post meta with default values */
if ( !function_exists( 'vce_get_post_meta' ) ):
	function vce_get_post_meta( $post_id, $field = false ) {

		$defaults = array(
			'layout' => 'inherit',
			'use_sidebar' => 'inherit',
			'sidebar' => 'inherit',
			'sticky_sidebar' => 'inherit',
			'display' => array(
				'show_cat' => 'inherit',
				'show_fimg' => 'inherit',
				'show_author_img' => 'inherit',
				'show_headline' => 'inherit',
				'show_tags' => 'inherit',
				'show_prev_next' => 'inherit',
				'show_author_box' => 'inherit',
				'show_related' => 'inherit',
			)
		);

		$meta = get_post_meta( $post_id, '_vce_meta', true );
		$meta = vce_parse_args( $meta, $defaults );


		if ( $field ) {
			if ( isset( $meta[$field] ) ) {
				return $meta[$field];
			} else {
				return false;
			}
		}

		return $meta;
	}
endif;

/* Get display options for single post */
if ( !function_exists( 'vce_get_post_display' ) ):
	function vce_get_post_display( $field ) {
		$post_id = get_the_ID();
		$meta = vce_get_post_meta( $post_id, 'display' );
		if ( $meta[$field] == 'inherit' ) {
			return vce_get_option( $field );
		} else {
			return $meta[$field] == 'on' ? true: false;
		}
	}
endif;

/* Check if we're using Cover Featured image */
if ( !function_exists( 'vce_use_cover_fimg' ) ) :

	function vce_use_cover_fimg() {

		$post_id = get_the_ID();
		$meta = is_single() ? vce_get_post_meta( $post_id, 'layout' ) : vce_get_page_meta( $post_id, 'layout' );

		if ( $meta == 'inherit' ) {
			$layout = is_single() ? vce_get_option( 'single_layout' ) :  vce_get_option( 'page_layout' );
			return ( $layout == 'classic' ) ? false : true;
		} else {
			return ( $meta == 'classic' ) ? false : true;
		}
	}

endif;



/* Get page meta with default values */
if ( !function_exists( 'vce_get_page_meta' ) ):
	function vce_get_page_meta( $post_id, $field = false ) {

		$defaults = array(
			'use_sidebar' => 'inherit',
			'sidebar' => 'inherit',
			'sticky_sidebar' => 'inherit',
			'layout' => 'inherit',
			'fa_post_type' => 'post',
			'fa_layout' => 0,
			'fa_limit' => 8,
			'fa_time' => 0,
			'fa_order' => 'date',
			'fa_sort' => 'DESC',
			'fa_manual' => array(),
			'fa_exclude' => 0,
			'fa_author' => array(),
			'fa_author_inc_exc' => 'in',
			'fa_exclude_by_id' => array(),
			'modules' => array(),
			'authors' => array( 'orderby' => 'post_count', 'order' => 'DESC', 'exclude' => '' ),
			'display_content' => array( 'position' => 0, 'style' => 'wrap', 'width' => 'container' ),
		);

		$meta = get_post_meta( $post_id, '_vce_meta', true );
		$meta = vce_parse_args( $meta, $defaults );

		if ( $field ) {
			if ( isset( $meta[$field] ) ) {
				return $meta[$field];
			} else {
				return false;
			}
		}

		return $meta;
	}
endif;

/* Get category meta with default values */
if ( !function_exists( 'vce_get_category_meta' ) ):
	function vce_get_category_meta( $cat_id = false, $field = false ) {
		$defaults = array(
			'layout' => 'inherit',
			'top_layout' => 'inherit',
			'top_limit' => vce_get_option( 'category_top_limit' ),
			'fa_layout' => 'inherit',
			'fa_limit' => vce_get_option( 'category_fa_limit' ),
			'use_sidebar' => 'inherit',
			'sidebar' => 'inherit',
			'sticky_sidebar' => 'inherit',
			'color_type' => 'inherit',
			'color' => '#000000',
			'ppp' => '',
			'image' => ''
		);

		if ( $cat_id ) {
			$meta = get_option( '_vce_category_'.$cat_id );
			$meta = wp_parse_args( (array) $meta, $defaults );
		} else {
			$meta = $defaults;
		}

		if ( $field ) {
			if ( isset( $meta[$field] ) ) {
				return $meta[$field];
			} else {
				return false;
			}
		}

		return $meta;
	}
endif;

/* Cache recently used category colors */
if ( !function_exists( 'vce_update_recent_cat_colors' ) ):
	function vce_update_recent_cat_colors( $color, $num_col = 10 ) {
		if ( empty( $color ) )
			return false;

		$current = get_option( 'vce_recent_cat_colors' );
		if ( empty( $current ) ) {
			$current = array();
		}

		$update = false;

		if ( !in_array( $color, $current ) ) {
			$current[] = $color;
			if ( count( $current ) > $num_col ) {
				$current = array_slice( $current, ( count( $current ) - $num_col ), ( count( $current ) - 1 ) );
			}
			$update = true;
		}

		if ( $update ) {
			update_option( 'vce_recent_cat_colors', $current );
		}

	}
endif;

/* Store color per each category */
if ( !function_exists( 'vce_update_cat_colors' ) ):
	function vce_update_cat_colors( $cat_id, $color, $type ) {

		$colors = (array)get_option( 'vce_cat_colors' );

		if ( array_key_exists( $cat_id, $colors ) ) {

			if ( $type == 'inherit' ) {
				unset( $colors[$cat_id] );
			} elseif ( $colors[$cat_id] != $color ) {
				$colors[$cat_id] = $color;
			}

		} else {

			if ( $type != 'inherit' ) {
				$colors[$cat_id] = $color;
			}
		}

		update_option( 'vce_cat_colors', $colors );

	}
endif;

/* Detect WordPress template */
if ( !function_exists( 'vce_detect_template' ) ):
	function vce_detect_template() {
		$template = '';
		if ( is_single() ) {

			$type = get_post_type( get_the_ID() );

			if ( $type == 'product' ) {
				$template = 'product';
			} else if ( $type == 'forum' ) {
					$template = 'forum';
				} else if ( $type == 'topic' ) {
					$template = 'topic';
				} else {
				$template = 'single';
			}

		} else if ( is_page_template( 'template-modules.php' ) && is_page() ) {
				$template = 'home_page';
			} else if ( is_page() ) {
				$template = 'page';
			} else if ( is_category() ) {
				$template = 'category';
			} else if ( is_tag() ) {
				$template = 'tag';
			} else if ( is_search() ) {
				$template = 'search';
			} else if ( is_author() ) {
				$template = 'author';
			} else if ( is_home() && ( $posts_page = get_option( 'page_for_posts' ) ) && !is_page_template( 'template-modules.php' ) ) {
				$template = 'posts_page';
			} else if ( is_tax( 'product_cat' ) || is_post_type_archive( 'product' ) ) {
				$template = 'product_cat';
			} else {
			$template = 'archive';
		}


		return $template;
	}
endif;

/* Get current sidebar options */
if ( !function_exists( 'vce_get_current_sidebar' ) ):
	function vce_get_current_sidebar() {

		/* Default */
		$use_sidebar = 'none';
		$sidebar = 'vce_default_sidebar';
		$sticky_sidebar = 'vce_default_sticky_sidebar';

		$vce_template = vce_detect_template();

		if ( in_array( $vce_template, array( 'search', 'tag', 'author', 'archive', 'product', 'product_cat', 'forum', 'topic' ) ) ) {

			$use_sidebar = vce_get_option( $vce_template.'_use_sidebar' );


			if ( $use_sidebar != 'none' ) {
				$sidebar = vce_get_option( $vce_template.'_sidebar' );
				$sticky_sidebar = vce_get_option( $vce_template.'_sticky_sidebar' );
			}

		} else if ( $vce_template == 'category' ) {
				$obj = get_queried_object();
				if ( isset( $obj->term_id ) ) {
					$meta = vce_get_category_meta( $obj->term_id );
				}

				if ( $meta['use_sidebar'] != 'none' ) {
					$use_sidebar = ( $meta['use_sidebar'] == 'inherit' ) ? vce_get_option( $vce_template.'_use_sidebar' ) : $meta['use_sidebar'];
					if ( $use_sidebar ) {
						$sidebar = ( $meta['sidebar'] == 'inherit' ) ?  vce_get_option( $vce_template.'_sidebar' ) : $meta['sidebar'];
						$sticky_sidebar = ( $meta['sticky_sidebar'] == 'inherit' ) ?  vce_get_option( $vce_template.'_sticky_sidebar' ) : $meta['sticky_sidebar'];
					}
				}

			} else if ( $vce_template == 'single' ) {

				$meta = vce_get_post_meta( get_the_ID() );
				$use_sidebar = ( $meta['use_sidebar'] == 'inherit' ) ? vce_get_option( $vce_template.'_use_sidebar' ) : $meta['use_sidebar'];
				if ( $use_sidebar != 'none' ) {
					$sidebar = ( $meta['sidebar'] == 'inherit' ) ?  vce_get_option( $vce_template.'_sidebar' ) : $meta['sidebar'];
					$sticky_sidebar = ( $meta['sticky_sidebar'] == 'inherit' ) ?  vce_get_option( $vce_template.'_sticky_sidebar' ) : $meta['sticky_sidebar'];
				}

			} else if ( in_array( $vce_template, array( 'home_page', 'page', 'posts_page' ) ) ) {
				if ( $vce_template == 'posts_page' ) {
					$meta = vce_get_page_meta( get_option( 'page_for_posts' ) );
				} else {
					$meta = vce_get_page_meta( get_the_ID() );
				}


				$use_sidebar = ( $meta['use_sidebar'] == 'inherit' ) ? vce_get_option( 'page_use_sidebar' ) : $meta['use_sidebar'];
				if ( $use_sidebar != 'none' ) {
					$sidebar = ( $meta['sidebar'] == 'inherit' ) ?  vce_get_option( 'page_sidebar' ) : $meta['sidebar'];
					$sticky_sidebar = ( $meta['sticky_sidebar'] == 'inherit' ) ?  vce_get_option( 'page_sticky_sidebar' ) : $meta['sticky_sidebar'];
				}

			}

		$args = array(
			'use_sidebar' => $use_sidebar,
			'sidebar' => $sidebar,
			'sticky_sidebar' => $sticky_sidebar
		);


		return $args;
	}
endif;

/* Get post format icon */
if ( !function_exists( 'vce_post_format_icon' ) ):
	function vce_post_format_icon( $layout = '' ) {

		if ( vce_get_option( $layout.'_icon' ) ) {
			$format = get_post_format();

			$icons = array(
				'video' => 'fa-play',
				'audio' => 'fa-music',
				'image' => 'fa-camera',
				'gallery' => 'fa-picture-o'
			);

			//Allow plugins or child themes to modify icons
			$icons = apply_filters( 'vce_post_format_icons', $icons );

			if ( $format && array_key_exists( $format, $icons ) ) {
				return $icons[$format];
			}
		}

		return false;
	}
endif;


/* Get related posts for particular post */
if ( !function_exists( 'vce_get_related_posts' ) ):
	function vce_get_related_posts( $post_id = false ) {

		if ( empty( $post_id ) ) {
			$post_id = get_the_id();
		}

		$args['post_type'] = 'post';

		//Exclude current post form query
		$args['post__not_in'] = array( $post_id );

		//If previuos next posts active exclude them too
		if ( vce_get_option( 'show_prev_next' ) ) {
			$in_same_cat = vce_get_option( 'prev_next_cat' ) ? true : false;
			$prev = get_previous_post( $in_same_cat );

			if ( !empty( $prev ) ) {
				$args['post__not_in'][] = $prev->ID;
			}
			$next = get_next_post( $in_same_cat );
			if ( !empty( $next ) ) {
				$args['post__not_in'][] = $next->ID;
			}
		}

		$num_posts = absint( vce_get_option( 'related_limit' ) );
		if ( $num_posts > 100 ) {
			$num_posts = 100;
		}
		$args['posts_per_page'] = $num_posts;
		$args['orderby'] = vce_get_option( 'related_order' );

		if ( $args['orderby'] == 'views' && function_exists( 'ev_get_meta_key' ) ) {

			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = ev_get_meta_key();

		} else if ( strpos( $args['orderby'], 'reviews' ) !== false && vce_is_wp_review_active() ) {

				if ( strpos( $args['orderby'], 'user' ) !== false ) {

					$review_type = substr( $args['orderby'], 13, strlen( $args['orderby'] ) );

					$args['orderby'] = 'meta_value_num';
					$args['meta_key'] = 'wp_review_user_reviews';

					$args['meta_query'] = array(
						array(
							'key'     => 'wp_review_user_review_type',
							'value'   => $review_type,
						)
					);

				} else {

					$review_type = substr( $args['orderby'], 8, strlen( $args['orderby'] ) );

					$args['orderby'] = 'meta_value_num';
					$args['meta_key'] = 'wp_review_total';

					$args['meta_query'] = array(
						array(
							'key'     => 'wp_review_type',
							'value'   => $review_type,
						)
					);
				}

			}

		if ( $args['orderby'] == 'comments_number' ) {
			$args['orderby'] = 'comment_count';
		}

		if ( $args['orderby'] == 'title' ) {
			$args['order'] = 'ASC';
		}

		if ( $time_diff = vce_get_option( 'related_time' ) ) {
			$args['date_query'] = array( 'after' => date( 'Y-m-d', vce_calculate_time_diff( $time_diff ) ) );
		}

		if ( $type = vce_get_option( 'related_type' ) ) {
			switch ( $type ) {

			case 'cat':
				$cats = get_the_category( $post_id );
				$cat_args = array();
				if ( !empty( $cats ) ) {
					foreach ( $cats as $k => $cat ) {
						$cat_args[] = $cat->term_id;
					}
				}
				$args['category__in'] = $cat_args;
				break;

			case 'tag':
				$tags = get_the_tags( $post_id );
				$tag_args = array();
				if ( !empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						$tag_args[] = $tag->term_id;
					}
				}
				$args['tag__in'] = $tag_args;
				break;

			case 'cat_and_tag':
				$cats = get_the_category( $post_id );
				$cat_args = array();
				if ( !empty( $cats ) ) {
					foreach ( $cats as $k => $cat ) {
						$cat_args[] = $cat->term_id;
					}
				}
				$tags = get_the_tags( $post_id );
				$tag_args = array();
				if ( !empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						$tag_args[] = $tag->term_id;
					}
				}
				$args['tax_query'] = array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'category',
						'field'    => 'id',
						'terms'    => $cat_args,
					),
					array(
						'taxonomy' => 'post_tag',
						'field'    => 'id',
						'terms'    => $tag_args,
					)
				);
				break;

			case 'cat_or_tag':
				$cats = get_the_category( $post_id );
				$cat_args = array();
				if ( !empty( $cats ) ) {
					foreach ( $cats as $k => $cat ) {
						$cat_args[] = $cat->term_id;
					}
				}
				$tags = get_the_tags( $post_id );
				$tag_args = array();
				if ( !empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						$tag_args[] = $tag->term_id;
					}
				}
				$args['tax_query'] = array(
					'relation' => 'OR',
					array(
						'taxonomy' => 'category',
						'field'    => 'id',
						'terms'    => $cat_args,
					),
					array(
						'taxonomy' => 'post_tag',
						'field'    => 'id',
						'terms'    => $tag_args,
					)
				);
				break;

			case 'author':
				global $post;
				$author_id = isset( $post->post_author ) ? $post->post_author : 0;
				$args['author'] = $author_id;
				break;

			case 'default':
				break;
			}
		}

		$args = apply_filters( 'vce_modify_related_posts_query_args', $args );

		$related_query = new WP_Query( $args );

		return $related_query;
	}
endif;

/* Get options for selection of time dependent posts */
if ( !function_exists( 'vce_get_time_diff_opts' ) ) :
	function vce_get_time_diff_opts( $range = false ) {

		$options = array();

		$options['to'] = array(
			'0' => __( 'This moment', THEME_SLUG ),
			'-1 day' => __( '1 Day', THEME_SLUG ),
			'-3 days' => __( '3 Days', THEME_SLUG ),
			'-1 week' => __( '1 Week', THEME_SLUG ),
			'-1 month' => __( '1 Month', THEME_SLUG ),
			'-3 months' => __( '3 Months', THEME_SLUG ),
			'-6 months' => __( '6 Months', THEME_SLUG ),
			'-1 year' => __( '1 Year', THEME_SLUG )

		);

		$options['from'] = array(
			'-1 day' => __( '1 Day', THEME_SLUG ),
			'-3 days' => __( '3 Days', THEME_SLUG ),
			'-1 week' => __( '1 Week', THEME_SLUG ),
			'-1 month' => __( '1 Month', THEME_SLUG ),
			'-3 months' => __( '3 Months', THEME_SLUG ),
			'-6 months' => __( '6 Months', THEME_SLUG ),
			'-1 year' => __( '1 Year', THEME_SLUG ),
			'0' => __( 'All time', THEME_SLUG )
		);

		//Allow child themes or plugins to change these options
		$options = apply_filters( 'vce_modify_time_diff_opts', $options );

		if ( empty( $range ) ) {
			return $options;
		} else if ( array_key_exists( $range, $options ) ) {
				return $options[$range];
			} else {
			return array();
		}

	}
endif;

/* Get options for selection of post ordering */
if ( !function_exists( 'vce_get_post_order_opts' ) ) :
	function vce_get_post_order_opts() {

		$options = array(
			'date' => __( 'Date', THEME_SLUG ),
			'comment_count' => __( 'Number of comments', THEME_SLUG ),
			'views' => __( 'Number of views', THEME_SLUG ),
			'title' => __( 'Title (alphabetically)', THEME_SLUG ),
			'modified' => __( 'Modified date', THEME_SLUG ),
			'rand' => __( 'Random', THEME_SLUG )

		);

		if ( vce_is_wp_review_active() ) {
			$options['reviews_star'] = esc_html__( 'Author Reviews (stars)', THEME_SLUG );
			$options['reviews_point'] = esc_html__( 'Author Reviews (points)', THEME_SLUG );
			$options['reviews_percentage'] = esc_html__( 'Author Reviews (percentage)', THEME_SLUG );

			$options['user_reviews_star'] = esc_html__( 'User Reviews (stars)', THEME_SLUG );
			$options['user_reviews_point'] = esc_html__( 'User Reviews (points)', THEME_SLUG );
			$options['user_reviews_percentage'] = esc_html__( 'User Reviews (percentage)', THEME_SLUG );
		}

		//Allow child themes or plugins to change these options
		$options = apply_filters( 'vce_modify_post_order_opts', $options );

		return $options;
	}
endif;

/* Get featured area posts and arguments */
if ( !function_exists( 'vce_get_fa_args' ) ) :
	function vce_get_fa_args() {

		if ( is_category() ) {

			global $vce_cat_fa_args;
			return $vce_cat_fa_args;

		} else if ( is_page_template( 'template-modules.php' ) ) {

				return vce_get_fa_home_args();
			}
	}
endif;

/* Get featured area posts and arguments for modules page */
if ( !function_exists( 'vce_get_fa_home_args' ) ) :
	function vce_get_fa_home_args() {

		$args = array( 'use_fa' => false );

		//Check home page featured area options
		$obj = get_queried_object();
		$meta = vce_get_page_meta( $obj->ID );
		$fa_layout = $meta['fa_layout'];

		if ( $fa_layout ) {

			$q_args['post_type'] = $meta['fa_post_type'];
			$post_type_with_taxonomies = vce_get_post_type_with_taxonomies( $meta['fa_post_type'] );
			$q_args['ignore_sticky_posts'] = 1;

			if ( !empty( $meta['fa_manual'] ) ) {
				$q_args['posts_per_page'] = absint( count( $meta['fa_manual'] ) );
				$q_args['orderby'] =  'post__in';
				$q_args['post__in'] =  $meta['fa_manual'];
				$q_args['post_type'] = array_keys( get_post_types( array( 'public' => true ) ) ); //support all existing public post types

			} else {
				$num_posts = absint( $meta['fa_limit'] );
				$q_args['posts_per_page'] = $num_posts;
				$q_args['orderby'] = $meta['fa_order'];

				if ( !empty( $meta['fa_exclude_by_id'] ) ) {
					$q_args['post__not_in'] = $meta['fa_exclude_by_id'];
				}

				if ( $q_args['orderby'] == 'views' && function_exists( 'ev_get_meta_key' ) ) {

					$q_args['orderby'] = 'meta_value_num';
					$q_args['meta_key'] = ev_get_meta_key();

				} else if ( strpos( $q_args['orderby'], 'reviews' ) !== false && vce_is_wp_review_active() ) {

						if ( strpos( $q_args['orderby'], 'user' ) !== false ) {

							$review_type = substr( $q_args['orderby'], 13, strlen( $q_args['orderby'] ) );

							$q_args['orderby'] = 'meta_value_num';
							$q_args['meta_key'] = 'wp_review_user_reviews';

							$q_args['meta_query'] = array(
								array(
									'key'     => 'wp_review_user_review_type',
									'value'   => $review_type,
								)
							);

						} else {

							$review_type = substr( $q_args['orderby'], 8, strlen( $q_args['orderby'] ) );

							$q_args['orderby'] = 'meta_value_num';
							$q_args['meta_key'] = 'wp_review_total';

							$q_args['meta_query'] = array(
								array(
									'key'     => 'wp_review_type',
									'value'   => $review_type,
								)
							);
						}

					}

				if ( $q_args['orderby'] == 'comments_number' ) {
					$q_args['orderby'] = 'comment_count';
				}

				$q_args['order'] = $meta['fa_sort'];

				if ( $meta['fa_time'] ) {
					$q_args['date_query'] = array( 'after' => date( 'Y-m-d', vce_calculate_time_diff( $meta['fa_time'] ) ) );
				}

				if ( !empty( $post_type_with_taxonomies->taxonomies ) ) {
					foreach ( $post_type_with_taxonomies->taxonomies as $taxonomy ) {
						$taxonomy_id = vce_patch_taxonomy_id( $taxonomy['id'] );

						if ( empty( $meta['fa_'. $taxonomy_id . '_inc_exc'] ) || empty( $meta['fa_'. $taxonomy_id] ) ) {
							continue;
						}

						$operator = $meta['fa_'. $taxonomy_id . '_inc_exc'] === 'not_in' ? 'NOT IN' : 'IN';

						if ( $taxonomy['hierarchical'] ) {

							$q_args['tax_query'][] = array(
								'taxonomy' => $taxonomy['id'],
								'field'    => 'id',
								'terms'    => $meta['fa_' . $taxonomy_id],
								'operator' => $operator,
								'include_children' => boolval( $meta['fa_' . $taxonomy_id . '_child'] )
							);
						}else {
							$q_args['tax_query'][] = array(
								'taxonomy' => $taxonomy['id'],
								'field'    => 'id',
								'terms'    => vce_get_tax_term_id_by_slug( explode( ',', $meta['fa_'. $taxonomy_id] ), $taxonomy['id'] ),
								'operator' => $operator
							);
						}
					}
				}

				if ( !empty( $meta['fa_author'] ) ) {
					$q_args['author__'.$meta['fa_author_inc_exc']] = $meta['fa_author'];
				}

			}

			$q_args = apply_filters( 'vce_modify_fa_query_args', $q_args );

			$args['fa_posts'] = new WP_Query( $q_args );

			if ( !is_wp_error( $args['fa_posts'] ) && !empty( $args['fa_posts'] ) ) {

				$num_posts = count( $args['fa_posts']->posts );
				$fa_layout = explode( "_", $fa_layout );
				$args['both'] = count( $fa_layout ) == 2 ? true: false;
				$args['full'] = $fa_layout[0] == 'full' ? true: false;
				$args['full_slider'] = ( $num_posts > 1 && !isset( $fa_layout[1] ) && $fa_layout[0] == 'full' ) ? true : false;
				$args['grid'] = in_array( 'grid', $fa_layout ) ? true: false;
				$args['big_grid'] = in_array( 'big-grid', $fa_layout ) ? true: false;
				$args['use_fa'] = true;

				if ( $meta['fa_exclude'] ) {
					global $vce_fa_home_posts;
					$vce_fa_home_posts = array();
					foreach ( $args['fa_posts']->posts as $p ) {
						$vce_fa_home_posts[] = $p->ID;
					}
				}

			}

			wp_reset_postdata();
		}

		//print_r($q_args);

		return $args;
	}
endif;

/* Get featured area posts and arguments for category */
if ( !function_exists( 'vce_get_fa_cat_args' ) ) :
	function vce_get_fa_cat_args() {

		$args = array( 'use_fa' => false );

		//Check category featured area options

		$obj = get_queried_object();
		$meta = vce_get_category_meta( $obj->term_id );

		if ( $meta['fa_layout'] == 'inherit' ) {
			$fa_layout = vce_get_option( 'category_fa' ) ? vce_get_option( 'category_fa_layout' ) : false;
			$num_posts = vce_get_option( 'category_fa' ) ? vce_get_option( 'category_fa_limit' ) : false;
		} else {
			$fa_layout = $meta['fa_layout'];
			$num_posts = $meta['fa_limit'];
		}


		if ( $fa_layout ) {

			$q_args['post_type'] = 'post';
			$q_args['posts_per_page'] = $num_posts;
			$q_args['orderby'] = vce_get_option( 'category_fa_order' );

			if ( $q_args['orderby'] == 'views' && function_exists( 'ev_get_meta_key' ) ) {
				$q_args['orderby'] = 'meta_value_num';
				$q_args['meta_key'] = ev_get_meta_key();
			}

			if ( $q_args['orderby'] == 'comments_number' ) {
				$q_args['orderby'] = 'comment_count';
			}

			if ( $q_args['orderby'] == 'title' ) {
				$q_args['order'] = 'ASC';
			}

			if ( $time_diff = vce_get_option( 'category_fa_time' ) ) {
				$q_args['date_query'] = array( 'after' => date( 'Y-m-d', vce_calculate_time_diff( $time_diff ) ) );
			}

			$q_args['cat'] = $obj->term_id;

			$q_args = apply_filters( 'vce_modify_fa_cat_query_args', $q_args );

			$args['fa_posts'] = new WP_Query( $q_args );

			if ( !is_wp_error( $args['fa_posts'] ) && !empty( $args['fa_posts'] ) ) {

				$num_posts = count( $args['fa_posts']->posts );

				$fa_layout = explode( "_", $fa_layout );
				$args['both'] = count( $fa_layout ) == 2 ? true: false;
				$args['full'] = $fa_layout[0] == 'full' ? true: false;
				$args['full_slider'] = ( $num_posts > 1 && !isset( $fa_layout[1] ) && $fa_layout[0] == 'full' ) ? true : false;
				$args['grid'] = in_array( 'grid', $fa_layout ) ? true: false;
				$args['big_grid'] = in_array( 'big-grid', $fa_layout ) ? true: false;

				$args['use_fa'] = true;
			}

			if ( vce_get_option( 'category_fa_hide_on_pages' ) && absint( get_query_var( 'paged' ) > 1 ) ) {
				$args['use_fa'] = false;
				//Show only on first page
			}
		}

		//print_r($q_args);

		return $args;
	}
endif;


/**
 * Get branding
 *
 * Returns HTML of logo or website title based on theme options
 *
 * @return string HTML
 * @since  1.0
 */

if ( !function_exists( 'vce_get_branding' ) ):
    function vce_get_branding() {
    	global $vce_logo_used;

        //Get all logos
        $logo = vce_get_option_media( 'logo' );
        $logo_retina = vce_get_option_media( 'logo_retina' );
        $logo_mini = vce_get_option_media( 'logo_mobile' );;
        $logo_mini_retina = vce_get_option_media( 'logo_mobile_retina' );
        $logo_sticky = vce_get_option_media( 'sticky_header_logo' );

        $logo_class = ''; //if there is a logo image we use special class

        if ( empty( $logo_mini ) ) {
            $logo_mini = $logo;
        }

       if ( $vce_logo_used && !empty( $logo_sticky ) ) {
            $logo = $logo_sticky;
            $logo_mini = $logo_sticky;
            $logo_retina = '';
            $logo_mini_retina = '';
        }

        if ( empty( $logo ) ) {

            $brand =  get_bloginfo( 'name' );
            
        } else {
            $brand = '<picture class="vce-logo">';
            $brand .= '<source media="(min-width: 1024px)" srcset="'.esc_attr( $logo );

            if ( !empty( $logo_retina ) ) {
                $brand .= ', '.esc_attr( $logo_retina ).' 2x';
            }

            $brand .= '">';
            $brand .= '<source srcset="'.esc_attr( $logo_mini );

            if ( !empty( $logo_mini_retina ) ) {
                $brand .= ', '.esc_attr( $logo_mini_retina ).' 2x';
            }

            $brand .= '">';
            $brand .= '<img src="'.esc_attr( $logo ).'" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
            $brand .= '</picture>';

            $logo_class = 'has-logo';
        }
 

        $element = is_front_page() && !$vce_logo_used ? 'h1' : 'span';
        $url = vce_get_option( 'logo_custom_url' ) ? vce_get_option( 'logo_custom_url' ) : home_url( '/' );
        $site_desc = !$vce_logo_used && vce_get_option('header_description') ? '<span class="site-description">' . get_bloginfo( 'description' ) . '</span>' : '';

        $output = '<' . esc_attr( $element ) . ' class="site-title"><a href="' . esc_url( $url ) . '" rel="home" class="'.esc_attr( $logo_class ).'">' . $brand . '</a></' . esc_attr( $element ) . '>' . $site_desc;

        $vce_logo_used = true;

        return apply_filters( 'vce_modify_branding', $output );

    }
endif;



/* Compares two values and sanitazes 0 */
if ( !function_exists( 'vce_compare' ) ):
	function vce_compare( $a, $b ) {
		return (string) $a === (string) $b;
	}
endif;




/* Check is post is paginated */
if ( !function_exists( 'vce_is_paginated_post' ) ):
	function vce_is_paginated_post() {

		global $multipage;
		return 0 !== $multipage;

	}
endif;

/* Get settings to pass to main JS file */
if ( !function_exists( 'vce_get_js_settings' ) ):
	function vce_get_js_settings() {
		global $vce_rtl;
		$js_settings = array();
		$js_settings['sticky_header'] = vce_get_option( 'sticky_header' ) ? true : false;
		$js_settings['sticky_header_offset'] = absint( vce_get_option( 'sticky_header_offset' ) );
		$js_settings['sticky_header_logo'] = vce_get_option_media( 'sticky_header_logo' );
		$js_settings['logo'] = vce_get_option_media( 'logo' );
		$js_settings['logo_retina'] = vce_get_option_media( 'logo_retina' );
		$js_settings['logo_mobile'] = vce_get_option_media( 'logo_mobile' );
		$js_settings['logo_mobile_retina'] = vce_get_option_media( 'logo_mobile_retina' );
		$js_settings['rtl_mode'] = $vce_rtl ? 1: 0;
		$protocol = is_ssl() ? 'https://' : 'http://';
		$js_settings['ajax_url'] = admin_url( 'admin-ajax.php', $protocol );
		$js_settings['ajax_wpml_current_lang'] = apply_filters( 'wpml_current_language', NULL );
		$js_settings['ajax_mega_menu'] = vce_get_option( 'ajax_mega_menu' ) ? true : false;
		$js_settings['mega_menu_slider'] = vce_get_option( 'mega_menu_slider' ) ? true : false;
		$js_settings['mega_menu_subcats'] = vce_get_option( 'mega_menu_subcats' ) ? true : false;
		$js_settings['lay_fa_grid_center'] = vce_get_option( 'lay_fa_grid_center' ) ? true : false;
		$js_settings['full_slider_autoplay'] = vce_get_option( 'lay_fa_big_autoplay' ) ? absint( vce_get_option( 'lay_fa_big_autoplay' ) ) * 1000 : false;
		$js_settings['grid_slider_autoplay'] = vce_get_option( 'lay_fa_grid_autoplay' ) ? absint( vce_get_option( 'lay_fa_grid_autoplay' ) ) * 1000 : false;
		$js_settings['grid_big_slider_autoplay'] = vce_get_option( 'lay_fa_grid_big_autoplay' ) ? absint( vce_get_option( 'lay_fa_grid_big_autoplay' ) ) * 1000 : false;
		$js_settings['fa_big_opacity'] = vce_get_option( 'lay_fa_big_opc' );
		$js_settings['top_bar_mobile'] = vce_get_option( 'top_bar_mobile' );
		$js_settings['top_bar_mobile_group'] = vce_get_option( 'top_bar_mobile_group' );
		$js_settings['top_bar_more_link'] = __vce( 'more' );

		return $js_settings;
	}
endif;

/* Parse font option */
if ( !function_exists( 'vce_get_font_option' ) ):
	function vce_get_font_option( $option = false ) {

		$font = vce_get_option( $option );
		$native_fonts = vce_get_native_fonts();
		if ( !in_array( $font['font-family'], $native_fonts ) ) {
			$font['font-family'] = "'".$font['font-family']."'";
		}

		return $font;
	}
endif;

/* Parse background option */
if ( !function_exists( 'vce_get_bg_styles' ) ):
	function vce_get_bg_styles( $option = false ) {

		$style = vce_get_option( $option );
		$css = '';

		if ( ! empty( $style ) && is_array( $style ) ) {
			foreach ( $style as $key => $value ) {
				if ( ! empty( $value ) && $key != "media" ) {
					if ( $key == "background-image" ) {
						$css .= $key . ":url('" . $value . "');";
					} else {
						$css .= $key . ":" . $value . ";";
					}
				}
			}
		}


		return $css;
	}
endif;

/* Get topbar items */
if ( !function_exists( 'vce_get_topbar_items' ) ):
	function vce_get_topbar_items() {
		$items = array(
			'0' => __( 'None', THEME_SLUG ),
			'top-navigation' => __( 'Top navigation menu', THEME_SLUG ),
			'social-menu' => __( 'Social menu', THEME_SLUG ),
			'search-bar' => __( 'Search form', THEME_SLUG ),
		);

		$items = apply_filters( 'vce_modify_topbar_items', $items );
		return $items;
	}
endif;

/* Get copyright bar items */
if ( !function_exists( 'vce_get_copybar_items' ) ):
	function vce_get_copybar_items() {
		$items = array(
			'0' => __( 'None', THEME_SLUG ),
			'footer-menu' => __( 'Footer menu' , THEME_SLUG ),
			'social-menu' => __( 'Social menu', THEME_SLUG ),
			'copyright-text' =>  __( 'Copyright text', THEME_SLUG )
		);

		$items = apply_filters( 'vce_modify_copybar_items', $items );
		return $items;
	}
endif;

/* 	Update theme option function */
if ( !function_exists( 'vce_read_time' ) ):
	function vce_read_time( $text ) {

		if ( !vce_get_option( 'multibyte_rtime' ) ) {
			//$words = str_word_count( wp_strip_all_tags( $text ) );
			$words = count( preg_split( "/[\n\r\t ]+/", wp_strip_all_tags( $text ) ) );

		} else {
			//$words = count( explode( ' ', html_entity_decode( mb_convert_encoding( $text, 'HTML-ENTITIES', 'UTF-8' ), ENT_QUOTES, 'UTF-8' ) ) );
			$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', wp_strip_all_tags( $text ) ), ' ' );
			preg_match_all( '/./u', $text, $words_array );
			$words = count( $words_array[0] );
		}

		$number_words_per_minute = vce_get_option( 'words_read_per_minute' );
		$number_words_per_minute = !empty( $number_words_per_minute ) ? absint( $number_words_per_minute ) : 200;

		if ( !empty( $words ) ) {
			$time_in_minutes = ceil( $words / $number_words_per_minute );
			return $time_in_minutes;
		}
		return false;
	}
endif;

/* Get update notification */
if ( !function_exists( 'vce_get_update_notification' ) ):
	function vce_get_update_notification() {
		$current = get_site_transient( 'update_themes' );
		$message_html = '';
		if ( isset( $current->response['voice'] ) ) {
			$message_html = '<span class="update-message">New update available!</span>
				<span class="update-actions">Version '.$current->response['voice']['new_version'].': <a href="http://mekshq.com/docs/voice-change-log" target="blank">See what\'s new</a><a href="'.admin_url( 'update-core.php' ).'">Update</a></span>';
		} else {
			$message_html = '<a class="theme-version-label" href="https://mekshq.com/docs/voice-change-log" target="blank">Version '.THEME_VERSION.'</a>';
		}

		return $message_html;
	}
endif;

/* Check if WooCommerce is activated */
if ( !function_exists( 'vce_is_woocommerce_active' ) ):
	function vce_is_woocommerce_active() {

		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;

/* Check if bbPress is activated */
if ( !function_exists( 'vce_is_bbpress_active' ) ):
	function vce_is_bbpress_active() {

		if ( in_array( 'bbpress/bbpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}
		return false;
	}
endif;


/* Check if WP Review plugin is active */
if ( !function_exists( 'vce_is_wp_review_active' ) ):
	function vce_is_wp_review_active() {

		if ( in_array( 'wp-review/wp-review.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;


/**
 * Support for Co-Authors Plus Plugin
 * Check if plugin is activated
 *
 * @since  2.3
 */

if ( !function_exists( 'voice_is_co_authors_active' ) ):
	function voice_is_co_authors_active() {

		if ( in_array( 'co-authors-plus/co-authors-plus.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;


/* Calculate time difference based on timestring */
if ( !function_exists( 'vce_calculate_time_diff' ) ) :
	function vce_calculate_time_diff( $timestring ) {

		$now = current_time( 'timestamp' );

		switch ( $timestring ) {
		case '-1 day' : $time = $now - DAY_IN_SECONDS; break;
		case '-3 days' : $time = $now - ( 3 * DAY_IN_SECONDS ); break;
		case '-1 week' : $time = $now - WEEK_IN_SECONDS; break;
		case '-1 month' : $time = $now - ( YEAR_IN_SECONDS / 12 ); break;
		case '-3 months' : $time = $now - ( 3 * YEAR_IN_SECONDS / 12 ); break;
		case '-6 months' : $time = $now - ( 6 * YEAR_IN_SECONDS / 12 ); break;
		case '-1 year' : $time = $now - ( YEAR_IN_SECONDS ); break;
		default : $time = $now;
		}

		return $time;
	}
endif;

/**
 * Get term IDs by term slugs for specific taxonomy
 *
 * @param array   $slugs List of tag slugs
 * @param string  $tax   Taxonomy name
 * @return array List IDs
 * @since  2.2
 */

if ( !function_exists( 'vce_get_tax_term_id_by_slug' ) ):
	function vce_get_tax_term_id_by_slug( $slugs, $tax = 'post_tag' ) {

		if ( empty( $slugs ) ) {
			return '';
		}

		$ids = array();

		foreach ( $slugs as $slug ) {
			$tag = get_term_by( 'slug', trim( $slug ), $tax );
			if ( !empty( $tag ) && isset( $tag->term_id ) ) {
				$ids[] = $tag->term_id;
			}
		}

		return $ids;

	}
endif;


/**
 * Get authors IDs by author username
 *
 * @param array   $slugs List of term slugs
 * @return array List of author IDs
 * @since  2.3  ( R.J. )
 */

if ( !function_exists( 'vce_get_authors_id_by_username' ) ):
	function vce_get_authors_id_by_username( $names ) {

		if ( empty( $names ) ) {
			return '';
		}
		$names = explode( ",", $names );
		$ids = array();
		foreach ( $names as $name ) {

			$meta = get_user_by( 'login', $name );
			if ( !empty( $meta ) ) {
				$ids[] = $meta->ID;
			}
		}

		return $ids;

	}
endif;


/**
 * Get authors username by author ID
 *
 * @param array   $slugs List of term slugs
 * @return array List of author IDs
 * @since  2.3 ( R.J. )
 */

if ( !function_exists( 'vce_get_authors_username_by_id' ) ):
	function vce_get_authors_username_by_id( $ids ) {

		if ( empty( $ids ) ) {
			return '';
		}

		$names = array();

		foreach ( $ids as $id ) {

			$meta = get_user_by( 'ID', $id );
			if ( !empty( $meta ) ) {
				$names[] = $meta->user_login;
			}
		}

		$names = implode( ",", $names );
		return $names;

	}
endif;




/**
 * Get module defaults CPT
 *
 * @return array Default arguments of a module
 * @since  2.5
 */

if ( !function_exists( 'vce_get_module_defaults_cpt' ) ):
	function vce_get_module_defaults_cpt( $key = '' ) {

		$defaults = array();

		$custom_post_types = vce_get_custom_post_types();

		if ( !empty( $custom_post_types ) ) {
			foreach ( $custom_post_types as $custom_post_type ) {
				$defaults[$custom_post_type] = array(
					'type' => $custom_post_type,
					'type_name' => $custom_post_type,
					'cpt' => 1,
					'layout' => 'c',
					'title' => '',
					'hide_title' => 0,
					'title_link' => '',
					'limit' => 4,
					'manual' => array(),
					'time' => 0,
					'timeto' => 0,
					'order' => 'date',
					'sort' => 'DESC',
					'top_layout' => 0,
					'top_limit' => 2,
					'one_column' => 0,
					'action' => '0',
					'pagination' => 'load-more',
					'action_link_text' => 'View all',
					'action_link_url' => 'http://',
					'autoplay' => '',
					'exclude' => 0,
					'css_class' => '',
					'author' => array(),
					'author_inc_exc' => 'in',
					'exclude_by_id' => array(),
					'tax' => array(),
					'active' => 1
				);
			}
			$custom_post_type_taxonomies = vce_get_taxonomies( $custom_post_type );
			if ( !empty( $custom_post_type_taxonomies ) ) {
				foreach ( $custom_post_type_taxonomies as $custom_post_type_taxonomy ) {
					$defaults[$custom_post_type][$custom_post_type_taxonomy['id'] . '_inc_exc'] = 'in';
				}
			}
		}

		if ( !empty( $key ) && array_key_exists( $key, $defaults ) ) {
			return $defaults[$key];
		}


		return $defaults;

	}
endif;

/**
 * Get module options CPT
 *
 * @return array Options for sepcific module
 * @since  2.5
 */

if ( !function_exists( 'vce_get_module_options_cpt' ) ):
	function vce_get_module_options_cpt() {

		$options = array();

		$custom_post_types = vce_get_custom_post_types();

		if ( !empty( $custom_post_types ) ) {
			foreach ( $custom_post_types as $custom_post_type ) {
				$options[$custom_post_type] = array(
					'layouts' => vce_get_main_layouts(),
					'starter_layouts' => vce_get_main_layouts( false, true ),
					'order' => vce_get_post_order_opts(),
					'time' => vce_get_time_diff_opts(),
					'actions' => vce_get_module_actions(),
					'paginations' => vce_get_pagination_layouts(),
					'taxonomies' => vce_get_taxonomies( $custom_post_type )
				);
			}
		}

		return $options;

	}
endif;

/**
 * Get category module defaults
 *
 * @return array Default arguments of a module
 * @since  2.5
 */
if ( !function_exists( 'vce_get_module_defaults_category' ) ):
	function vce_get_module_defaults_category() {

		$defaults = array(
			'type' => 'category',
			'title' => '',
			'hide_title' => 0,
			'title_link' => '',
			'layout' => 'c',
			'top_layout' => 0,
			'top_limit' => 0,
			'display_count' => 1,
			'count_label' => __( 'articles', THEME_SLUG ),
			'cat' => array(),
			'action' => '0',
			'autoplay' => '',
			'css_class' => '',
			'active' => 1,
		);

		return $defaults;

	}
endif;

/**
 * Get category module options
 *
 * @return array Options for sepcific module
 * @since  2.5
 */
if ( !function_exists( 'vce_get_module_options_category' ) ):
	function vce_get_module_options_category() {

		$options = array(
			'layouts' => vce_get_category_layouts(),
			'cats' => get_categories( array( 'hide_empty' => false, 'number' => 0 ) ),
			'actions' => vce_get_module_actions( array( 'pagination', 'link' ) )
		);

		return $options;

	}
endif;

/**
 * Get post module defaults
 *
 * @return array Default arguments of a module
 * @since  2.5
 */
if ( !function_exists( 'vce_get_module_defaults_posts' ) ):
	function vce_get_module_defaults_posts() {

		$defaults = array(
			'type' => 'posts',
			'layout' => 'c',
			'title' => '',
			'hide_title' => 0,
			'title_link' => '',
			'limit' => 4,
			'manual' => array(),
			'cat' => array(),
			'cat_inc_exc' => 'in',
			'time' => 0,
			'timeto' => 0,
			'order' => 'date',
			'sort' => 'DESC',
			'top_layout' => 0,
			'top_limit' => 2,
			'one_column' => 0,
			'action' => '0',
			'pagination' => 'load-more',
			'action_link_text' => 'View all',
			'action_link_url' => 'http://',
			'cat_child' => 0,
			'tag' => '',
			'tag_inc_exc' => 'in',
			'autoplay' => '',
			'exclude' => 0,
			'css_class' => '',
			'author' => array(),
			'author_inc_exc' => 'in',
			'exclude_by_id' => array(),
			'active' => 1
		);

		return $defaults;



	}
endif;

/**
 * Get post module options
 *
 * @return array Options for sepcific module
 * @since  2.5
 */
if ( !function_exists( 'vce_get_module_options_posts' ) ):
	function vce_get_module_options_posts() {

		$options = array(
			'layouts' => vce_get_main_layouts(),
			'starter_layouts' => vce_get_main_layouts( false, true ),
			'cats' => get_categories( array( 'hide_empty' => false, 'number' => 0 ) ),
			'order' => vce_get_post_order_opts(),
			'time' => vce_get_time_diff_opts(),
			'actions' => vce_get_module_actions(),
			'paginations' => vce_get_pagination_layouts(),
		);

		return $options;

	}
endif;

/**
 * Get text module defaults
 *
 * @return array Default arguments of a module
 * @since  2.5
 */
if ( !function_exists( 'vce_get_module_defaults_text' ) ):
	function vce_get_module_defaults_text() {

		$defaults = array(
			'type' => 'blank',
			'title' => '',
			'one_column' => 0,
			'hide_title' => 0,
			'title_link' => '',
			'content' => '',
			'css_class' => '',
			'active' => 1
		);

		return $defaults;

	}
endif;



/**
 * Sort option items
 *
 * Use this function to properly order sortable options like in categories and series module
 *
 * @param unknown $items    Array of items
 * @param unknown $selected Array of IDs of currently selected items
 * @return array ordered items
 * @since  1.0
 */

if ( !function_exists( 'vce_sort_option_items' ) ):
	function vce_sort_option_items( $items, $selected, $field = 'term_id' ) {

		if ( empty( $selected ) ) {
			return $items;
		}

		$new_items = array();
		$temp_items = array();
		$temp_items_ids = array();

		foreach ( $selected as $selected_item_id ) {

			foreach ( $items as $item ) {
				if ( $selected_item_id == $item->$field ) {
					$new_items[] = $item;
				} else {
					if ( !in_array( $item->$field, $selected ) && !in_array( $item->$field, $temp_items_ids ) ) {
						$temp_items[] = $item;
						$temp_items_ids[] = $item->$field;
					}
				}
			}

		}

		$new_items = array_merge( $new_items, $temp_items );

		return $new_items;
	}
endif;


/**
 * Get all public custom post types
 *
 * @return array List of slugs
 * @since  2.3.1
 */

if ( !function_exists( 'vce_get_custom_post_types' ) ):
	function vce_get_custom_post_types( $raw = false ) {

		$custom_post_types =  get_post_types( array( 'public' => true, '_builtin' => false ), 'object' );

		if ( !empty( $custom_post_types ) ) {

			$exclude = array( 'topic', 'forum', 'guest-author', 'reply' );

			foreach ( $custom_post_types as $i => $obj ) {
				if ( in_array( $obj->name, $exclude ) ) {
					unset( $custom_post_types[$i] );
				}
			}

			if ( !$raw ) {
				$custom_post_types = array_keys( $custom_post_types );
			}
		}
		$custom_post_types =  apply_filters( 'vce_modify_custom_post_types_list', $custom_post_types );

		return $custom_post_types;
	}
endif;

/**
 * Get all taxonomies for custom post type
 *
 * @param unknown $cpt Custom post type ID
 * @return array List of custom post types and taxonomies
 * @since  2.3.1
 */
if ( !function_exists( 'vce_get_taxonomies' ) ) :
	function vce_get_taxonomies( $cpt ) {

		$taxonomies = get_taxonomies( array(
				'object_type' => array( $cpt ),
				'public' => true,
				'show_ui' => true
			),
			'object' );

		$output = array();

		foreach ( $taxonomies as $taxonomy ) {

			$tax = array();
			$tax['id'] = $taxonomy->name;
			$tax['name'] = $taxonomy->label;
			$tax['hierarchical'] = $taxonomy->hierarchical;
			if ( $tax['hierarchical'] ) {
				$tax['terms'] = get_terms( $taxonomy->name, array( 'hide_empty' => false ) ); //false for testing - change to true
			}

			$output[] = $tax;
		}

		return $output;
	}
endif;

/**
 * Get term IDs by term names for specific taxonomy
 *
 * @param array   $names List of term names
 * @param string  $tax   Taxonomy name
 * @return array List of term IDs
 * @since  2.3.1
 */

if ( !function_exists( 'vce_get_tax_term_id_by_name' ) ):
	function vce_get_tax_term_id_by_name( $names, $tax = 'post_tag' ) {

		if ( empty( $names ) ) {
			return '';
		}

		if ( !is_array( $names ) ) {
			$names = explode( ",", $names );
		}

		$ids = array();

		foreach ( $names as $name ) {
			$tag = get_term_by( 'name', trim( $name ), $tax );
			if ( !empty( $tag ) && isset( $tag->term_id ) ) {
				$ids[] = $tag->term_id;
			}
		}
		return $ids;

	}
endif;

/**
 * Get term names by term id for specific taxonomy
 *
 * @param array   $names List of term ids
 * @param string  $tax   Taxonomy name
 * @return array List of term names
 * @since  2.3.1
 */

if ( !function_exists( 'vce_get_tax_term_name_by_id' ) ):
	function vce_get_tax_term_name_by_id( $ids, $tax = 'post_tag' ) {

		if ( empty( $ids ) ) {
			return '';
		}

		$names = array();

		foreach ( $ids as $id ) {
			$tag = get_term_by( 'id', trim( $id ), $tax );
			if ( !empty( $tag ) && isset( $tag->name ) ) {
				$names[] = $tag->name;
			}
		}

		$names = implode( ',', $names );

		return $names;

	}
endif;


/**
 * Create Display Options Metabox
 *
 */
if ( !function_exists( 'vce_post_display_option' ) ) :
	function vce_post_display_option( $option_name, $selected = 'inherit' ) {
		$options = array( 'inherit' => __( 'Inherit', THEME_SLUG ), 'on' => __( 'On', THEME_SLUG ), 'off' => __( 'Off', THEME_SLUG ) );
?>
		<select name="vce[display][<?php echo $option_name; ?>]" class="vce-single-display-opt">
            <?php foreach ( $options as $val => $label ): ?>
				<option value="<?php echo $val; ?>" <?php selected( $selected, $val, true ); ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
		</select>
        <?php
	}
endif;

/**
 * Get Admin JS localized variables
 *
 * Function creates list of variables from theme to pass
 * them to global JS variable so we can use it in JS files
 *
 * @since  2.7
 *
 * @return array List of JS settings
 */
if ( !function_exists( 'vce_get_admin_js_settings' ) ):
	function vce_get_admin_js_settings() {

		$js_settings = array();
		$js_settings['ajax_url'] = admin_url( 'admin-ajax.php' );
		$js_settings['is_gutenberg'] = vce_is_gutenberg_page();
		return $js_settings;
	}
endif;

/**
 * Trim text characters with UTF-8
 * for adding to html attributes it's not breaking the code and
 * you are able to have all the kind of characters (Japanese, Cyrillic, German, French, etc.)
 *
 * @param unknown $text
 * @since  2.7
 */
if ( !function_exists( 'vce_esc_text' ) ):
	function vce_esc_text( $text ) {
		return rawurlencode( html_entity_decode( wp_kses( $text, null ), ENT_COMPAT, 'UTF-8' ) );
	}
endif;

/**
 * Trims URL with special characters like used in (Japanese, Cyrillic, German, French, etc.)
 *
 * @param unknown $url
 * @since  2.7
 */
if ( !function_exists( 'vce_esc_url' ) ):
	function vce_esc_url( $url ) {
		return rawurlencode( esc_url( esc_attr( $url ) ) );
	}
endif;

/**
 * Return category image or if is not set category image return last post feature image
 *
 * @since  1.7
 *
 * @return mixed html
 */

if ( !function_exists( 'vce_get_category_featured_image' ) ) :
	function vce_get_category_featured_image( $size, $cat_id ) {

		if ( empty( $cat_id ) ) {
			$cat_id = get_queried_object_id();
		}

		$img_url = vce_get_category_meta( $cat_id, 'image' );

		$img_html = '';

		if ( !empty( $img_url ) ) {
			$img_id = vce_get_image_id_by_url( $img_url );
			$img_html = wp_get_attachment_image( $img_id, $size );
			if ( empty( $img_html ) ) {
				$img_html = '<img src="'.esc_url( $img_url ).'"/>';
			}
		}

		if ( empty( $img_html )  ) {
			$first_post = vce_get_first_post_in_category( $cat_id );
			$post_id = false;
			if ( !empty( $first_post ) && isset( $first_post->ID ) ) {
				$post_id = $first_post->ID;
			}
			$img_html = vce_featured_image( $size, $post_id );
		}

		return wp_kses_post( $img_html );
	}
endif;

/**
 * Get first post in category
 *
 * @since  1.7
 * @param unknown $category_id
 * @return object WP Query Object
 */

if ( !function_exists( 'vce_get_first_post_in_category' ) ) :
	function vce_get_first_post_in_category( $category_id ) {

		$args = array(
			'post_type' => 'post',
			'posts_per_page' => 1,
			'category__in' => array( $category_id ),
		);

		$query = new WP_Query( $args );

		if ( !$query->have_posts() ) {
			return false;
		}

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_obj = $query->post;
		}

		wp_reset_postdata();
		return $post_obj;
	}
endif;



/**
 * Get primary category if Yoast is enabled and primary category is set
 *
 * @since  2.8
 *
 * @return mixed|html
 */

if ( !function_exists( 'vce_get_primary_category' ) ) :
	function vce_get_primary_category() {

		if ( !vce_is_yoast_active() ) {
			return false;
		}

		global $post;

		$primary_category = !is_single() ? vce_get_option( 'primary_category' ) : false;
		$primary_term_id = $primary_category ? get_post_meta( $post->ID, '_yoast_wpseo_primary_category', true ) : false;

		if ( !$primary_category && isset( $primary_term_id ) && empty( $primary_term_id ) ) {
			return false;
		}

		$term = get_term( $primary_term_id );

		if ( is_wp_error( $term ) || empty( $term ) ) {
			return false;
		}

		return $term;
	}
endif;


/* Check if SEO by Yoast plugin is active */
if ( !function_exists( 'vce_is_yoast_active' ) ):
	function vce_is_yoast_active() {

		if ( in_array( 'wordpress-seo/wp-seo.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}

		return false;
	}
endif;


/**
 * Display ads
 *
 * @since  2.7.1
 *
 * @return boolean
 */
if ( !function_exists( 'vce_can_display_ads' ) ):
	function vce_can_display_ads() {
		if ( is_404() && vce_get_option( 'ad_exclude_404' ) ) {
			return false;
		}

		$exclude_ids_option = vce_get_option( 'ad_exclude_from_pages' );
		$exclude_ids = !empty( $exclude_ids_option ) ? $exclude_ids_option : array();

		if ( is_page() && in_array( get_queried_object_id(), $exclude_ids ) ) {
			return false;
		}

		return true;
	}
endif;


/**
 * Used for getting post type with all its taxonomies
 *
 * @return array
 * @since    2.7.1
 */
if ( !function_exists( 'vce_get_post_type_with_taxonomies' ) ):
	function vce_get_post_type_with_taxonomies( $post_type ) {

		$post_type = get_post_type_object( $post_type );

		if ( empty( $post_type ) )
			return null;


		$post_taxonomies = array();
		$taxonomies = get_taxonomies( array(
				'object_type' => array( $post_type->name ),
				'public'      => true,
				'show_ui'     => true,
			), 'object' );

		if ( !empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {

				$tax = array();
				$tax['id'] = $taxonomy->name;
				$tax['name'] = $taxonomy->label;
				$tax['hierarchical'] = $taxonomy->hierarchical;
				if ( $tax['hierarchical'] ) {
					$tax['terms'] = get_terms( $taxonomy->name, array( 'hide_empty' => false ) );
				}

				$post_taxonomies[] = $tax;
			}
		}

		if ( !empty( $post_taxonomies ) ) {
			$post_type->taxonomies = $post_taxonomies;
		}


		return apply_filters( 'vce_modify_post_type_with_taxonomies', $post_type );
	}
endif;



/**
 * Get term slugs by term names for specific taxonomy
 *
 * @param string  $names List of tag names separated by comma
 * @param string  $tax   Taxonomy name
 * @return array List of slugs
 * @since  2.7.1
 */

if ( !function_exists( 'vce_get_tax_term_slug_by_name' ) ):
	function vce_get_tax_term_slug_by_name( $names, $tax = 'post_tag' ) {

		if ( empty( $names ) ) {
			return '';
		}

		$slugs = array();
		$names = explode( ",", $names );

		foreach ( $names as $name ) {
			$tag = get_term_by( 'name', trim( $name ), $tax );

			if ( !empty( $tag ) && isset( $tag->slug ) ) {
				$slugs[] = $tag->slug;
			}
		}

		return $slugs;

	}
endif;


/* Check if Gutenberg plugin is active and we are on its page */
if ( !function_exists( 'vce_is_gutenberg_page' ) ):
	function vce_is_gutenberg_page() {

		if ( function_exists( 'is_gutenberg_page' ) ) {
			return is_gutenberg_page();
		}
		
		global $wp_version;

		if( version_compare( $wp_version, '5', '<' ) ){
			return false;
		}

		if ( function_exists('classic_editor_init_actions') && get_option('classic-editor-replace') == 'replace' ){
			return false;
		}

		if ( function_exists('classic_editor_init_actions') && isset( $_REQUEST['classic-editor'] ) ) {
			return false;
		}
		
		return true;
	}
endif;

/* Check if Envato Market plugin is active */
if ( !function_exists( 'vce_is_envato_market_active' ) ):
	function vce_is_envato_market_active() {

		if ( in_array( 'envato-market/envato-market.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}
		return false;
	}
endif;
?>