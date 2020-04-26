<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider;

class Misc {
    public static $begin_header_short_code = '[tps_header]';
    public static $end_header_short_code = '[/tps_header]';
    public static $begin_title_short_code = '[tps_title]';
    public static $end_title_short_code = '[/tps_title]';
    public static $begin_footer_short_code = '[tps_footer]';
    public static $end_footer_short_code = '[/tps_footer]';
    // The posts for which we have enabled the slider (i.e. the script has been appended).
    public static $posts_with_slider = array();
    // If true, the slider won't be added to posts.
    public static $force_disable = false;
    // Forcefully add beginning and ending comment.
    public static $force_begin_and_end_comments = false;
    // The [tps_title] of the current post.
    public static $current_post_title = null;
    // The current post's previous post URL.
    public static $post_previous_post_url = null;
    // The current post's next post URL.
    public static $post_next_post_url = null;

    public static function staticConstruct() {
        add_action( 'init', __NAMESPACE__ . '\\Misc::init' );
        add_action( 'get_header', __NAMESPACE__ . '\\Misc::remove_canonical_url' );
        add_action( 'get_the_excerpt', __NAMESPACE__ . '\\Misc::before_get_the_excerpt', - 999999 );
        add_action( 'get_the_excerpt', __NAMESPACE__ . '\\Misc::after_get_the_excerpt', 999999 );
        add_filter( 'mce_buttons', __NAMESPACE__ . '\\Misc::wysiwyg_editor', 999999 );
        add_filter( 'body_class', __NAMESPACE__ . '\\Misc::body_class' );
        add_filter( 'plugin_action_links_' . plugin_basename( THEIA_POST_SLIDER_MAIN ), __NAMESPACE__ . '\\Misc::plugin_action_links' );
    }

    public static function init() {
        // Define the plugin URL.
        if ( ! defined( 'THEIA_POST_SLIDER_PLUGINS_URL' ) ) {
            define( 'THEIA_POST_SLIDER_PLUGINS_URL', plugins_url( '', THEIA_POST_SLIDER_MAIN ) . '/' );
        }
    }

    // The first filter that is called for get_the_excerpt.
    public static function before_get_the_excerpt( $excerpt ) {
        self::$force_disable = true;

        if ( Options::get( 'add_header_to_excerpt' ) && $excerpt == '' ) {
            $text = get_the_content( '' );
            $text = str_replace( array( '[tps_header]', '[/tps_header]', '[tps_footer]', '[/tps_footer]' ), '', $text );

            $text = strip_shortcodes( $text );

            /** This filter is documented in wp-includes/post-template.php */
            $text = apply_filters( 'the_content', $text );
            $text = str_replace( ']]>', ']]&gt;', $text );

            /**
             * Filter the number of words in an excerpt.
             *
             * @since 2.7.0
             *
             * @param int $number The number of words. Default 55.
             */
            $excerpt_length = apply_filters( 'excerpt_length', 55 );
            /**
             * Filter the string in the "more" link displayed after a trimmed excerpt.
             *
             * @since 2.9.0
             *
             * @param string $more_string The string shown within the more link.
             */
            $excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
            $text         = wp_trim_words( $text, $excerpt_length, $excerpt_more );

            return apply_filters( 'wp_trim_excerpt', $text, $excerpt );
        }

        return $excerpt;
    }

    // The last filter that is called for get_the_excerpt.
    public static function after_get_the_excerpt( $excerpt ) {
        self::$force_disable = false;

        return $excerpt;
    }

    // Is this post a "post" or a "page" (i.e. should we display the slider)?
    public static function is_compatible_post( $post = null ) {
        $post         = get_post( $post );
        $value        = ( is_single() || is_page() || is_attachment() ) && in_array( get_post_type(), Options::get_post_types() );
        $isCompatible = true;

        if ( $value == false ) {
            $isCompatible = false;
        } else if ( $post ) {
            if ( false == PostOptions::get_post_option_enabled( $post->ID ) ) {
                $isCompatible = false;
            }
        }

        $isCompatible = apply_filters( 'tps_is_compatible_post', $isCompatible, $post );

        return $isCompatible;
    }

