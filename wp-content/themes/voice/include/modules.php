<?php

/* Get module actions */
if ( !function_exists( 'vce_get_module_actions' ) ):
	function vce_get_module_actions( $exclude = array() ) {
		$actions = array(
			'0' => __( 'None', THEME_SLUG ),
			'slider' => __( 'Apply slider', THEME_SLUG ),
			'pagination' => __( 'Add pagination', THEME_SLUG ),
			'link' => __( 'Add action link', THEME_SLUG )
		);

		if ( !empty( $exclude ) ) {
			foreach ( $exclude as $action ) {
				if ( array_key_exists( $action, $actions ) ) {
					unset( $actions[$action] );
				}
			}
		}
		return $actions;
	}
endif;

/* Parse arguments and returns posts for specific module */
if ( !function_exists( 'vce_get_module_query' ) ):
	function vce_get_module_query( $args = array(), $paged = false ) {

		if ( $args['type'] == 'blank' )
			return false;

		global $vce_fa_home_posts, $vce_modules_exclude;

		$defaults = array(
			'order' => 'date',
			'sort' => 'DESC',
			'limit' => 4,
			'cat' => array(),
			'cat_child' => 0,
			'cat_inc_exc' => 'in',
			'manual' => array(),
			'tag' => '',
			'tag_inc_exc' => 'in',
			'author' => array(),
			'author_inc_exc' => 'in',
			'exclude_by_id' => array(),
			'exclude' => 0,
			'time' => 0,
			'timeto' => 0
		);

		$args = wp_parse_args( (array)$args, $defaults );

		$q_args['post_type'] = 'post';
		$q_args['ignore_sticky_posts'] = 1;


		if ( isset( $vce_fa_home_posts ) && !empty( $vce_fa_home_posts ) ) {
			$q_args['post__not_in'] = $vce_fa_home_posts;
		}

		if ( isset( $vce_modules_exclude ) && !empty( $vce_modules_exclude ) ) {
			if ( !isset( $q_args['post__not_in'] ) ) {
				$q_args['post__not_in'] = array();
			}
			foreach ( $vce_modules_exclude as $ex ) {
				if ( !in_array( $ex, $q_args['post__not_in'] ) ) {
					$q_args['post__not_in'][] = $ex;
				}
			}
		}

		if ( !empty( $args['manual'] ) ) {

			$q_args['posts_per_page'] = absint( count( $args['manual'] ) );
			$q_args['orderby'] = 'post__in';
			$q_args['post__in'] = $args['manual'];
			$q_args['post_type'] = array_keys( get_post_types( array( 'public' => true ) ) ); //support all existing public post types

		} else {

			$q_args['posts_per_page'] = absint( $args['limit'] );

			if ( !empty( $args['exclude_by_id'] ) ) {

				if ( !empty( $q_args['post__not_in'] ) ) {
					$q_args['post__not_in'] = array_unique( array_merge( $q_args['post__not_in'], $args['exclude_by_id'] ) );
				} else {
					$q_args['post__not_in'] = $args['exclude_by_id'];
				}
			}

			if ( !empty( $args['cat'] ) ) {

				if ( $args['cat_child'] ) {
					$child_cat_temp = array();
					foreach ( $args['cat'] as $parent ) {
						$child_cats = get_categories( array( 'child_of' => $parent ) );
						if ( !empty( $child_cats ) ) {
							foreach ( $child_cats as $child ) {
								$child_cat_temp[] = $child->term_id;
							}
						}
					}
					$args['cat'] = array_merge( $args['cat'], $child_cat_temp );
				}

				$q_args['category__' . $args['cat_inc_exc']] = $args['cat'];
			}

			if ( !empty( $args['author'] ) ) {
				$q_args['author__' . $args['author_inc_exc']] = $args['author'];
			}

			$q_args['orderby'] = $args['order'];

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
								'key' => 'wp_review_user_review_type',
								'value' => $review_type,
							)
						);

					} else {

						$review_type = substr( $q_args['orderby'], 8, strlen( $q_args['orderby'] ) );

						$q_args['orderby'] = 'meta_value_num';
						$q_args['meta_key'] = 'wp_review_total';

						$q_args['meta_query'] = array(
							array(
								'key' => 'wp_review_type',
								'value' => $review_type,
							)
						);
					}

				}

			if ( $q_args['orderby'] == 'comments_number' ) {
				$q_args['orderby'] = 'comment_count';
			}

			$q_args['order'] = $args['sort'];


			$date_query = array();

			if ( $time_diff = $args['time'] ) {

				$date_query[0]['after'] = date( 'Y-m-d', vce_calculate_time_diff( $time_diff ) );
			}

			if ( $time_diff = $args['timeto'] ) {

				$date_query[0]['before'] = date( 'Y-m-d', vce_calculate_time_diff( $time_diff ) );
			}

			if ( !empty( $date_query ) ) {
				$q_args['date_query'] = $date_query;
			}

			if ( !empty( $args['tag'] ) ) {
				$q_args['tag__' . $args['tag_inc_exc']] = vce_get_tax_term_id_by_slug( explode( ",", $args['tag'] ) );
			}

		}

		if ( $paged ) {
			$q_args['paged'] = $paged;
		}

		$q_args = apply_filters('vce_modify_module_query_args', $q_args );

		$query = new WP_Query( $q_args );

		if ( $args['exclude'] && !is_wp_error( $query ) && !empty( $query ) ) {

			foreach ( $query->posts as $p ) {
				$vce_modules_exclude[] = $p->ID;
			}

		}

		return $query;

	}
