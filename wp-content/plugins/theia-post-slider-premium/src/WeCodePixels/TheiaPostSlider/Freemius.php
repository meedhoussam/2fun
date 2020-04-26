<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */
namespace WeCodePixels\TheiaPostSlider;

class Freemius
{
    public static function staticConstruct()
    {
        // Create a helper function for easy SDK access.
        function theia_post_slider_fs()
        {
            global  $theia_post_slider_fs ;
            
            if ( !isset( $theia_post_slider_fs ) ) {
                // Include Freemius SDK.
                require_once THEIA_POST_SLIDER_DIR . '/vendor/freemius/wordpress-sdk/start.php';
                $theia_post_slider_fs = fs_dynamic_init( array(
                    'id'               => '2322',
                    'slug'             => 'theia-post-slider',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_dd9399a2667824b88646a35aa4a4b',
                    'is_premium'       => true,
                    'is_premium_only'  => true,
                    'has_addons'       => true,
                    'has_paid_plans'   => true,
                    'is_org_compliant' => false,
                    'menu'             => array(
                    'slug'       => 'theia-post-slider',
                    'first-path' => 'options-general.php?page=theia-post-slider',
                    'contact'    => false,
                    'support'    => false,
                    'account'    => false,
                    'pricing'    => false,
                    'parent'     => array(
                    'slug' => 'options-general.php',
                ),
                ),
                    'is_live'          => true,
                ) );
            }
            
            return $theia_post_slider_fs;
        }
        
        // Init Freemius.
        theia_post_slider_fs();
        // Signal that SDK was initiated.
        do_action( 'theia_post_slider_fs_loaded' );
        // Init Freemius through our framework.
        global  $theia_post_slider_fs ;
        require THEIA_POST_SLIDER_DIR . '/vendor/wecodepixels/wordpress-plugin/Freemius.php';
        \WeCodePixels\TheiaPostSliderFramework\Freemius::init( $theia_post_slider_fs, __NAMESPACE__ . '\\Admin\\Admin' );
    }

}