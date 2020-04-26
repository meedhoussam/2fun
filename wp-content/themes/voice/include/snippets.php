<?php
/*-----------------------------------------------------------------------------------*/
/*	Include snippets to modify/add some features to this theme
/*-----------------------------------------------------------------------------------*/

/* Allow shortcodes in widgets */
add_filter( 'widget_text', 'do_shortcode' );

/* Add classes to body tag */
if ( !function_exists( 'vce_body_class' ) ):
	function vce_body_class( $classes ) {
		global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

		//Add some broswer classes which can be usefull for some css hacks later
		if ( $is_lynx ) $classes[] = 'lynx';
		elseif ( $is_gecko ) $classes[] = 'gecko';
		elseif ( $is_opera ) $classes[] = 'opera';
		elseif ( $is_NS4 ) $classes[] = 'ns4';
		elseif ( $is_safari ) $classes[] = 'safari';
		elseif ( $is_chrome ) $classes[] = 'chrome';
		elseif ( $is_IE ) $classes[] = 'ie';
		else $classes[] = 'unknown';

		if ( $is_iphone ) $classes[] = 'iphone';

		//Do not touch this, we use this global var to define current sidebar layout on all pages
		global $vce_sidebar_opts;

		$vce_sidebar_opts = vce_get_current_sidebar();
		$sidebar_class = $vce_sidebar_opts['use_sidebar'] ? 'vce-sid-'.$vce_sidebar_opts['use_sidebar'] : '';

		$classes[] = $sidebar_class;

		$classes[] = 'voice-v_' . str_replace('.', '_', THEME_VERSION);

		return $classes;
	}
endif;

add_filter( 'body_class', 'vce_body_class' );

/* Backwards support for wp title tag ( if version < wp 4.1) */
if ( ! function_exists( '_wp_render_title_tag' ) ) :

	if ( ! function_exists( 'vce_render_title' ) ) :
		function vce_render_title() {
			echo '<title>';
			wp_title( '|', true, 'right' );
			echo '</title>';
		}
	endif;

add_action( 'wp_head', 'vce_render_title' );

/* Add wp_title filter */
if ( !function_exists( 'vce_wp_title' ) ):
	function vce_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() )
			return $title;

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 )
			$title = "$title $sep " . sprintf( __( 'Page %s', THEME_SLUG ), max( $paged, $page ) );

		return $title;
	}
endif;

add_filter( 'wp_title', 'vce_wp_title', 10, 2 );

endif;

/* Run Theme Update Check */
if ( !function_exists( 'vce_run_updater' ) ):
	function vce_run_updater() {

		$user = vce_get_option( 'theme_update_username' );
		$apikey = vce_get_option( 'theme_update_apikey' );
		if ( !empty( $user ) && !empty( $apikey ) ) {
			include_once 'classes/class-pixelentity-theme-update.php';
			PixelentityThemeUpdate::init( $user, $apikey );
		}
	}
endif;

add_action( 'admin_init', 'vce_run_updater' );


/* Extend user social profiles  */
if ( !function_exists( 'vce_user_social_profiles' ) ):
	function vce_user_social_profiles( $contactmethods ) {

		unset( $contactmethods['aim'] );
		unset( $contactmethods['yim'] );
		unset( $contactmethods['jabber'] );

		$social = vce_get_social();
		foreach ( $social as $soc_id => $soc_name ) {
			if ( $soc_id ) {
				$contactmethods[$soc_id] = $soc_name;
			}
		}
		return $contactmethods;
	}
endif;

add_filter( 'user_contactmethods', 'vce_user_social_profiles' );

/* Delete our custom category meta from database on category deletion */
if ( !function_exists( 'vce_delete_category_meta' ) ):
	function vce_delete_category_meta( $term_id ) {
		delete_option( '_vce_category_'.$term_id );
	}
endif;

add_action( 'delete_category', 'vce_delete_category_meta' );


/* Change customize link to lead to theme options instead of live customizer */
if ( !function_exists( 'vce_change_customize_link' ) ):
	function vce_change_customize_link( $themes ) {
		if ( array_key_exists( 'voice', $themes ) ) {
			$themes['voice']['actions']['customize'] = admin_url( 'admin.php?page=vce_options' );
		}
		return $themes;
	}
endif;

add_filter( 'wp_prepare_themes_for_js', 'vce_change_customize_link' );