endif;


/* Parse arguments and returns CPT posts for module */
if ( !function_exists( 'vce_get_module_query_cpt' ) ):
	function vce_get_module_query_cpt( $args = array(), $paged = false ) {

		if ( $args['type'] == 'blank' )
			return false;

		global $vce_fa_home_posts, $vce_modules_exclude;

		$defaults = array(
			'order' => 'date',
			'sort' => 'DESC',
			'limit' => 4,
			'manual' => array(),
			'author' => array(),
			'author_inc_exc' => 'in',
			'exclude_by_id' => array(),
			'exclude' => 0,
			'time' => 0,
			'timeto' => 0,
			'tax' => array(),
		);

		$args = wp_parse_args( (array)$args, $defaults );


		$q_args['post_type'] = $args['type'];
		$q_args['ignore_sticky_posts'] = 1;


		if ( isset( $vce_fa_home_posts ) && !empty( $vce_fa_home_posts ) ) {
			$q_args['post__not_in'] = $vce_fa_home_posts;
		}

		if ( isset( $vce_modules_exclude ) && !empty( $vce_modules_exclude ) ) {
			if ( !isset( $q_args['post__not_in'] ) ) {
				$q_args['post__not_in'] = array();
			}
			foreach ( $vce_modules_exclude as $ex ) {
				if ( !in_array( $ex, $q_args['post__not_in'] ) ) {
					$q_args['post__not_in'][] = $ex;
				}
			}
		}

		if ( !empty( $args['manual'] ) ) {

			$q_args['posts_per_page'] = absint( count( $args['manual'] ) );
			$q_args['orderby'] = 'post__in';
			$q_args['post__in'] = $args['manual'];
			$q_args['post_type'] = array_keys( get_post_types( array( 'public' => true ) ) ); //support all existing public post types

		} else {

			$q_args['posts_per_page'] = absint( $args['limit'] );

			if ( !empty( $args['exclude_by_id'] ) ) {

				if ( !empty( $q_args['post__not_in'] ) ) {
					$q_args['post__not_in'] = array_unique( array_merge( $q_args['post__not_in'], $args['exclude_by_id'] ) );
				} else {
					$q_args['post__not_in'] = $args['exclude_by_id'];
				}
			}

			if ( !empty( $args['tax'] ) ) {
				$taxonomies = array();
				foreach ( $args['tax'] as $k => $v ) {

					$temp = array();
					if ( !empty( $v ) ) {
						$temp['fields'] = 'id';
						$temp['taxonomy'] = $k;
						$temp['terms'] = $v;
						$temp['operator'] = $args[$k . '_inc_exc'] == 'not_in' ? 'NOT IN' : 'IN';
						$taxonomies[] = $temp;
					}
				}

				$q_args['tax_query'] = $taxonomies;
			}

			if ( !empty( $args['author'] ) ) {
				$q_args['author__' . $args['author_inc_exc']] = $args['author'];
			}

			$q_args['orderby'] = $args['order'];

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
								'key' => 'wp_review_user_review_type',
								'value' => $review_type,
							)
						);

					} else {

						$review_type = substr( $q_args['orderby'], 8, strlen( $q_args['orderby'] ) );

						$q_args['orderby'] = 'meta_value_num';
						$q_args['meta_key'] = 'wp_review_total';

						$q_args['meta_query'] = array(
							array(
								'key' => 'wp_review_type',
								'value' => $review_type,
							)
						);
					}

				}

			if ( $q_args['orderby'] == 'comments_number' ) {
				$q_args['orderby'] = 'comment_count';
			}

			$q_args['order'] = $args['sort'];

			$date_query = array();

			if ( $time_diff = $args['time'] ) {

				$date_query[0]['after'] = date( 'Y-m-d', vce_calculate_time_diff( $time_diff ) );
			}

			if ( $time_diff = $args['timeto'] ) {

				$date_query[0]['before'] = date( 'Y-m-d', vce_calculate_time_diff( $time_diff ) );
			}

			if ( !empty( $date_query ) ) {
				$q_args['date_query'] = $date_query;
			}

		}

		if ( $paged ) {
			$q_args['paged'] = $paged;
		}

		$q_args = apply_filters('vce_modify_module_query_cpt_args', $q_args );

		$query = new WP_Query( $q_args );

		if ( $args['exclude'] && !is_wp_error( $query ) && !empty( $query ) ) {

			foreach ( $query->posts as $p ) {
				$vce_modules_exclude[] = $p->ID;
			}

		}

		return $query;

	}
