<?php

	add_action('wp_ajax_vce_hide_welcome', 'vce_hide_welcome');
	add_action('wp_ajax_vce_update_version', 'vce_update_version');
	add_action('wp_ajax_nopriv_vce_mega_menu', 'vce_generate_mega_menu_content');
	add_action('wp_ajax_vce_mega_menu', 'vce_generate_mega_menu_content');


	/* Update latest theme version (we use internally for new version introduction text) */
	function vce_update_version(){
		update_option('vce_theme_version',THEME_VERSION);
		die();
	}

	/* Update latest theme version */
	function vce_hide_welcome(){
		update_option('vce_welcome_box_displayed', true);
		die();
	}

	function vce_generate_mega_menu_content(){
	
		if(!isset($_POST['cat']))
			die();

		$cat = absint($_POST['cat']);

		if ( isset( $_GET[ 'wpml_lang' ] ) ) {
			do_action( 'wpml_switch_language',  $_GET[ 'wpml_lang' ] ); // switch the content language
		}

		$output = vce_load_mega_menu($cat);

		echo $output;

		die();
	}



/**
 * Get searched posts or pages on ajax call for auto-complete functionality
 * 
 */
add_action( 'wp_ajax_vce_ajax_search', 'vce_ajax_search' );

if ( !function_exists( 'vce_ajax_search' ) ):
	function vce_ajax_search() {
		
		$post_type = in_array($_GET['type'], array('posts', 'featured')) ? array_keys( get_post_types( array( 'public' => true ) ) ) : $_GET['type'];
		
		$posts = get_posts( array(
				's' => $_GET['term'],
				'post_type' => $post_type,
				'posts_per_page' => -1
			) );

		$suggestions = array();

		global $post;
		
		foreach ( $posts as $post ) {
			setup_postdata( $post );
			$suggestion = array();
			$suggestion['label'] = esc_html( $post->post_title );
			$suggestion['id'] = $post->ID;
			$suggestions[]= $suggestion;
		}

		$response = $_GET["callback"] . "(" . json_encode( $suggestions ) . ")";

		echo $response;

		die();
	}
endif;


?>