/* Print some stuff from options to head tag */
if ( !function_exists( 'vce_wp_head' ) ):
	function vce_wp_head() {

		//Add favicons
		if ( $favicon = vce_get_option_media( 'favicon' ) ) {
			echo '<link rel="shortcut icon" href="'.esc_url( $favicon ).'" type="image/x-icon" />';
		}

		if ( $apple_touch_icon = vce_get_option_media( 'apple_touch_icon' ) ) {
			echo '<link rel="apple-touch-icon" href="'.esc_url( $apple_touch_icon ).'" />';
		}

		if ( $metro_icon = vce_get_option_media( 'metro_icon' ) ) {
			echo '<meta name="msapplication-TileColor" content="#ffffff">';
			echo '<meta name="msapplication-TileImage" content="'.esc_url( $metro_icon ).'" />';
		}

		//Additional CSS (if user adds his custom css inside theme options)
		$additional_css = trim( preg_replace( '/\s+/', ' ', vce_get_option( 'additional_css' ) ) );
		if ( !empty( $additional_css ) ) {
			echo '<style type="text/css">'.$additional_css.'</style>';
		}

		//Google Analytics (tracking)
		if ( $ga = vce_get_option( 'ga' ) ) {
			echo $ga;
		}

	}
endif;

add_action( 'wp_head', 'vce_wp_head', 99 );

/* For advanced use - custom JS code into footer if specified in theme options */
if ( !function_exists( 'vce_wp_footer' ) ):
	function vce_wp_footer() {

		//Additional JS
		$additional_js = trim( preg_replace( '/\s+/', ' ', vce_get_option( 'additional_js' ) ) );
		if ( !empty( $additional_js ) ) {
			echo '<script type="text/javascript">
				/* <![CDATA[ */
					'.$additional_js.'
				/* ]]> */
				</script>';
		}


	}
endif;

add_action( 'wp_footer', 'vce_wp_footer', 99 );


/* Show welcome message and quick tips after theme activation */
if ( !function_exists( 'vce_welcome_msg' ) ):
	function vce_welcome_msg() {
		if ( !get_option( 'vce_welcome_box_displayed' ) ) { update_option( 'vce_theme_version', THEME_VERSION ); ?>
				<?php include_once THEME_DIR.'/sections/welcome.php';?>
		<?php
		}
	}
endif;

/* Show message box after theme update */
if ( !function_exists( 'vce_update_msg' ) ):
	function vce_update_msg() {
		if ( get_option( 'vce_welcome_box_displayed' ) ) {
			$prev_version = get_option( 'vce_theme_version' );
			$cur_version = THEME_VERSION;
			if ( $prev_version === false ) {$prev_version = '0.0.0';}
			if ( version_compare( $cur_version, $prev_version, '>' ) ) { ?>
				<?php include_once THEME_DIR.'/sections/update-notify.php';?>
			<?php
			}
		}
	}
endif;

/* Show admin notices */
if ( !function_exists( 'vce_check_installation' ) ):
	function vce_check_installation() {
		add_action( 'admin_notices', 'vce_welcome_msg', 1 );
		add_action( 'admin_notices', 'vce_update_msg', 1 );
	}
endif;

add_action( 'admin_init', 'vce_check_installation' );


/* Fix pagination issue caused by Facebook plugin */
if ( !function_exists( 'vce_fb_plugin_pagination_fix' ) ):
	function vce_fb_plugin_pagination_fix() {
		if ( class_exists( 'Facebook_Loader' ) && is_front_page() ) {
			global $wp_query;
			$page = get_query_var( 'page' );
			$paged = get_query_var( 'paged' );
			if ( $page > 1 || $paged > 1 ) {
				unset( $wp_query->queried_object );
			}
		}
	}
endif;

add_action( 'wp', 'vce_fb_plugin_pagination_fix', 99 );


/* Store registered sidebars so we can get them before wp_registered_sidebars is initialized to use them in theme options */
if ( !function_exists( 'vce_check_sidebars' ) ):
	function vce_check_sidebars() {
		global $wp_registered_sidebars;
		if ( !empty( $wp_registered_sidebars ) ) {
			update_option( 'vce_registered_sidebars', $wp_registered_sidebars );
		}
	}
endif;

add_action( 'admin_init', 'vce_check_sidebars' );

