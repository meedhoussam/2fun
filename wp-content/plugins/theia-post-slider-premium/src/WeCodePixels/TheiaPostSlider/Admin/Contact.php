<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider\Admin;

use WeCodePixels\TheiaPostSliderFramework\Freemius;

class Contact {
    public function echoPage() {
        /** @var $theia_post_slider_fs Freemius */
        global $theia_post_slider_fs;

        $theia_post_slider_fs->_contact_page_render();
    }
}
