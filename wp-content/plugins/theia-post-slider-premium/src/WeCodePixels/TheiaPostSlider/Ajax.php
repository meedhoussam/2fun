<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider;

class Ajax {
    public static function staticConstruct() {
        add_action( 'wp_ajax_tps_get_slides', __NAMESPACE__ . '\\Ajax::wp_ajax_nopriv_tps_get_slides' );
        add_action( 'wp_ajax_nopriv_tps_get_slides', __NAMESPACE__ . '\\Ajax::wp_ajax_nopriv_tps_get_slides' );
    }

    public static function wp_ajax_nopriv_tps_get_slides() {
        if (
            ! array_key_exists( 'postId', $_POST ) ||
            ! array_key_exists( 'slides', $_POST )
        ) {
            return;
        }

        $loadTime = microtime( true );

        // Just in case any other plugin echoes something and ruins our JSON.
        ob_start();

        // Compatibility with the AdRotate plugin - https://wordpress.org/plugins/adrotate/
        {
            global $shortcode_tags;

            if ( function_exists( 'adrotate_shortcode' ) && defined( 'ADROTATE_VERSION' ) && ! array_key_exists( 'adrotate', $shortcode_tags ) ) {
                add_shortcode( 'adrotate', 'adrotate_shortcode' );
            }
        }

        Misc::$force_disable = true;

        // Get post.
        global $post, $pages;
        $post = get_post( $_POST['postId'] );
        if ( $post === null ) {
            exit();
        }
        setup_postdata( $post );
        query_posts( 'p=' . $_POST['postId'] );

        // Get and process each slide.
        $requestedSlides = $_POST['slides'];
        $slides          = array();
        foreach ( $requestedSlides as $i ) {
            $slides[ $i ] = Misc::get_sub_page( $i + 1, null );
        }

        // Add previous and next slide permalinks.
        {
            $last_slide = count( $pages ) - 1;

            if ( in_array( $last_slide, $requestedSlides ) && Options::get( 'button_behaviour', 'tps_nav' ) === 'loop' ) {
                $url = Misc::get_post_page_url( 1 );
                if ( $url ) {
                    $slides[0] = array(
                        'permalink' => $url
                    );
                }
            }

            if ( in_array( 0, $requestedSlides ) && Options::get( 'button_behaviour', 'tps_nav' ) === 'loop' ) {
                $url = Misc::get_post_page_url( $last_slide + 1 );
                if ( $url ) {
                    $slides[ $last_slide ] = array(
                        'permalink' => $url
                    );
                }
            }

            $previous = min( $requestedSlides ) - 1;
            $url      = Misc::get_post_page_url( $previous + 1 );
            if ( $url ) {
                $slides[ $previous ] = array(
                    'permalink' => $url
                );
            }

            $next = max( $requestedSlides ) + 1;
            $url  = Misc::get_post_page_url( $next + 1 );
            if ( $url ) {
                $slides[ $next ] = array(
                    'permalink' => $url
                );
            }
        }

        $loadTime = microtime( true ) - $loadTime;

        $result = array(
            'postId'   => $post->ID,
            'slides'   => $slides,
            'loadTime' => $loadTime
        );

        $result = apply_filters( 'tps_ajax_get_slides_result', $result );

        ob_clean();

        header( 'Content-Type: application/json; charset=utf-8' );
        echo json_encode( $result );

        exit();
    }
}