/* Function that outputs the contents of the dashboard widget */
if ( !function_exists( 'vce_dashboard_widget_cb' ) ):
	function vce_dashboard_widget_cb() {

		$hide = false;

		if ( $data = get_transient( 'vce_mksaw' ) ) {
			if ( $data != 'error' ) {
				echo $data;
			} else {
				$hide = true;
			}
		} else {
			$url = 'https://demo.mekshq.com/mksaw.php';
			$args = array( 'body' => array( 'key' => md5( 'meks' ), 'theme' => 'voice' ) );
			$response = wp_remote_post( $url, $args );
			if ( !is_wp_error( $response ) ) {
				$json = wp_remote_retrieve_body( $response );
				if ( !empty( $json ) ) {
					$json = ( json_decode( $json ) );
					if ( isset( $json->data ) ) {
						echo $json->data;
						set_transient( 'vce_mksaw', $json->data, 86400 );
					} else {
						set_transient( 'vce_mksaw', 'error', 86400 );
						$hide = true;
					}
				} else {
					set_transient( 'vce_mksaw', 'error', 86400 );
					$hide = true;
				}

			} else {
				set_transient( 'vce_mksaw', 'error', 86400 );
				$hide = true;
			}
		}

		if ( $hide ) {
			echo '<style>#vce_dashboard_widget {display:none;}</style>'; //hide widget if data is not returned properly
		}

	}
endif;

/* Add dashboard widget */
if ( !function_exists( 'vce_add_dashboard_widgets' ) ):
	function vce_add_dashboard_widgets() {
		add_meta_box( 'vce_dashboard_widget', 'Meks - WordPress Themes & Plugins', 'vce_dashboard_widget_cb', 'dashboard', 'side', 'high' );
	}
endif;

add_action( 'wp_dashboard_setup', 'vce_add_dashboard_widgets' );

/* Add media graber features */
if ( !function_exists( 'vce_add_media_graber' ) ):
	function vce_add_media_graber() {
		if ( !class_exists( 'Hybrid_Media_Grabber' ) ) {
			include_once 'classes/class-hybrid-media-grabber.php';
		}
	}
endif;

add_action( 'init', 'vce_add_media_graber' );


/* Add span elements to post count number in category widget */
if ( !function_exists( 'vce_add_span_cat_count' ) ):
	function vce_add_span_cat_count( $links, $args ) {

		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] != 'category' ) {
			return $links;
		}

		$links = preg_replace( '/(<a[^>]*>)/', '$1<span class="category-text">', $links );
		$links = str_replace( '</a>', '</span></a>', $links );
		$links = str_replace( '</a> (', '<span class="count"><span class="count-hidden">', $links );
		$links = str_replace( ')', '</span></span></a>', $links );

		return $links;
	}
endif;

add_filter( 'wp_list_categories', 'vce_add_span_cat_count', 10, 2 );

/* Unregister Entry Views widget */
if ( !function_exists( 'vce_unregister_widgets' ) ):
	function vce_unregister_widgets() {

		$widgets = array( 'EV_Widget_Entry_Views' );

		//Allow child themes or plugins to add/remove widgets they want to unregister
		$widgets = apply_filters( 'vce_modify_unregister_widgets', $widgets );

		if ( !empty( $widgets ) ) {
			foreach ( $widgets as $widget ) {
				unregister_widget( $widget );
			}
		}

	}
endif;


add_action( 'widgets_init', 'vce_unregister_widgets', 99 );

/* Remove entry views support for other post types, we need post support only */
if ( !function_exists( 'vce_remove_entry_views_support' ) ):
	function vce_remove_entry_views_support() {

		$types = array( 'page', 'attachment', 'literature', 'portfolio_item', 'recipe', 'restaurant_item' );

		//Allow child themes or plugins to modify entry views support
		$widgets = apply_filters( 'vce_modify_entry_views_support', $types );

		if ( !empty( $types ) ) {
			foreach ( $types as $type ) {
				remove_post_type_support( $type, 'entry-views' );
			}
		}

	}
endif;

add_action( 'init', 'vce_remove_entry_views_support', 99 );

add_action( 'init', 'vce_check_gallery' );

/* Check wheter to enable Voice galley styling */
function vce_check_gallery() {
	if ( vce_get_option( 'use_gallery' ) ) {
		add_filter( 'shortcode_atts_gallery', 'vce_gallery_atts', 10, 3 );
		add_filter( 'post_gallery', 'vce_gallery_shortcode', 10, 4 );
	}
}