endif;


/* Define type of a module */
if ( !function_exists( 'vce_define_module_type' ) ):
	function vce_define_module_type( $mod ) {

		if ( !isset( $mod['type'] ) || empty( $mod['type'] ) ) {
			$mod['type'] = 'posts';
		}

		if ( !isset( $mod['active'] ) ) {
			$mod['active'] = 1;
		}

		return $mod;
	}
endif;

/* Creates category color bar on module top */
if ( !function_exists( 'vce_get_cat_class' ) ):
	function vce_get_cat_class( $mod ) {

		if ( $mod['type'] == 'posts' && isset( $mod['cat'] ) && !empty( $mod['cat'] ) && empty( $mod['manual'] ) ) {
			return 'cat-' . $mod['cat'][0];
		}

		return '';
	}
endif;


/* Wrap posts if layouts are combined or if slider is used */
if ( !function_exists( 'vce_loop_wrap_div' ) ):
	function vce_loop_wrap_div( $mod, $i, $real_count ) {

		$slider_allow = ( !$mod['top_layout'] && $real_count > 1 ) || ( $mod['top_layout'] && $real_count > absint( $mod['top_limit'] ) + 1 ) ? true : false;

		if ( $real_count < ( absint( $mod['top_limit'] ) + 1 ) ) {
			$mod['top_layout'] = 0;
		}

		if ( ( $mod['top_layout'] && $i == ( absint( $mod['top_limit'] ) + 1 ) ) || ( !$mod['top_layout'] && $i == 1 ) ) {
			if ( isset( $mod['action'] ) && $mod['action'] == 'slider' && $slider_allow ) {
				$slider_class = ' vce-slider-pagination vce-slider-' . $mod['layout'];
				if ( isset( $mod['autoplay'] ) && !empty( $mod['autoplay'] ) ) {
					$autoplay = 'data-autoplay="' . ( absint( $mod['autoplay'] ) * 1000 ) . '"';
				} else {
					$autoplay = '';
				}

			} else {
				$slider_class = '';
				$autoplay = '';
			}
			return '<div class="vce-loop-wrap' . $slider_class . '" ' . $autoplay . '>';
		}

		return '';
	}
