<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider;

class ShortCodes {
    public static function staticConstruct() {
        add_action( 'init', __NAMESPACE__ . '\\ShortCodes::init' );
        add_action( 'init', __NAMESPACE__ . '\\ShortCodes::add_button' );
    }

    public static function init() {
        add_shortcode( 'tps_header', __NAMESPACE__ . '\\ShortCodes::shortcode_header_handler' );
        add_shortcode( 'tps_title', __NAMESPACE__ . '\\ShortCodes::shortcode_title_handler' );
        add_shortcode( 'tps_footer', __NAMESPACE__ . '\\ShortCodes::shortcode_footer_handler' );
        add_shortcode( 'tps_start_button', __NAMESPACE__ . '\\ShortCodes::shortcode_start_button_handler' );
    }

    public static function shortcode_header_handler( $atts, $content = null ) {
        global $post;

        if ( is_object( $post ) && Content::havePostData( $post->ID ) ) {
            return Misc::$begin_header_short_code . $content . Misc::$end_header_short_code;
        }

        return $content;
    }

    public static function shortcode_title_handler( $atts, $content = null ) {
        global $post;

        if ( Content::havePostData( $post->ID ) ) {
            return Misc::$begin_title_short_code . $content . Misc::$end_title_short_code;
        }

        return $content;
    }

    public static function shortcode_footer_handler( $atts, $content = null ) {
        global $post;

        if ( Content::havePostData( $post->ID ) ) {
            return Misc::$begin_footer_short_code . $content . Misc::$end_footer_short_code;
        }

        return $content;
    }

    public static function shortcode_start_button_handler( $atts, $content = null ) {
        global $page;

        $defaults = array(
            'label' => 'Start slideshow',
            'style' => '',
            'class' => ''
        );
        $atts     = array_merge( $defaults, $atts );
        $nextPost = htmlspecialchars( Misc::get_post_page_url( $page + 1 ) );
        $button   = "<a href='$nextPost'
                        onclick='if (tpsInstance) { tpsInstance.setNext(); return false; }'
                        rel='next'
                        style='${atts['style']}'
                        class='${atts['class']}'>${atts['label']}</a>";

        return $button;
    }

    // Add buttons to the post editor
    public static function add_button() {
        if ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) {
            add_filter( 'mce_external_plugins', __NAMESPACE__ . '\\ShortCodes::add_plugin' );
            add_filter( 'mce_buttons_2', __NAMESPACE__ . '\\ShortCodes::register_button' );
        }
    }

    public static function add_plugin( $plugin_array ) {
        $plugin_array['theiaPostSlider'] = THEIA_POST_SLIDER_PLUGINS_URL . 'dist/js/tps-tinymce-customcodes.js';

        return $plugin_array;
    }

    public static function register_button( $buttons ) {
        array_push( $buttons, '|', 'tps_header', 'tps_title', 'tps_footer', 'tps_start_button' );

        return $buttons;
    }

    // Extract a shortcode from a string.
    public static function extract_short_code( &$content, $beginShortCode, $endShortCode, $parseParagraphs = true ) {
        // Find the opening tag
        $begin = Misc::mb_strpos( $content, $beginShortCode );
        if ( $begin === false ) {
            return null;
        }

        // Find the closing tag
        $end = Misc::mb_strpos( $content, $endShortCode, $begin );
        if ( $end === false ) {
            return null;
        }

        // Cache some string lengths
        $lenBegin = Misc::mb_strlen( $beginShortCode );
        $lenEnd   = Misc::mb_strlen( $endShortCode );

        // If the shortcodes are surrounded by header tags, then extract them too.
        $beginHeadingTag = $endHeadingTag = '';
        if (
            preg_match( '(<h([1-6])>)', Misc::mb_substr( $content, $begin - 4, 4 ), $beginMatches ) &&
            preg_match( '(</h([1-6])>)', Misc::mb_substr( $content, $end + $lenEnd, 5 ), $endMatches ) &&
            $beginMatches[1] === $endMatches[1]
        ) {
            $beginHeadingTag = $beginMatches[0];
            $endHeadingTag   = $endMatches[0];
        }

        // Extract shortcode content.
        $shortCode = $beginHeadingTag . trim( Misc::mb_substr( $content, $begin + $lenBegin, $end - $begin - $lenBegin ) ) . $endHeadingTag;

        // Parse newlinews.
        if ( $parseParagraphs ) {
            $shortCode = wpautop( $shortCode );
        }

        // Get resulting post content by removing the shortcode and its content.
        {
            $split1 = $begin - Misc::mb_strlen( $beginHeadingTag );
            $split2 = $end + $lenEnd + Misc::mb_strlen( $endHeadingTag );

            // Remove trailing paragraph tags.
            if (
                Misc::mb_substr( $content, $split1 - 3, 3 ) == '<p>' &&
                Misc::mb_substr( $content, $split2, 4 ) == '</p>'
            ) {
                $split1 -= 4;
                $split2 += 4;
            }

            $content = trim( Misc::mb_substr( $content, 0, $split1 ) ) . trim( Misc::mb_substr( $content, $split2 ) );
        }

        return $shortCode;
    }

    public static function tps_header( $atts, $content = null ) {
        return $content;
    }

    public static function tps_footer( $atts, $content = null ) {
        return $content;
    }

    public static function tps_title( $atts, $content = null ) {
        return $content;
    }
}
