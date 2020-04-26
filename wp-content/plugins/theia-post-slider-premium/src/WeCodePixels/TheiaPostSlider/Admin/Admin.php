<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider\Admin;

use WeCodePixels\TheiaPostSlider\Options;

class Admin {
    public static function staticConstruct() {
        add_action( 'admin_init', __NAMESPACE__ . '\\Admin::admin_init' );
        add_action( 'admin_menu', __NAMESPACE__ . '\\Admin::admin_menu' );
    }

    public static function admin_init() {
        register_setting( 'tps_options_dashboard', 'tps_dashboard', __NAMESPACE__ . '\\Admin::validate' );
        register_setting( 'tps_options_general', 'tps_general', __NAMESPACE__ . '\\Admin::validate' );
        register_setting( 'tps_options_nav', 'tps_nav', __NAMESPACE__ . '\\Admin::validate' );
        register_setting( 'tps_options_advanced', 'tps_advanced', __NAMESPACE__ . '\\Admin::validate' );
        register_setting( 'tps_options_advanced', 'tps_advanced_post_types', __NAMESPACE__ . '\\Admin::validate' );
        register_setting( 'tps_options_troubleshooting', 'tps_troubleshooting', __NAMESPACE__ . '\\Admin::validate' );
    }

    public static function admin_menu() {
        add_options_page( 'Theia Post Slider Settings', 'Theia Post Slider', 'manage_options', 'theia-post-slider', __NAMESPACE__ . '\\Admin::do_page' );
    }