endif;

/* Check which layout to display when two layouts are combined */
if ( !function_exists( 'vce_module_layout' ) ):
	function vce_module_layout( $mod, $i ) {

		$layout = $mod['top_layout'] && $i <= $mod['top_limit'] ? $mod['top_layout'] : $mod['layout'];

		return $layout;
	}
endif;


/* Check whether to remove padding in module (for layout A and G) */
if ( !function_exists( 'vce_get_mainbox_class' ) ):
	function vce_get_mainbox_class( $mod ) {

		if ( $mod['type'] == 'blank' )
			return '';

		$class = array();

		if ( in_array( $mod['layout'], array( 'a', 'g' ) ) && $mod['limit'] == 1 ) {
			$class[] = 'main-box-nopad';
		}

		if ( !empty( $class ) ) {
			return implode( " ", $class );
		}

		return '';
	}
endif;

/* Check if module is one-column */
if ( !function_exists( 'vce_get_column_class' ) ):
	function vce_get_column_class( $mod ) {

		$class = array();

		if ( isset( $mod['one_column'] ) && !empty( $mod['one_column'] ) ) {
			$class[] = 'main-box-half';
		}

		if ( !empty( $class ) ) {
			return implode( " ", $class );
		}

		return '';
	}
endif;

/* Check whether to open div wrapper for one-columned modules*/
if ( !function_exists( 'vce_open_column_wrap' ) ):
	function vce_open_column_wrap( $mod ) {
		global $vce_module_column_flag;

		if ( empty( $vce_module_column_flag ) && isset( $mod['one_column'] ) && !empty( $mod['one_column'] ) && vce_allow_onecolumn_module( $mod ) ) {

			$vce_module_column_flag = 1;
			return '<div class="vce-module-columns">';

		}

		return '';

	}
endif;

/* Check whether to close div wrapper for one-columned modules */
if ( !function_exists( 'vce_close_column_wrap' ) ):
	function vce_close_column_wrap( $modules, $k ) {
		global $vce_module_column_flag;
		if ( !empty( $vce_module_column_flag ) ) {
			if ( !isset( $modules[$k + 1] ) || !isset( $modules[$k + 1]['one_column'] ) || ( isset( $modules[$k + 1]['one_column'] ) && ( !vce_allow_onecolumn_module( $modules[$k + 1] ) ) ) ) {
				$vce_module_column_flag = 0;
				return '</div>';
			}
		}

		return '';

	}
endif;

/* Check if module is allowed to be one-column */
if ( !function_exists( 'vce_allow_onecolumn_module' ) ):
	function vce_allow_onecolumn_module( $mod ) {
		global $vce_module_column_flag;


		if ( !isset( $mod['type'] ) ) {
			$mod['type'] = 'posts';
		}

		if ( $mod['type'] == 'blank' || ( in_array( $mod['layout'], array( 'c', 'd', 'f', 'h' ) ) && in_array( $mod['top_layout'], array( '0', 'c', 'd', 'f', 'h' ) ) ) ) {
			return true;
		}
		return false;

	}
endif;