/* Change atts of wp gallery shortcode to get best size depending on column selection */
if ( !function_exists( 'vce_gallery_atts' ) ):
	function vce_gallery_atts( $out, $pairs, $atts ) {


		global $vce_sidebar_opts;

		$size_split = $vce_sidebar_opts['use_sidebar'] == 'none' ? 7 : 5;

		if ( !isset( $atts['columns'] ) ) {
			$atts['columns'] = 3;
		}

		if ( $atts['columns'] < $size_split ) {
			$size = 'vce-lay-b';
		} else {
			$size = 'vce-lay-d';
		}

		if( $atts['columns'] == 2 || ( $atts['columns'] == 3 && $vce_sidebar_opts['use_sidebar'] == 'none' ) ){
			$size = 'vce-lay-a';
		}

		$out['columns'] = $atts['columns'];
		$out['size'] = $size;
		$out['link'] = 'file';

		return $out;

	}
endif;


/* Slighly modify wordpress gallery shortcode */
if ( !function_exists( 'vce_gallery_shortcode' ) ):
	function vce_gallery_shortcode( $output = '', $attr, $content = false, $tag = false ) {
		$post = get_post();

		static $instance = 0;
		$instance++;

		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) ) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}


		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( ! $attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		$html5 = current_theme_supports( 'html5', 'gallery' );
		$atts = shortcode_atts( array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post ? $post->ID : 0,
				'itemtag'    => $html5 ? 'figure'     : 'dl',
				'icontag'    => $html5 ? 'div'        : 'dt',
				'captiontag' => $html5 ? 'figcaption' : 'dd',
				'columns'    => 3,
				'size'       => 'thumbnail',
				'include'    => '',
				'exclude'    => '',
				'link'       => ''
			), $attr, 'gallery' );

		$id = intval( $atts['id'] );
		if ( 'RAND' == $atts['order'] ) {
			$atts['orderby'] = 'none';
		}

		if ( ! empty( $atts['include'] ) ) {
			$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( ! empty( $atts['exclude'] ) ) {
			$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		} else {
			$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		}

		if ( empty( $attachments ) ) {
			return '';
		}

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
			}
			return $output;
		}

		$itemtag = tag_escape( $atts['itemtag'] );
		$captiontag = tag_escape( $atts['captiontag'] );
		$icontag = tag_escape( $atts['icontag'] );
		$valid_tags = wp_kses_allowed_html( 'post' );
		if ( ! isset( $valid_tags[ $itemtag ] ) ) {
			$itemtag = 'dl';
		}
		if ( ! isset( $valid_tags[ $captiontag ] ) ) {
			$captiontag = 'dd';
		}
		if ( ! isset( $valid_tags[ $icontag ] ) ) {
			$icontag = 'dt';
		}

		$columns = intval( $atts['columns'] );
		$itemwidth = $columns > 0 ? floor( 100/$columns ) : 100;
		$float = is_rtl() ? 'right' : 'left';

		$selector = "gallery-{$instance}";

		$gallery_style = '';

		if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
			$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>\n\t\t";
		}

		$size_class = sanitize_html_class( $atts['size'] );
		$gallery_div = "<div id='$selector' class='vce-gallery gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

		$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );


		$output .= '<div class="vce-gallery-big">';
		global $vce_sidebar_opts;
		$big_size = $vce_sidebar_opts['use_sidebar'] == 'none' ? 'vce-lay-a-nosid' : 'vce-lay-a';
		$vce_i = 0;
		foreach ( $attachments as $id => $attachment ) {
			$image_output = wp_get_attachment_link( $id, $big_size, false, false );
			$display = ( $vce_i == 0 ) ? '' : 'style="display:none;"';
			$output .= '<div class="big-gallery-item item-'.$vce_i.'" '.$display.'>';
			$output .= "
			<{$icontag} class='gallery-icon'>
				$image_output
			</{$icontag}>";

			if ( $captiontag && trim( $attachment->post_excerpt ) ) {
				$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize( $attachment->post_excerpt ) . "
				</{$captiontag}>";
			}
			$output .= '</div>';
			$vce_i++;
		}
		$output .= '</div>';

		if ( $columns > 1 ) {
			$output .= '<div class="vce-gallery-slider" data-columns="'.$columns.'">';
			$i = 0; $vce_i = 0;
			foreach ( $attachments as $id => $attachment ) {

				if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
					$image_output = wp_get_attachment_link( $id, $atts['size'], false, false );
				} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
					$image_output = wp_get_attachment_image( $id, $atts['size'], false );
				} else {
					$image_output = wp_get_attachment_link( $id, $atts['size'], true, false );
				}
				$image_meta  = wp_get_attachment_metadata( $id );

				$orientation = '';
				if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
					$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
				}
				$output .= "<{$itemtag} class='gallery-item' data-item='".$vce_i."'>";
				$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";
				$output .= "</{$itemtag}>";
				if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
					$output .= '<br style="clear: both" />';
				}

				$vce_i++;

			}

			if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
				$output .= "
			<br style='clear: both' />";
			}

			$output .= "</div>";
		}
		$output .= "</div>\n";

		return $output;
	}
