<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider\Admin;

use WeCodePixels\TheiaPostSliderFramework\Freemius;

class Account {
    public function echoPage() {
        /** @var $theia_post_slider_fs Freemius */
        global $theia_post_slider_fs;
        $theia_post_slider_fs->add_filter( 'hide_account_tabs', function () {
            return true;
        } );

        if ( $_GET['page'] !== 'theia-post-slider-account' ) {
            $theia_post_slider_fs->_account_page_load();
        }

        $theia_post_slider_fs->_account_page_render();
    }
}
