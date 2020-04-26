<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider;

class Enqueues {
    public static function staticConstruct() {
        add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\Enqueues::wp_enqueue_scripts' );
        add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\Enqueues::admin_enqueue_scripts' );
        add_filter( 'script_loader_tag', __NAMESPACE__ . '\\Enqueues::script_loader_tag', 10, 2 );
    }

    // Enqueue the required JavaScript for a given transition effect.
    public static function enqueue_transition( $transition ) {
        wp_register_script( 'theiaPostSlider/transition.js', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/js/tps-transition-' . $transition . '.js', array( 'jquery' ), THEIA_POST_SLIDER_VERSION );
        wp_enqueue_script( 'theiaPostSlider/transition.js' );
    }

    // Enqueue JavaScript and CSS.
    public static function wp_enqueue_scripts() {
        // Do not load unless necessary.
        if ( ! is_admin() && ! Misc::is_compatible_post() ) {
            return;
        }

        // Theme.
        $theme = Options::get( 'theme_type' ) == 'font' ? 'font-theme.css' : Options::get( 'theme' );
        if ( $theme != 'none' ) {
            wp_register_style( 'theiaPostSlider', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/css/' . $theme, array(), THEIA_POST_SLIDER_VERSION );
            wp_enqueue_style( 'theiaPostSlider' );
        }

        // Font icons.
        if ( is_admin() || Options::get( 'theme_type' ) == 'font' ) {
            wp_register_style( 'theiaPostSlider-font', THEIA_POST_SLIDER_PLUGINS_URL . 'assets/fonts/style.css', array(), THEIA_POST_SLIDER_VERSION );
            wp_enqueue_style( 'theiaPostSlider-font' );
        }

        if ( ! is_admin() ) {
            // history.js
            wp_register_script( 'history.js', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/js/jquery.history.js', array( 'jquery' ), '1.7.1' );
            wp_enqueue_script( 'history.js' );
        }

        // async.js
        wp_register_script( 'async.js', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/js/async.min.js', array(), '14.09.2014' );
        wp_enqueue_script( 'async.js' );

        // Hammer.js
        if ( Options::get( 'enable_touch_gestures', 'tps_advanced' ) ) {
            wp_register_script( 'hammer.js', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/js/hammer.min.js', array(), '2.0.4' );
            wp_enqueue_script( 'hammer.js' );
        }

        // The slider
        wp_register_script( 'theiaPostSlider/theiaPostSlider.js', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/js/tps.js', array( 'jquery' ), THEIA_POST_SLIDER_VERSION );
        wp_enqueue_script( 'theiaPostSlider/theiaPostSlider.js' );

        // Declarative approach js
        wp_register_script( 'theiaPostSlider/main.js', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/js/main.js', array( 'jquery' ), THEIA_POST_SLIDER_VERSION );
        wp_enqueue_script( 'theiaPostSlider/main.js' );

        // The selected transition effect
        self::enqueue_transition( Options::get( 'transition_effect' ) );

        if ( is_rtl() ) {
            wp_register_style( 'theiaPostSlider-rtl', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/css/rtl.css', array(), THEIA_POST_SLIDER_VERSION );
            wp_enqueue_style( 'theiaPostSlider-rtl' );
        }

        // Add inline styles.
        self::add_inline_styles();
    }

    // Add attributes to bypass Cloudflare's Rocket Loader.
    public static function script_loader_tag( $tag, $handle ) {
        if ( ! Options::get( 'disable_rocketscript' ) ) {
            return $tag;
        }

        if ( ! in_array( $handle, array( 'jquery', 'jquery-core', 'jquery-migrate', 'history.js', 'async.js', 'hammer.js', 'theiaPostSlider/theiaPostSlider.js', 'theiaPostSlider/main.js', 'theiaPostSlider/transition.js' ) ) ) {
            return $tag;
        }

        return str_replace( ' src', ' data-cfasync="false" src', $tag );
    }

    // Enqueue JavaScript and CSS for the admin interface.
    public static function admin_enqueue_scripts( $hookSuffix ) {
        if ( strpos( $hookSuffix, 'settings_page_theia-post-slider' ) !== 0 ) {
            return;
        }

        self::wp_enqueue_scripts();

        // Enqueue all transition scripts for live preview.
        foreach ( Options::get_transition_effects() as $key => $value ) {
            self::enqueue_transition( $key );
        }

        // CSS, even if there is no theme, so we can change the path via JS.
        if ( Options::get( 'theme' ) == 'none' ) {
            wp_register_style( 'theiaPostSlider', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/css/' . Options::get( 'theme' ), THEIA_POST_SLIDER_VERSION );
            wp_enqueue_style( 'theiaPostSlider' );
        }

        // Admin CSS
        wp_register_style( 'theiaPostSlider-admin', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/css/admin.css', array(), THEIA_POST_SLIDER_VERSION );
        wp_enqueue_style( 'theiaPostSlider-admin' );

        // Color picker
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        // Slim Select
        wp_enqueue_style( 'slimselect', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/slim-select/slimselect.min.css', array(), THEIA_POST_SLIDER_VERSION );
        wp_enqueue_script( 'slimselect', THEIA_POST_SLIDER_PLUGINS_URL . 'dist/slim-select/slimselect.min.js', array( 'jquery' ), THEIA_POST_SLIDER_VERSION );
    }

    protected static function add_inline_styles() {
        $css = array();

        $css[] = Options::get( 'custom_css' );

        // Additional CSS for vector themes.
        if ( Options::get( 'theme_type' ) == 'font' || is_admin() ) {
            $colors = Colors::get_variations( Options::get( 'theme_font_color' ) );

            $css[] = "
				.theiaPostSlider_nav.fontTheme ._title,
				.theiaPostSlider_nav.fontTheme ._text {
					line-height: " . Options::get( 'theme_font_size' ) . "px;
				}

				.theiaPostSlider_nav.fontTheme ._button,
				.theiaPostSlider_nav.fontTheme ._button svg {
					color: " . Options::get( 'theme_font_color' ) . ";
					fill: " . Options::get( 'theme_font_color' ) . ";
				}

				.theiaPostSlider_nav.fontTheme ._button ._2 span {
					font-size: " . Options::get( 'theme_font_size' ) . "px;
					line-height: " . Options::get( 'theme_font_size' ) . "px;
				}

				.theiaPostSlider_nav.fontTheme ._button ._2 svg {
					width: " . Options::get( 'theme_font_size' ) . "px;
				}

				.theiaPostSlider_nav.fontTheme ._button:hover,
				.theiaPostSlider_nav.fontTheme ._button:focus,
				.theiaPostSlider_nav.fontTheme ._button:hover svg,
				.theiaPostSlider_nav.fontTheme ._button:focus svg {
					color: " . Colors::hsl_to_hex( $colors['hover_color'] ) . ";
					fill: " . Colors::hsl_to_hex( $colors['hover_color'] ) . ";
				}

				.theiaPostSlider_nav.fontTheme ._disabled,
                .theiaPostSlider_nav.fontTheme ._disabled svg {
					color: " . Colors::hsl_to_hex( $colors['disabled_color'] ) . " !important;
					fill: " . Colors::hsl_to_hex( $colors['disabled_color'] ) . " !important;
				}
			";

            $buttonCss = array();

            if ( Options::get( 'theme_padding' ) ) {
                $buttonCss[] = 'padding: ' . Options::get( 'theme_padding' ) . 'px;';
            }

            if ( Options::get( 'theme_border_radius' ) ) {
                $buttonCss[] = 'border-radius: ' . Options::get( 'theme_border_radius' ) . 'px;';
            }

            if ( Options::get( 'theme_background_color' ) ) {
                $buttonCss[] = 'background-color: ' . Options::get( 'theme_background_color' ) . ';';
            }

            if ( count( $buttonCss ) ) {
                $css[] = "
					.theiaPostSlider_nav.fontTheme ._buttons ._button {
						" . implode( "\n", $buttonCss ) . "
					}
				";
            }
        }

        wp_add_inline_style( 'theiaPostSlider', implode( "\n", $css ) );
    }
}