endif;


/* Pre get posts */
if ( !function_exists( 'vce_pre_get_posts' ) ):
	function vce_pre_get_posts( $query ) {

		if ( !is_admin() && $query->is_main_query() && !$query->is_feed() ) {

			/* Check whether to change number of posts per page for specific archive template if specifed in theme options */
			$template = vce_detect_template();
			$ppp = vce_get_option( $template.'_ppp' );

			if ( $ppp == 'custom' ) {

				$ppp = absint( vce_get_option( $template.'_ppp_num' ) );
				$query->set( 'posts_per_page', $ppp );

			}

			if ( $template == 'category' ) {
					$obj = get_queried_object();
					$cat_meta = vce_get_category_meta( $obj->term_id );
					if ( $cat_meta['layout'] != 'inherit' && !empty( $cat_meta['ppp'] ) ) {
						$ppp = $cat_meta['ppp'];
						$query->set( 'posts_per_page', $ppp );
					}
			}

			/*Check for featured area on category page and exclude those posts from main post listing */
			if ( $template == 'category' ) {

				global $vce_cat_fa_args;
				$vce_cat_fa_args = vce_get_fa_cat_args();

				if ( vce_get_option( 'category_fa_not_duplicate' ) ) {
					if ( isset( $vce_cat_fa_args['fa_posts'] ) && !empty( $vce_cat_fa_args['fa_posts'] ) ) {
						$exclude_ids = array();
						foreach ( $vce_cat_fa_args['fa_posts']->posts as $p ) {
							$exclude_ids[] = $p->ID;
						}
						$query->set( 'post__not_in', $exclude_ids );
					}
				}
			}


		}

	}
endif;

add_action( 'pre_get_posts', 'vce_pre_get_posts' );

/* Change default arguments of flickr widget plugin */
if ( !function_exists( 'vce_flickr_widget_defaults' ) ):
	function vce_flickr_widget_defaults( $defaults ) {

		$defaults['t_width'] = 80;
		$defaults['t_height'] = 80;
		return $defaults;
	}
endif;

add_filter( 'mks_flickr_widget_modify_defaults', 'vce_flickr_widget_defaults' );


/* Change default arguments of author widget plugin */
if ( !function_exists( 'vce_author_widget_defaults' ) ):
	function vce_author_widget_defaults( $defaults ) {
		$defaults['avatar_size'] = 90;
		$defaults['show_social_networks'] = 0;
		return $defaults;
	}
endif;

add_filter( 'mks_author_widget_modify_defaults', 'vce_author_widget_defaults' );


/* Rrevent redirect issue that may brake home page pagination caused by some plugins */
function vce_disable_redirect_canonical( $redirect_url ) {
	if ( is_page_template( 'template-modules.php' ) && is_paged() ) {
		$redirect_url = false;
	}
	return $redirect_url;
}

add_filter( 'redirect_canonical', 'vce_disable_redirect_canonical' );

/* Add items dynamically to menu*/
if ( !function_exists( 'vce_extend_navigation' ) ):
	function vce_extend_navigation( $items, $args ) {
		if ( $args->theme_location == 'vce_main_navigation_menu'  ) {
			
			if ( vce_get_option( 'header_search' ) ) {
				$items .= '<li class="search-header-wrap"><a class="search_header" href="javascript:void(0)"><i class="fa fa-search"></i></a><ul class="search-header-form-ul"><li>';				
				$items.= get_search_form( false );
				$items .= '</li></ul></li>';
			}

			if ( vce_get_option( 'woocommerce_cart_icon' ) ) {

				$elements = vce_woocommerce_cart_elements();
				if(!empty($elements)){
					$items .= '<li class="vce-cart-icon"><a class="vce-custom-cart" href="'. esc_attr($elements['cart_url']) . '"><i class="fa fa-shopping-cart">';
					if( $elements['products_count'] > 0 ) { 
						$items .= '<span class="vce-cart-count"> '. $elements['products_count'] .'</span>';
					}
					$items .= '</i></a></li>';
				}
			}

		}
		
		return $items;
	}