    public static function do_page() {
        $tabs = array(
            'dashboard'       => array(
                'title' => __( "Dashboard", 'theia-post-slider' ),
                'class' => __NAMESPACE__ . '\\Dashboard'
            ),
            'general'         => array(
                'title' => __( "General", 'theia-post-slider' ),
                'class' => __NAMESPACE__ . '\\General'
            ),
            'navigationBar'   => array(
                'title' => __( "Navigation Bar", 'theia-post-slider' ),
                'class' => __NAMESPACE__ . '\\NavigationBar'
            ),
            'advanced'        => array(
                'title' => __( "Advanced", 'theia-post-slider' ),
                'class' => __NAMESPACE__ . '\\Advanced'
            ),
            'troubleshooting' => array(
                'title' => __( "Troubleshooting", 'theia-post-slider' ),
                'class' => __NAMESPACE__ . '\\Troubleshooting'
            ),
            'account'         => array(
                'title' => __( "Account", 'theia-post-slider' ),
                'class' => __NAMESPACE__ . '\\Account'
            ),
            'addons'          => array(
                'title' => __( "Add-Ons", 'theia-post-slider' ),
                'class' => __NAMESPACE__ . '\\AddOns'
            ),
            'contact'         => array(
                'title' => __( "Contact Us", 'theia-post-slider' ),
                'class' => __NAMESPACE__ . '\\Contact'
            )
        );

        // Remove contact tab for theme licenses.
        global $theia_post_slider_fs;
        if ( $theia_post_slider_fs->is_plan( 'theme_bundle', true ) ) {
            unset( $tabs['contact'] );
        }

        // Allow add-ons to add other tabs.
        $tabs = apply_filters( 'tps_admin_tabs', $tabs );

        if ( array_key_exists( 'tab', $_GET ) && array_key_exists( $_GET['tab'], $tabs ) ) {
            $current_tab = $_GET['tab'];
        } else {
            $current_tab = 'dashboard';
        }
        ?>

        <div class="wrap">
            <h2 class="theiaPostSlider_adminTitle">
                <a href="https://wecodepixels.com/theia-post-slider-for-wordpress/?utm_source=theia-post-slider-for-wordpress"
                   target="_blank"><img src="<?php echo plugins_url( '/assets/images/theia-post-slider-thumbnail.png', THEIA_POST_SLIDER_MAIN ); ?>"></a>

                Theia Post Slider

                <a class="theiaPostSlider_adminLogo"
                   href="https://wecodepixels.com/?utm_source=theia-post-slider-for-wordpress"
                   target="_blank"><img src="<?php echo plugins_url( '/assets/images/wecodepixels-logo.png', THEIA_POST_SLIDER_MAIN ); ?>"></a>
            </h2>

            <h2 class="nav-tab-wrapper">
                <?php
                foreach ( $tabs as $id => $tab ) {
                    $class = 'nav-tab';
                    if ( $id == $current_tab ) {
                        $class .= ' nav-tab-active';
                    }
                    ?>
                    <a href="?page=theia-post-slider&tab=<?php echo $id; ?>"
                       class="<?php echo $class; ?>"><?php echo $tab['title']; ?></a>
                    <?php
                }
                ?>
            </h2>

            <?php
            $class       = $tabs[ $current_tab ]['class'];
            $page        = new $class;
            $showPreview = property_exists( $page, 'showPreview' ) && $page->showPreview;

            // Must enqueue this $(document).ready script first.
            if ( $showPreview ) {
                $sliderOptions = array(
                    'slideContainer'    => '#tps_slideContainer',
                    'nav'               => array( '#tps_nav_upper', '#tps_nav_lower' ),
                    'navText'           => Options::get( 'navigation_text' ),
                    'helperText'        => Options::get( 'helper_text' ),
                    'transitionEffect'  => Options::get( 'transition_effect' ),
                    'transitionSpeed'   => (int) Options::get( 'transition_speed' ),
                    'keyboardShortcuts' => true,
                    'themeType'         => Options::get( 'theme_type' ),
                    'prevText'          => Options::get( 'prev_text' ),
                    'nextText'          => Options::get( 'next_text' ),
                    'prevFontIcon'      => Options::get_font_icon( is_rtl() ? 'right' : 'left' ),
                    'nextFontIcon'      => Options::get_font_icon( is_rtl() ? 'left' : 'right' ),
                    'buttonWidth'       => Options::get( 'button_width' ),
                    'numberOfSlides'    => 3,
                    'is_rtl'            => is_rtl()
                );

                ?>
                <script type='text/javascript'>
                    var slider;

                    jQuery(document).ready(function () {
                        slider = new tps.createSlideshow(<?php echo json_encode( $sliderOptions ); ?>);
                    });
                </script>
                <?php
            }
            ?>

            <div class="theiaPostSlider_adminContainer <?php echo $showPreview ? 'hasPreview' : ''; ?>">
                <div class="theiaPostSlider_adminContainer_left">
                    <div class="theia-post-slider-admin-<?= $current_tab ?>">
                        <?php
                        $page->echoPage();
                        ?>
                    </div>
                </div>

                <div class="theiaPostSlider_adminContainer_right">
                    <?php
                    if ( $showPreview == true ) {
                        ?>
                        <h3><?php _e( "Live Preview", 'theia-post-slider' ); ?></h3>
                        <div class="theiaPostSlider_adminPreview">
                            <?php
                            echo \WeCodePixels\TheiaPostSlider\NavigationBar::get_navigation_bar( array(
                                'currentSlide' => 1,
                                'totalSlides'  => 3,
                                'id'           => 'tps_nav_upper',
                                'class'        => '_upper',
                                'style'        => in_array( Options::get( 'nav_vertical_position' ), array(
                                    'top_and_bottom',
                                    'top'
                                ) ) ? '' : 'display: none'
                            ) );
                            ?>
                            <div id="tps_slideContainer" class="theiaPostSlider_slides">
                                <?php
                                PreviewSlider::echoPreviewSlider();
                                ?>
                            </div>
                            <?php
                            echo \WeCodePixels\TheiaPostSlider\NavigationBar::get_navigation_bar( array(
                                'currentSlide' => 1,
                                'totalSlides'  => 3,
                                'id'           => 'tps_nav_lower',
                                'class'        => '_lower',
                                'style'        => in_array( Options::get( 'nav_vertical_position' ), array(
                                    'top_and_bottom',
                                    'bottom'
                                ) ) ? '' : 'display: none'
                            ) );
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    public static function validate( $input ) {
        return $input;
    }
}
