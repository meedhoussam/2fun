<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider;

class Content {
    public static function staticConstruct() {
        add_action( 'init', __NAMESPACE__ . '\\Content::init' );
        add_action( 'the_post', __NAMESPACE__ . '\\Content::the_post', 999999 );
        add_action( 'the_content', __NAMESPACE__ . '\\Content::the_content', 999999 );
        add_filter( 'wp_title_parts', __NAMESPACE__ . '\\Content::wp_title_parts', 10, 1 );
        add_filter( 'wpseo_title', __NAMESPACE__ . '\\Content::wpseo_title', 10, 1 );

        add_filter( 'tps_the_content_before', __NAMESPACE__ . '\\Content::filter_content_before', 10, 2);
        add_filter( 'tps_the_content_before_current_slide', __NAMESPACE__ . '\\Content::filter_content_before_current_slide', 10, 2 );
        add_filter( 'tps_the_content_after_current_slide', __NAMESPACE__ . '\\Content::filter_content_after_current_slide', 10, 2 );
        add_filter( 'tps_the_content_after', __NAMESPACE__ . '\\Content::filter_content_after', 10, 2 );
    }

    // Set this to true to prevent the_content() from calling itself in an infinite loop.
    public static $theContentIsCalled = false;
    // Post data per post ID
    public static $postData = array();

    public static function init() {
        add_action( 'the_content', __NAMESPACE__ . '\\Content::the_content_early', Options::get( 'the_content_early_priority' ) );
    }

    /*
     * We want to enable sliders only for the main post on a post page. This usually means that is_singular() returns true
     * (i.e. the query is for only one post). But, some themes have single queries used only to display the excerpts.
     * So, here we'll prepare the post for sliders, but these sliders will be activated only if the_content() is also
     * called.
     */
    public static function the_post( $post ) {
        if (
            Misc::$force_disable ||
            ! Misc::is_compatible_post( $post )
        ) {
            return;
        }

        global $page, $pages, $multipage;

        // If a page does not exist, display the last page.
        if ( $page > count( $pages ) ) {
            $page = count( $pages );
        }

        // Get previous and next posts.
        $prevPost = Misc::get_prev_next_post( true );
        $nextPost = Misc::get_prev_next_post( false );

        /*
         * Prepare the sliders if
         * a) This is a single post with multiple pages.
         * - OR -
         * b) Previous/next post navigation is enabled and we do have a previous or a next post.
         */
        if ( ! ( $multipage || $prevPost || $nextPost ) ) {
            return;
        }

        // Save some variables that we'll also use in the_content().
        Content::$postData[ $post->ID ] = array(
            'slideContainerId' => 'tps_slideContainer_' . $post->ID,
            'navIdUpper'       => 'tps_nav_upper_' . $post->ID,
            'navIdLower'       => 'tps_nav_lower_' . $post->ID,
            'prevPostId'       => $prevPost,
            'nextPostId'       => $nextPost,
            'prevPostUrl'      => $prevPost ? get_permalink( $prevPost ) : null,
            'nextPostUrl'      => $nextPost ? get_permalink( $nextPost ) : null
        );

        // Set this to false so that the theme doesn't display pagination buttons. Kind of a hack.
        $multipage = false;
    }

    public static function the_content_early( $content ) {
        if ( ! Misc::$force_begin_and_end_comments && (
                Misc::$force_disable ||
                ! Misc::is_compatible_post()
            )
        ) {
            return $content;
        }

        // Add strings to delimit the content.
        $content = Options::get_beginning_post_content_separator() . "\n" . $content . "\n" . Options::get_ending_post_content_separator();

        // Be sure that shortcodes are in their own paragraph.
        $shortcodes = array(
            Misc::$begin_header_short_code,
            Misc::$end_header_short_code,
            Misc::$begin_title_short_code,
            Misc::$end_title_short_code,
            Misc::$begin_footer_short_code,
            Misc::$end_footer_short_code
        );
        foreach ( $shortcodes as $sc ) {
            $content = str_replace( $sc, "\n\n" . $sc . "\n\n", $content );
        }

        return $content;
    }