endif;

add_action( 'wp_nav_menu_items', 'vce_extend_navigation', 10, 2 );


/* Modify WooCommerce wrappers */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'vce_woocommerce_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'vce_woocommerce_wrapper_end', 10 );

if ( !function_exists( 'vce_woocommerce_wrapper_start' ) ):
	function vce_woocommerce_wrapper_start() {
		echo '<div id="content" class="container site-content"><div id="primary" class="vce-main-content"><main id="main" class="main-box main-box-single">';
	}
endif;

if ( !function_exists( 'vce_woocommerce_wrapper_end' ) ):
	function vce_woocommerce_wrapper_end() {
		echo '</main></div>';
	}
endif;

/**
 * Woocommerce  Cart Elements
 *
 * @return bool
 * @since  2.6
 */
if ( !function_exists( 'vce_woocommerce_cart_elements' ) ):
	function vce_woocommerce_cart_elements() {
		if( !vce_is_woocommerce_active() ){ return; }
		$elements = array();
		$elements['cart_url'] = wc_get_cart_url(); 
		$elements['products_count'] = WC()->cart->get_cart_contents_count();
		return $elements;
	}
endif;

/**
 * Woocommerce Ajaxify Cart
 *
 * @return bool
 * @since  2.6
 */
if ( !function_exists( 'vce_woocommerce_ajax_fragments' ) ):
	
	if ( vce_is_woocommerce_active() && version_compare( WC_VERSION, '3.2.6', '<') ) {
		add_filter( 'add_to_cart_fragments', 'vce_woocommerce_ajax_fragments' );
	} else {
		add_filter( 'woocommerce_add_to_cart_fragments', 'vce_woocommerce_ajax_fragments' );
	}

	function vce_woocommerce_ajax_fragments( $fragments ) {
		
		ob_start();	
		$elements = vce_woocommerce_cart_elements();
		if (!empty($elements)) :
		?>
			<a class="vce-custom-cart" href="<?php echo esc_attr($elements['cart_url']); ?>">
				<i class="fa fa-shopping-cart" aria-hidden="true">
					<?php if( $elements['products_count'] > 0 ) : ?>
						<span class="vce-cart-count"><?php echo $elements['products_count']; ?></span>
					<?php endif; ?>
				</i>
			</a>
		<?php
		endif;
		$fragments['a.vce-custom-cart'] = ob_get_clean();
		return $fragments;
	}
endif;

/* Add Voice author widget social options */
if ( !function_exists( 'vce_add_author_widget_opts' ) ) :

	function vce_add_author_widget_opts( $widget, $return, $instance ) {
		if ( $widget instanceof MKS_Author_Widget ):
			$field_id = $widget->get_field_id( 'show_social_networks' );
		$field_name = $widget->get_field_name( 'show_social_networks' );
		$checked = checked( 1, $instance['show_social_networks'], false );
		$option_name = __( 'Show social networks', THEME_SLUG );
		$option_help = __( 'Check this box to show social networks', THEME_SLUG );


?>
			<ul>
				<li>
					<input id="<?php echo $widget->get_field_id( 'show_social_networks' ); ?>" type="checkbox" name="<?php echo $widget->get_field_name( 'show_social_networks' ); ?>" <?php echo $checked;?> class="widefat"/>
					<label for="<?php echo $widget->get_field_id( 'show_social_networks' ); ?>"><?php _e( 'Show social networks:', THEME_SLUG ); ?></label>
					<small class="howto"><?php _e( 'Check this box to display social networks', THEME_SLUG ); ?></small>
				</li>
			</ul>
		<?php
		endif;

	}


endif;

add_action( 'in_widget_form', 'vce_add_author_widget_opts', 10, 3 );