    // Get the previous or next post.
    public static function get_prev_next_post( $previous ) {
        if ( ! Options::get( 'button_behaviour' ) === 'post' ) {
            return null;
        }

        if ( Options::get( 'post_navigation_inverse' ) ) {
            $previous = ! $previous;
        }

        $post = get_adjacent_post( Options::get( 'post_navigation_same_category' ), '', $previous );
        if ( ! $post ) {
            return null;
        }

        return $post->ID;
    }

    public static function get_sub_page( $pageNumber, $currentPageNumber = null ) {
        global $page, $pages, $more;

        // Set new page number
        $page               = $pageNumber;
        $slide              = array();
        $slide['title']     = self::get_page_title();
        $slide['permalink'] = self::get_post_page_url( $page );

        // In case the page has a <!--more--> tag, we have to set this global variable in order to get the full content of the page.
        // Necessary only on AJAX requests, since on single post pages this variable is already set by WordPress.
        $more = - 1;

        // Get content
        $slideContent = get_the_content();

        // Remove header shortcode.
        if ( $pageNumber == 1 ) {
            ShortCodes::extract_short_code( $slideContent, Misc::$begin_header_short_code, Misc::$end_header_short_code );
        }

        // Remove footer shortcode.
        if ( $pageNumber == ( is_array( $pages ) ? count( $pages ) : 1 ) ) {
            ShortCodes::extract_short_code( $slideContent, Misc::$begin_footer_short_code, Misc::$end_footer_short_code );
        }

        // Save the shortcode title, if present.
        $slide['shortCodeTitle'] = ShortCodes::extract_short_code( $slideContent, Misc::$begin_title_short_code, Misc::$end_title_short_code );

        // Apply filters.
        Misc::$force_begin_and_end_comments = true;
        $slideContent                       = apply_filters( 'the_content', $slideContent );
        Misc::$force_begin_and_end_comments = false;
        $slideContent                       = str_replace( ']]>', ']]&gt;', $slideContent );

        /*
		 * Leave only the actual text. Aditional headers or footers will be discarded. Plugins like "video quicktags"
		 * will be left intact, while plugins like "related posts thumbnails" and "better author bio" will be discarded.
		 */
        $split_content = Misc::split_content( $slideContent );
        $slideContent  = $split_content['content'];

        $slide['content'] = $slideContent;

        // Set back page number.
        $page = $currentPageNumber;

        return $slide;
    }

    // Add the "page break" button to the post editor.
    public static function wysiwyg_editor( $mce_buttons ) {
        // Check if the "page break" button exists.
        $pos = array_search( 'wp_page', $mce_buttons, true );
        if ( $pos !== false ) {
            return $mce_buttons;
        }

        // Add the "page break" button after the "more" button.
        $pos = array_search( 'wp_more', $mce_buttons, true );
        if ( $pos !== false ) {
            $tmp_buttons   = array_slice( $mce_buttons, 0, $pos + 1 );
            $tmp_buttons[] = 'wp_page';
            $mce_buttons   = array_merge( $tmp_buttons, array_slice( $mce_buttons, $pos + 1 ) );
        }

        return $mce_buttons;
    }

    // Get the URL of a post's subpage. $i is 1-indexed.
    // e.g. get_post_page_url(5) = http://wordpress.dev/?p=1&page=5
    public static function get_post_page_url( $i ) {
        global $post, $wp_rewrite, $pages;
        if (
            ! $post ||
            $i < 1 ||
            ( is_array( $pages ) && $i > count( $pages ) )
        ) {
            return null;
        }
        if ( 1 == $i ) {
            $url = get_permalink();
        } else {
            if (
                '' == get_option( 'permalink_structure' ) ||
                in_array( $post->post_status, array( 'draft', 'pending' ) )
            ) {
                $url = add_query_arg( 'page', $i, get_permalink() );
            } elseif ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $post->ID ) {
                $url = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
            } else {
                $url = trailingslashit( get_permalink() ) . user_trailingslashit( $i, 'single_paged' );
            }
        }