    /*
     * Append the JavaScript code only if the_content is called (i.e. the whole post is being displayed, not just the
     * excerpt).
     */
    public static function the_content( $content ) {
        global $post, $page, $pages;

        if (
            Misc::$force_disable ||
            ! Misc::is_compatible_post() ||
            ! isset( $post ) ||
            ! self::havePostData( $post->ID )
        ) {
            // Remove shortcodes.
            $content = str_replace( array(
                Misc::$begin_header_short_code,
                Misc::$end_header_short_code,
                Misc::$begin_title_short_code,
                Misc::$end_title_short_code,
                Misc::$begin_footer_short_code,
                Misc::$end_footer_short_code
            ), '', $content );

            return $content;
        }

        $postData = Content::$postData[ $post->ID ];

        // Do not allow multiple instances, if enabled.
        if ( Options::get( 'do_not_check_for_multiple_instances', 'tps_advanced' ) == false && in_array( $post->ID, Misc::$posts_with_slider ) == true ) {
            return $content;
        }

        // Prevent this function from calling itself.
        if ( self::$theContentIsCalled ) {
            return $content;
        }
        self::$theContentIsCalled = true;

        $currentPage = min( max( $page, 1 ), count( $pages ) );

        // Extract short codes. This needs to be done before splitting the content.
        {
            Misc::$current_post_title = ShortCodes::extract_short_code( $content, Misc::$begin_title_short_code, Misc::$end_title_short_code );

            if ( $page == 1 ) {
                $contentToExtractHeaderFrom = &$content;
            } else {
                $contentToExtractHeaderFrom = &$pages[0];
            }
            $header = ShortCodes::extract_short_code( $contentToExtractHeaderFrom, Misc::$begin_header_short_code, Misc::$end_header_short_code );
            $header = do_shortcode( $header );

            if ( $page == count( $pages ) ) {
                $contentToExtractFooterFrom = &$content;
            } else {
                $contentToExtractFooterFrom = &$pages[ count( $pages ) - 1 ];
            }
            $footer = ShortCodes::extract_short_code( $contentToExtractFooterFrom, Misc::$begin_footer_short_code, Misc::$end_footer_short_code );
            $footer = do_shortcode( $footer );
        }

        // Split the content.
        $split_content = Misc::split_content( $content );
        $content       = $split_content['content'];

        // Fix broken HTML.
        if ( Options::get( 'try_to_fix_broken_html' ) ) {
            if ( function_exists( 'tidy_repair_string' ) ) {
                $content = tidy_repair_string( $content, null, 'utf8' );
            } else {
                // Include HTMLPurifier, unless it is autoloaded.
                if ( ! class_exists( 'HTMLPurifier' ) ) {
                    $file = THEIA_POST_SLIDER_DIR . '/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';

                    if ( is_file( $file ) ) {
                        include_once $file;
                    }
                }

                $config = \HTMLPurifier_Config::createDefault();
                $config->set( 'Core.Encoding', 'UTF-8' );
                $config->set( 'HTML.TidyLevel', 'none' );
                $config->set( 'HTML.Trusted', true );
                $config->set( 'Cache.DefinitionImpl', null );
                $config->set( 'Core.LexerImpl', 'DirectLex' );
                $config->set( 'HTML.TargetNoreferrer', false );
                $purifier = new \HTMLPurifier( $config );

                $content = $purifier->purify( $content );
            }
        }

        // Start adding HTML.
        $html = '';

        // Apply 'before' fiters.
        $html = apply_filters( 'tps_the_content_before', $html, $content,  'content_above_slider');

        // Add slider HTML.
        {
            $html .= $split_content['beforeContent'];

            // Header
            if ( $header ) {
                $html .= '<div class="theiaPostSlider_header _header">' . $header . '</div>';
            }

            $html = apply_filters( 'tps_the_content_before_header', $html, $content );

            // Top slider
            if ( in_array( PostOptions::get( $post->ID, 'nav_vertical_position', 'tps_nav' ), array(
                'top_and_bottom',
                'top'
            ) ) ) {
                $html .= NavigationBar::get_navigation_bar( array(
                    'currentSlide' => $page,
                    'totalSlides'  => count( $pages ),
                    'prevPostUrl'  => $postData['prevPostUrl'],
                    'nextPostUrl'  => $postData['nextPostUrl'],
                    'id'           => $postData['navIdUpper'],
                    'class'        => '_upper',
                    'title'        => Misc::$current_post_title
                ) );
            }

            $html = apply_filters( 'tps_the_content_before_current_slide', $html, $content, 'content_under_top_navigation' );

            // Current slide.
            $html .= '<div id="' . $postData['slideContainerId'] . '" class="theiaPostSlider_slides"><div class="theiaPostSlider_preloadedSlide">';
            $html .= "\n\n" . $content . "\n\n";
            $html .= '</div></div>';

            $html = apply_filters( 'tps_the_content_after_current_slide', $html, $content, 'content_above_bottom_navigation' );

            // Bottom slider
            if ( in_array( PostOptions::get( $post->ID, 'nav_vertical_position', 'tps_nav' ), array(
                'top_and_bottom',
                'bottom'
            ) ) ) {
                $html .= NavigationBar::get_navigation_bar( array(
                    'currentSlide' => $page,
                    'totalSlides'  => count( $pages ),
                    'prevPostUrl'  => $postData['prevPostUrl'],
                    'nextPostUrl'  => $postData['nextPostUrl'],
                    'id'           => $postData['navIdLower'],
                    'class'        => '_lower',
                    'title'        => Misc::$current_post_title
                ) );
            }

            // Footer
            $html .= '<div class="theiaPostSlider_footer _footer">' . $footer . '</div>';

            $html .= $split_content['afterContent'];
        }

        $slides = array();
        // Preload slides.
        {
            $preloadBegin = $currentPage + 1;
            $preloadEnd   = $currentPage;

            if ( PostOptions::get( $post->ID, 'slide_loading_mechanism', 'tps_advanced' ) == 'all' ) {
                $preloadBegin = 1;
                $preloadEnd   = count( $pages );
            }

            if ( Options::get( 'ad_refreshing_mechanism', 'tps_advanced' ) == 'page' ) {
                // Avoid AJAX request if the page refreshes every single slide.
                if ( Options::get( 'refresh_ads_every_n_slides', 'tps_advanced' ) == 1 ) {
                    $preloadBegin = $currentPage + 1;
                    $preloadEnd   = $currentPage;
                } else {
                    $preloadBegin = max( $currentPage - Options::get( 'refresh_ads_every_n_slides', 'tps_advanced' ), $preloadBegin );
                    $preloadEnd   = min( $currentPage + Options::get( 'refresh_ads_every_n_slides', 'tps_advanced' ), $preloadEnd );
                }
            }

            // Validate values.
            $preloadBegin = max( 1, $preloadBegin );
            $preloadEnd   = min( count( $pages ), $preloadEnd );

            for ( $i = $preloadBegin; $i <= $preloadEnd; $i ++ ) {
                // If we don't need to pass the source, then don't get the current slide since it will be echoed as actual HTML.
                if ( ! Options::get( 'do_not_cache_rendered_html', 'tps_advanced' ) && $i == $currentPage ) {
                    continue;
                }

                if ( Options::get( 'ad_refreshing_mechanism', 'tps_advanced' ) == 'page' ) {
                    // Only get permalinks for the edge slides.
                    if (
                        $i == $currentPage - Options::get( 'refresh_ads_every_n_slides', 'tps_advanced' ) ||
                        $i == $currentPage + Options::get( 'refresh_ads_every_n_slides', 'tps_advanced' )
                    ) {
                        $slides[ $i - 1 ] = array(
                            'permalink' => Misc::get_post_page_url( $i )
                        );

                        continue;
                    }
                }

                // Get the entire slide.
                $slides[ $i - 1 ] = Misc::get_sub_page( $i, $currentPage );
            }
        }

        // Append the slider initialization script to the "theiaPostSlider.js" script.
        if ( PostOptions::get( $post->ID, 'slide_loading_mechanism', 'tps_advanced' ) != 'refresh' ) {
            $sliderOptions = array(
                'slideContainer'             => '#' . $postData['slideContainerId'],
                'nav'                        => array( '.theiaPostSlider_nav' ),
                'navText'                    => Options::get( 'navigation_text' ),
                'helperText'                 => Options::get( 'helper_text' ),
                'defaultSlide'               => $currentPage - 1,
                'transitionEffect'           => Options::get( 'transition_effect' ),
                'transitionSpeed'            => (int) Options::get( 'transition_speed' ),
                'keyboardShortcuts'          => ( Misc::is_compatible_post() && ! Options::get( 'disable_keyboard_shortcuts', 'tps_nav' ) ) ? true : false,
                'scrollAfterRefresh'         => Options::get( 'scroll_after_refresh' ),
                'numberOfSlides'             => count( $pages ),
                'slides'                     => $slides,
                'useSlideSources'            => Options::get( 'do_not_cache_rendered_html', 'tps_advanced' ),
                'themeType'                  => Options::get( 'theme_type' ),
                'prevText'                   => Options::get( 'prev_text' ),
                'nextText'                   => Options::get( 'next_text' ),
                'buttonWidth'                => Options::get( 'button_width' ),
                'buttonWidth_post'           => Options::get( 'button_width_post' ),
                'postUrl'                    => get_permalink( $post->ID ),
                'postId'                     => $post->ID,
                'refreshAds'                 => PostOptions::getAdBehaviorBasedOnType( $post->ID,'refresh_ads', 'tps_advanced' ),
                'refreshAdsEveryNSlides'     => PostOptions::getAdBehaviorBasedOnType( $post->ID, 'refresh_ads_every_n_slides', 'tps_advanced' ),
                'adRefreshingMechanism'      => PostOptions::getAdBehaviorBasedOnType( $post->ID, 'ad_refreshing_mechanism', 'tps_advanced' ),
                'ajaxUrl'                    => admin_url( 'admin-ajax.php' ),
                'loopSlides'                 => Options::get( 'button_behaviour', 'tps_nav' ) === 'loop',
                'scrollTopOffset'            => Options::get( 'scroll_top_offset', 'tps_nav' ),
                'hideNavigationOnFirstSlide' => PostOptions::get( $post->ID, 'nav_hide_on_first_slide' ),
                'isRtl'                      => is_rtl(),
                'excludedWords'              => explode( "\n", str_replace( "\r", '', Options::get( 'excludedWords' ) ) )
            );

            if ( Options::get( 'theme_type' ) == 'font' ) {
                $sliderOptions['prevFontIcon'] = Options::get_font_icon( is_rtl() ? 'right' : 'left' );
                $sliderOptions['nextFontIcon'] = Options::get_font_icon( is_rtl() ? 'left' : 'right' );
            }

            if ( Options::get( 'button_behaviour' ) === 'post' ) {
                $sliderOptions = array_merge( $sliderOptions, array(
                    'prevPost'      => $postData['prevPostUrl'],
                    'nextPost'      => $postData['nextPostUrl'],
                    'prevText_post' => Options::get( 'prev_text_post' ),
                    'nextText_post' => Options::get( 'next_text_post' )
                ) );
            }

            // Trigger ready/load events when navigating to another slide.
            $onChangeSlide = '';
            if ( Options::get( 'window_load_js' ) ) {
                $onChangeSlide .= "$(window).load();";
            }
            if ( Options::get( 'document_ready_js' ) ) {
                $onChangeSlide .= "$(document).ready();";
            }
            if ( Options::get( 'domcontentloaded_js' ) ) {
                $onChangeSlide .= "var DOMContentLoaded_event = document.createEvent(\"Event\"); DOMContentLoaded_event.initEvent(\"DOMContentLoaded\", true, true); window.document.dispatchEvent(DOMContentLoaded_event);";
            }
            if ( Options::get( 'document_resize_js' ) ) {
                $onChangeSlide .= "$(document).resize();";
            }
            if ( Options::get( 'document_scroll_js' ) ) {
                $onChangeSlide .= "$(document).scroll();";
            }

            $html .= "<div data-theiaPostSlider-sliderOptions='" . htmlspecialchars( json_encode( $sliderOptions ), ENT_QUOTES ) . "'
					 data-theiaPostSlider-onChangeSlide='" . htmlspecialchars( json_encode( $onChangeSlide ), ENT_QUOTES ) . "'></div>";

            // Mark the post as having a slider.
            Misc::$posts_with_slider[] = $post->ID;
        }

        // Apply 'after' fiters.
        $html = apply_filters( 'tps_the_content_after', $html, $content, 'content_under_slider');

        self::$theContentIsCalled = false;

        return $html;
    }