/* Check if module has additional actions */
if ( !function_exists( 'vce_check_module_action' ) ):
	function vce_check_module_action( $module, $module_query = false ) {

		$output = '';

		if ( !empty( $module['action'] ) ) {
			switch ( $module['action'] ) {

			case 'slider':
				break;

			case 'pagination':
				if(isset($module['paginated'])){
					ob_start();
					global $paged, $wp_query;
					if ( $module_paged = vce_module_template_is_paged() ) {
						$paged = $module_paged;
					}

					$temp_query = $wp_query;
					$wp_query = $module_query;
					get_template_part( 'sections/pagination/' . $module['pagination'] );
					$wp_query = $temp_query;
					$output = ob_get_contents();
					ob_end_clean();
				}
				break;

			case 'link':
				$output .= '<div id="vce-pagination"><a class="vce-button vce-action-link" href="' . esc_url( $module['action_link_url'] ) . '">' . esc_html( $module['action_link_text'] ) . '</a></div>';
				break;
			default:
				break;
			}
		}

		if ( !empty( $output ) ) {
			return $output;
		}
		return '';

	}
endif;

/* Get module title */
if ( !function_exists( 'vce_get_module_title' ) ):
	function vce_get_module_title( $module ) {

		$output = '';

		if ( !empty( $module['title'] ) ) {

			$output = esc_html( $module['title'] );

			if ( isset( $module['title_link'] ) && !empty( $module['title_link'] ) ) {
				$output = '<a href="' . esc_url( $module['title_link'] ) . '">' . $output . '</a>';
			}

		}

		return $output;

	}
endif;

/* Check if module have a custom class */
if ( !function_exists( 'vce_get_module_css_class' ) ):
	function vce_get_module_css_class( $module ) {

		if ( isset( $module['css_class'] ) && !empty( $module['css_class'] ) ) {
			return esc_attr( $module['css_class'] );
		}

		return '';

	}
endif;


/**
 * Get modules
 *
 * Parses module page template data and sets current module array
 *
 * @return array Modules data
 * @since  2.6
 */

if ( !function_exists( 'vce_get_modules' ) ):
	function vce_get_modules( ) {

		$meta = vce_get_page_meta( get_the_ID() );

		if ( empty( $meta['modules'] ) ) {
			return false;
		}

		$modules = array_map( 'vce_define_module_type', $meta['modules'] );
		$modules = vce_trim_inactive_modules( $modules );
		$modules = vce_set_paginated_module($modules);

		if ( vce_module_template_is_paged() ) {
			$modules = vce_parse_paged_module_template( $modules );
		}

		return $modules;

	}
endif;


/**
 * Module template is paged
 *
 * Check if we are on paginated modules page
 *
 * @return int|false
 * @since  1.0
 */

if ( !function_exists( 'vce_module_template_is_paged' ) ):
	function vce_module_template_is_paged() {
		$current_page = is_front_page() ? absint( get_query_var( 'page' ) ) : absint( get_query_var( 'paged' ) );
		return $current_page > 1 ? $current_page : false;
	}
endif;


/**
 * Parse paged module template
 *
 * When we are on paginated module page
 * pull only the last posts module and its section
 * but check queries for other modules
 *
 * @param array   $modules existing modules data
 * @return array Paginated module
 * @since  2.6
 */

if ( !function_exists( 'vce_parse_paged_module_template' ) ):
	function vce_parse_paged_module_template( $modules ) {

		if ( empty( $modules ) ) {
			return $modules;
		}

		foreach ( $modules as $n => $module ) {

			if ( isset($module['paginated']) ) {
				return array( 0 =>  $module );
			}

			if ( isset( $module['exclude'] ) && !empty( $module['exclude'] ) && $module['active'] ) {
				vce_get_module_query( $module );
			}
		}

		return $modules;

	}
endif;


/**
 * Check modules and sets 'paginated' parameter for the last module which has a pagination set
 *
 * @param array   $module module args
 * @return array   $module module args
 * @since  2.6
 */

if ( !function_exists( 'vce_set_paginated_module' ) ):
	function vce_set_paginated_module( $modules ) {

		$index = false;

		foreach ( $modules as $n => $module ) {

			if ( isset( $module['action'] ) && $module['action'] == 'pagination' ) {
				$index = $n;
			}
		}

		if($index !== false){
			$modules[$index]['paginated'] = true;
		}

		return $modules;
	}