        return $url;
    }

    /**
     * Tries to get the correct title of the page. Very hackish, but there's no other way.
     * @return string
     */
    public static function get_page_title() {
        // Set the current page of the WP query since it's used by SEO plugins.
        global $wp_query, $page;
        $oldPage = $wp_query->get( 'page' );
        if ( $page > 1 ) {
            $wp_query->set( 'page', $page );
        } else {
            $wp_query->set( 'page', null );
        }

        // Get the title.
        $title = self::get_page_titleHelper();
        $title = html_entity_decode( $title, ENT_QUOTES, 'UTF-8' );

        // Set back the current page.
        $wp_query->set( 'page', $oldPage );

        // Return the title.
        return $title;
    }

    private static function get_page_titleHelper() {
        // If the WordPress SEO plugin is active and compatible.
        global $wpseo_front;
        if (
            isset( $wpseo_front ) &&
            method_exists( $wpseo_front, 'title' )
        ) {
            return $wpseo_front->title( '', false );
        }

        // If the SEO Ultimate plugin is active and compatible.
        global $seo_ultimate;
        if (
            isset( $seo_ultimate ) &&
            property_exists( $seo_ultimate, 'modules' ) &&
            isset( $seo_ultimate->modules['titles'] ) &&
            method_exists( $seo_ultimate->modules['titles'], 'get_title' )
        ) {
            @$title = $seo_ultimate->modules['titles']->get_title();

            return $title;
        }

        // If all else fails, return the standard WordPress title. Unfortunately, most theme hard-code their <title> tag.
        return wp_title( '', false );
    }

    /*
	 * Split a string using the beginning and ending post content separator.
	 */
    public static function split_content( $content ) {
        $begin = Misc::mb_strpos( $content, Options::get_beginning_post_content_separator() );
        $end   = Misc::mb_strpos( $content, Options::get_ending_post_content_separator() );

        if ( $begin !== false && $end !== false ) {
            // Cut!
            $beforeContent = Misc::mb_substr( $content, 0, $begin );
            $afterContent  = Misc::mb_substr( $content, $end );

            $begin   += Misc::mb_strlen( Options::get_beginning_post_content_separator() );
            $content = Misc::mb_substr( $content, $begin, $end - $begin );
        } else {
            $beforeContent = '';
            $afterContent  = '';
        }

        // Trim left and right whitespaces.
        $content = trim( $content );

        return array(
            'beforeContent' => $beforeContent,
            'content'       => $content,
            'afterContent'  => $afterContent
        );
    }

    public static function multibyte_functions_exist() {
        // Cache result.
        static $result;

        if ( $result === null ) {
            $result = function_exists( 'mb_substr' ) && function_exists( 'mb_strlen' ) && function_exists( 'mb_strpos' );
        }

        return $result;
    }

    public static function mb_substr() {
        $args = func_get_args();
        if ( self::multibyte_functions_exist() ) {
            return call_user_func_array( 'mb_substr', $args );
        }

        return call_user_func_array( 'substr', $args );
    }

    public static function mb_strlen() {
        $args = func_get_args();
        if ( self::multibyte_functions_exist() ) {
            return call_user_func_array( 'mb_strlen', $args );
        }

        return call_user_func_array( 'strlen', $args );
    }

    public static function mb_strpos() {
        $args = func_get_args();
        if ( self::multibyte_functions_exist() ) {
            return call_user_func_array( 'mb_strpos', $args );
        }

        return call_user_func_array( 'strpos', $args );
    }

    public static function remove_canonical_url() {
        global $post;

        if (
            Misc::$force_disable ||
            ! Misc::is_compatible_post( $post )
        ) {
            return;
        } else {
            if ( Options::get( 'remove_canonical_rel' ) === true ) {
                remove_action( 'wp_head', 'rel_canonical' );
            }
        }
    }

    public static function body_class( $classes ) {
        global $post, $pages;

        if ( Misc::is_compatible_post( $post ) ) {
            $classes[] = 'theiaPostSlider_body';

            if ( is_array( $pages ) && count( $pages ) > 1 ) {
                $classes[] = 'theiaPostSlider_bodyWithMultiplePages';
            }
        }

        return $classes;
    }

    public static function plugin_action_links( $links ) {
        $mylinks = array(
            '<a href="' . admin_url( 'options-general.php?page=theia-post-slider' ) . '">Settings</a>',
        );

        return array_merge( $mylinks, $links );
    }
}
