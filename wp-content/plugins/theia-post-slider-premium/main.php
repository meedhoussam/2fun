<?php
/*
Plugin Name: Theia Post Slider
Plugin URI: https://wecodepixels.com/theia-post-slider-for-wordpress/?utm_source=theia-post-slider-for-wordpress
Description: Display multi-page posts using a slider, as a slideshow.
Author: WeCodePixels
Author URI: https://wecodepixels.com/?utm_source=theia-post-slider-for-wordpress
Version: 2.3.2
Copyright: WeCodePixels
*/

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

define( 'THEIA_POST_SLIDER_VERSION', '2.3.2' );
define( 'THEIA_POST_SLIDER_DIR', __DIR__ );
define( 'THEIA_POST_SLIDER_MAIN', __FILE__ );

// Autoloader
require __DIR__ . '/vendor/wecodepixels/wordpress-plugin/Autoloader.php';
$autoLoader = new \WeCodePixels\TheiaPostSliderFramework\Autoloader( __DIR__, 'WeCodePixels\\TheiaPostSlider' );

\WeCodePixels\TheiaPostSlider\Freemius::staticConstruct();
\WeCodePixels\TheiaPostSlider\Ajax::staticConstruct();
\WeCodePixels\TheiaPostSlider\Content::staticConstruct();
\WeCodePixels\TheiaPostSlider\Enqueues::staticConstruct();
\WeCodePixels\TheiaPostSlider\Misc::staticConstruct();
\WeCodePixels\TheiaPostSlider\Options::staticConstruct();
\WeCodePixels\TheiaPostSlider\PostOptions::staticConstruct();
\WeCodePixels\TheiaPostSlider\ShortCodes::staticConstruct();
\WeCodePixels\TheiaPostSlider\Admin\Admin::staticConstruct();