/* Add Voice author widget options save */
if ( !function_exists( 'vce_save_author_widget_opts' ) ) :

	function vce_save_author_widget_opts( $instance, $new_instance, $old_instance ) {
		if ( $_POST['id_base'] != 'mks_author_widget' )
			return $instance;

		//print_r($_POST);die();

		if ( isset( $_POST['widget_number'] ) && ( $_POST['widget_number'] != '' ) ) :
			$widget_no = $_POST['widget_number'];
		$instance['show_social_networks'] = ( isset( $_POST['widget-mks_author_widget'][$widget_no]['show_social_networks'] ) ) ? 1 : 0;
		elseif ( isset( $_POST['multi_number'] ) && ( $_POST['multi_number'] != '' ) ) :
			$widget_no = $_POST['multi_number'];
		$instance['show_social_networks'] = ( isset( $_POST['widget-mks_author_widget'][$widget_no]['show_social_networks'] ) ) ? 1 : 0;
		else :
			$instance['show_social_networks'] = ( isset( $_POST['widget-mks_author_widget']['show_social_networks'] ) ) ? 1 : 0;
		endif;

		//var_dump($instance['show_social_networks']); die();

		return $instance;

	}

endif;

add_filter( 'widget_update_callback', 'vce_save_author_widget_opts', 20, 3 );

if ( !function_exists( 'vce_meks_author_social_networks' ) ) :

	function vce_meks_author_social_networks( $user_id ) {

		$output = '';

		if ($author_url = get_the_author_meta('url', $user_id )) {
			$output .= '<a href="'.esc_url($author_url).'" target="_blank" class="fa fa-link"></a>';
		} 

		$user_social = vce_get_social();

		foreach ( $user_social as $soc_id => $soc_name ){
			if ( $social_meta = get_the_author_meta($soc_id, $user_id) ) {
				if ($soc_id == 'twitter') {
					$social_meta = (strpos($social_meta, 'http') === false) ? 'https://twitter.com/' . $social_meta : $social_meta; 
				}
				$output .= '<a href="'.$social_meta.'" target="_blank" class="fa fa-'.$soc_id.'"></a>';
			}
		}

		if(!empty($output)){
			$output = '<div class="vce-author-links">'.$output.'</div>';
		}

		return $output;
	}

endif;


/* White label WP Review plugin - remove banner from options */

add_filter( 'wp_review_remove_branding', '__return_true' );


/* Remove WP review for pages */

add_filter( 'wp_review_excluded_post_types', 'vce_wp_review_exclude_post_types' );

if ( !function_exists( 'vce_wp_review_exclude_post_types' ) ):
	function vce_wp_review_exclude_post_types( $excluded ) {
	  $excluded[] = 'page';
	  return $excluded;
	}
endif; 

/* Remove WP review notice */

remove_action('admin_notices', 'wp_review_admin_notice');


/* Remove WP review jQuery UI from admin pages */

add_action('admin_enqueue_scripts', 'vce_wp_review_exclude_admin_scripts', 99 );

if ( !function_exists( 'vce_wp_review_exclude_admin_scripts' ) ):
	function vce_wp_review_exclude_admin_scripts() {

		if( vce_is_wp_review_active() ) {
		 	wp_dequeue_style( 'plugin_name-admin-ui-css' );
		 	wp_dequeue_style( 'wp-review-admin-ui-css' );
		}

		wp_dequeue_style( 'jquery-ui.js' );
	  
	}
endif;		

/**
 * Add widget form options
 *
 * Add custom options to each widget
 *
 * @return void
 * @since  2.4
 */

add_action( 'in_widget_form', 'vce_add_widget_form_options', 10, 3 );

if ( !function_exists( 'vce_add_widget_form_options' ) ) :

	function vce_add_widget_form_options(  $widget, $return, $instance) {

		if(!isset($instance['vce-padding'])){
			$instance['vce-padding'] = 0;
		}

		$exclude =  array( 
				'pages', 
				'categories', 
				'archives',
				'recent-comments',
				'recent-posts',
				'nav_menu',
				'calendar',
				'meta',
				'rss',
				'search',
				'tag_cloud',
				'vce_video_widget',  
				'vce_posts_widget',
				'vce_adsense_widget',  
				'mks_ads_widget', 
				'mks_author_widget', 
				'mks_flickr_widget', 
				'mks_social_widget', 
				'mks_themeforest_widget',

		);

		$exclude = apply_filters('vce_modify_widgets_exclude_add_form_options', $exclude );

		if(in_array( $widget->id_base , $exclude )){
			return;
		}
		?>	
		<p class="vce-opt-padding">
			<label for="<?php echo esc_attr( $widget->get_field_id( 'vce-padding' )); ?>">
				<input type="checkbox" id="<?php echo esc_attr($widget->get_field_id( 'vce-padding' )); ?>" name="<?php echo esc_attr($widget->get_field_name( 'vce-padding' )); ?>" value="1" <?php checked($instance['vce-padding'], 1); ?> />
				<?php esc_html_e( 'Make widget content full-width', THEME_SLUG);?>
				<small class="howto"><?php esc_html_e( 'Check this option if you want to expand your widget content to 300px', THEME_SLUG);?></small>
			</label>
		</p>

	<?php
	}
