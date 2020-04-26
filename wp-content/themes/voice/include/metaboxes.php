<?php
/*-----------------------------------------------------------------------------------*/
/*	Add Metaboxes
/*-----------------------------------------------------------------------------------*/

add_action('load-post.php', 'vce_meta_boxes_setup');
add_action('load-post-new.php', 'vce_meta_boxes_setup');

/* Meta box setup function. */
if (!function_exists('vce_meta_boxes_setup')) :
    function vce_meta_boxes_setup()
    {
        global $typenow;
        if ($typenow == 'page') {
            add_action('add_meta_boxes', 'vce_load_page_metaboxes');
            add_action('save_post', 'vce_save_page_metaboxes', 10, 2);
        }

        if ($typenow == 'post') {
            add_action('add_meta_boxes', 'vce_load_post_metaboxes');
            add_action('save_post', 'vce_save_post_metaboxes', 10, 2);
        }
    }
endif;

include_once( get_template_directory().'/include/admin/metaboxes/post.php');
include_once( get_template_directory().'/include/admin/metaboxes/page.php');
include_once( get_template_directory().'/include/admin/metaboxes/category.php');
?>