    public static function wp_title_parts( $title_array ) {
        if ( ! Options::get( 'override_subtitles' ) ) {
            return $title_array;
        }

        global $post, $page, $pages;

        // Only override subpages, not the first page.
        if ( $page == 1 ) {
            return $title_array;
        }

        // Get [tps_title] content of the curent page, if available.
        setup_postdata( $post );
        $content = $pages[ $page - 1 ];
        $title   = ShortCodes::extract_short_code( $content, Misc::$begin_title_short_code, Misc::$end_title_short_code, false );
        if ( $title ) {
            $title_array[0] = strip_tags( $title );

            return $title_array;
        }

        // Return unchanged title.
        return $title_array;
    }

    public static function wpseo_title( $wpseo_title ) {
        if ( ! Options::get( 'override_subtitles' ) ) {
            return $wpseo_title;
        }

        global $post, $page, $pages;

        // Only override subpages, not the first page.
        if ( $page == 1 ) {
            return $wpseo_title;
        }

        // Get [tps_title] content of the curent page, if available.
        setup_postdata( $post );
        $content = $pages[ $page - 1 ];
        $title   = ShortCodes::extract_short_code( $content, Misc::$begin_title_short_code, Misc::$end_title_short_code, false );
        if ( $title ) {
            $wpseo_title = str_replace( $post->post_title, $title, $wpseo_title );

            return $wpseo_title;
        }

        // Return unchanged title.
        return $wpseo_title;
    }

    public static function havePostData( $postId ) {
        return array_key_exists( $postId, Content::$postData );
    }

    // Show post-specific content or default setting.
    public static function filter_content_above_under_slider_and_navigation( $html, $content, $name ) {
        $options = PostOptions::get_post_options(get_the_ID());
        if ( empty( $options[$name] ) ) {
            $optionContent = Options::get( $name, 'tps_nav' );
        } else {
            $optionContent = $options[$name];
        }
        $html .= do_shortcode($optionContent);

        return $html;
    }

    public static function filter_content_before($html, $content) {
        return self::filter_content_above_under_slider_and_navigation($html, $content, 'content_above_slider');
    }

    public static function filter_content_before_current_slide($html, $content){
        return self::filter_content_above_under_slider_and_navigation($html, $content, 'content_under_top_navigation');
    }

    public static function filter_content_after_current_slide($html, $content){
        return self::filter_content_above_under_slider_and_navigation($html, $content, 'content_above_bottom_navigation');
    }

    public static function filter_content_after($html, $content){
        return self::filter_content_above_under_slider_and_navigation($html, $content, 'content_under_slider');
    }
}