endif;


/**
 * Save widget form options
 *
 * Save custom options to each widget
 *
 * @return void
 * @since  2.4
 */

add_filter( 'widget_update_callback', 'vce_save_widget_form_options', 20, 2 );

if ( !function_exists( 'vce_save_widget_form_options' ) ) :

	function vce_save_widget_form_options( $instance, $new_instance ) {
		
		$instance['vce-padding'] = isset( $new_instance['vce-padding'] ) ? 1 : 0;
		return $instance;

	}

endif;


/**
 * Widget display callback
 *
 * Check if padding option is selected and add no-padding class to widget
 *
 * @return void
 * @since  2.4
 */

add_filter( 'dynamic_sidebar_params', 'vce_modify_widget_display' );

if ( !function_exists( 'vce_modify_widget_display' ) ) :

	function vce_modify_widget_display( $params ) {

		if ( strpos( $params[0]['id'], 'vce_footer_sidebar' ) !== false ) {
			return $params; //do not apply styling for footer widgets
		}

		global $wp_registered_widgets;

		$widget_id              = $params[0]['widget_id'];
		$widget_obj             = $wp_registered_widgets[$widget_id];
		$widget_num             = $widget_obj['params'][0]['number'];
		$widget_opt = get_option( $widget_obj['callback'][0]->option_name );

		if ( isset( $widget_opt[$widget_num]['vce-padding'] ) && $widget_opt[$widget_num]['vce-padding'] == 1 ) {
			$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"vce-no-padding ", $params[0]['before_widget'], 1 );
		}

		return $params;

	}

endif;


add_action('admin_bar_menu', 'vce_add_frontend_adminbar_theme_options_links', 100);
/**
 * Add Theme options links to adminbar on frontend
 *
 * @param WP_Admin_Bar $admin_bar
 * @return WP_Admin_Bar
 * @since  1.8
 */
if(!function_exists('vce_add_frontend_adminbar_theme_options_links')):
    function vce_add_frontend_adminbar_theme_options_links($admin_bar){
        if(is_admin() || !current_user_can('manage_options')){
            return $admin_bar;
        }

        /* Theme Options - main options(parent off all) */
        $admin_bar->add_menu( array(
            'id'    => 'wp-admin-bar-vce_options',
            'title' => '<span class="ab-icon dashicons-admin-generic"></span>' . __('Theme Options', THEME_SLUG),
            'href'  => admin_url('admin.php?page=vce_options&tab=1'),
            'meta'  => array(
                'title' => __('Theme Options', THEME_SLUG),
                'target' => '_blank',
            ),
        ));

        return $admin_bar;
    }
endif;


/**
 * Add comment form default fields args filter 
 * to replace comment fields labels
 */

add_filter('comment_form_default_fields', 'vce_comment_fields_labels');

if(!function_exists('vce_comment_fields_labels')):
function vce_comment_fields_labels($fields){

	$replace = array(
		'author' => array(
			'old' => __( 'Name' ),
			'new' =>__vce( 'comment_name' )
		),
		'email' => array(
			'old' => __( 'Email' ),
			'new' =>__vce( 'comment_email' )
		),
		'url' => array(
			'old' => __( 'Website' ),
			'new' =>__vce( 'comment_website' )
		),

		'cookies' => array(
			'old' => __( 'Save my name, email, and website in this browser for the next time I comment.' ),
			'new' =>__vce( 'comment_cookie_gdpr' )
		)
	);

	foreach($fields as $key => $field){

		if(array_key_exists($key, $replace)){
			$fields[$key] = str_replace($replace[$key]['old'], $replace[$key]['new'], $fields[$key]);
		}

	}
	
	return $fields;

}

endif;

?>