endif;

/**
 * Trim inactive modules
 *
 * Check if module is active and remove it from modules array
 *
 * @param array   $modules
 * @return array active modules
 * @since  2.6
 */

if ( !function_exists( 'vce_trim_inactive_modules' ) ):
	function vce_trim_inactive_modules( $modules ) {

		if ( empty( $modules ) ) {
			return $modules;
		}

		foreach ( $modules as $k => $module ) {

			if ( !$module['active'] ) {
				unset( $modules[$k] );
			}

		}

		return array_values( $modules );
	}
endif;


/**
 * Get posts from manually selected field in modules  
 *
 * @since  2.7 
 *
 * @param srting $post_ids - Selected posts ids from choose manually meta field
 * @return array - List of selected posts or empty list
 */
if ( !function_exists( 'vce_get_manually_selected_posts' ) ):
	function vce_get_manually_selected_posts( $post_ids, $module_type = 'posts' ) {
		
		if ( empty($post_ids) ) {
			return array();
		}

		$post_type = in_array($module_type, array('posts', 'featured')) ? array_keys( get_post_types( array( 'public' => true ) ) ) : $module_type;

		$get_selected_posts = get_posts( 
			array(
				'post__in' => $post_ids, 
				'orderby' => 'post__in', 
				'post_type' => $post_type, 
				'posts_per_page' => '-1'
			) 
		);

		return wp_list_pluck( $get_selected_posts, 'post_title', 'ID' );
	}
endif;


/**
 * Display manualy selected posts  
 *
 * @since  2.7 
 *
 * @param array $posts - Array of manualy selected posts
 * @return HTML - Title of manualy selected post
 */
if ( !function_exists( 'vce_display_manually_selected_posts' ) ):
	function vce_display_manually_selected_posts($posts) {
		
		if ( empty($posts) ) {
			return;
		}

		$output = '';
	 	foreach ( $posts as $id => $title ){
			$output .= '<span><button type="button" class="ntdelbutton" data-id="'. esc_attr($id) .'"><span class="remove-tag-icon"></span></button><span class="vce-searched-title">'. esc_html( $title ). '</span></span>';
		} 

		echo $output;
	}
endif;

/**
 * Used for getting post types with all taxonomies
 *
 * @return array
 * @since    2.7.1
 */
if (!function_exists('vce_get_posts_types_with_taxonomies')):
	function vce_get_posts_types_with_taxonomies( $exclude = array() ) {
		
		$post_types_with_taxonomies = array();
		
		$post_types = vce_get_custom_post_types( true );
		$post_types[] = get_post_type_object('post');
		
		if (empty($post_types))
			return null;
		
		foreach ($post_types as $post_type) {
			if(in_array($post_type->name, $exclude)){
				continue;
			}
			
			$post_taxonomies = vce_get_taxonomies($post_type->name);
			
			$post_type->taxonomies = $post_taxonomies;
			$post_types_with_taxonomies[] = $post_type;
		}
		
		return apply_filters('vce_modify_posts_types_with_taxonomies', $post_types_with_taxonomies);
	}
endif;

/**
 * Now when taxonomies are dynamical in featrued area depanding on post type we have to overwrite old settings.
 * For Category to cat and for post_tag to tag
 *
 * @string $taxonomy_id
 * @since 1.9.1
 * @return $taxonomy_id
 */
if(!function_exists('vce_patch_category_and_tags')):
	function vce_patch_taxonomy_id($taxonomy_id){
		
		if ( in_array( $taxonomy_id, array( 'category', 'post_tag' ) ) ) {
			if ( $taxonomy_id === 'category' ) {
				$taxonomy_id = 'cat';
			}
			if ( $taxonomy_id === 'post_tag' ) {
				$taxonomy_id = 'tag';
			}
		}
		
		return $taxonomy_id;
	}
endif;
?>