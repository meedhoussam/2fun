<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider\Admin;

class Dashboard {
    public function echoPage() {
        global $theia_post_slider_fs;

        ?>
        <form method="post" action="options.php">
            <?php settings_fields('tps_options_dashboard'); ?>

            <p>
                You are using
                <a href="https://wecodepixels.com/theia-post-slider-for-wordpress/?utm_source=theia-post-slider-for-wordpress"
                   target="_blank"><b>Theia Post Slider</b></a>
                version <b class="theiaPostSlider_adminVersion"><?php echo THEIA_POST_SLIDER_VERSION; ?></b>, developed
                by
                <a href="https://wecodepixels.com/?utm_source=theia-post-slider-for-wordpress"
                   target="_blank"><b>WeCodePixels</b></a>.
                <br>
            </p>
            <br>

            <h3 class="support-title"><?php _e( "Support", 'theia-post-slider' ); ?></h3>

            <p>
                1. If you have any problems or questions, you should first check
                <a href="https://wecodepixels.com/theia-post-slider-for-wordpress/docs/?utm_source=theia-post-slider-for-wordpress"
                   target="_blank">The Documentation</a>.
            </p>
            <p>
                2. If the plugin is misbehaving, try to

                <a href="#" onclick="if (confirm('Are you sure you want to reset The Global Settings to their default values?')) { jQuery('#reset_global_settings_to_default').click(); }"
                   class="button">
                    Reset Global Settings to Default
                </a>
                <input name="tps_dashboard[reset_global_settings_to_default]"
                       id="reset_global_settings_to_default"
                       type="submit"
                       class=""
                       value="true"
                       style="display: none">
                or

                <a href="#" onclick="if (confirm('Are you sure you want to reset All Post Settings to their default values?')) { jQuery('#reset_all_post_settings_to_default').click(); } "
                   class="button">
                    Reset All Post Settings to Default
                </a>.
                <input name="tps_dashboard[reset_all_post_settings_to_default]"
                       id="reset_all_post_settings_to_default"
                       type="submit"
                       class=""
                       value="<?php echo wp_generate_password( 16, false ) ?>"
                       style="display: none">
            </p>
            <p>
                3. Deactivate all plugins. If the issue is solved, then re-activate them one-by-one to pinpoint the exact cause.
            </p>
            <p>
                4. If your issue persists, please
                <?php
                if ( $theia_post_slider_fs->is_plan( 'theme_bundle', true ) ) {
                    ?>contact the theme author.<?php
                } else {
                    ?>
                    <a href="?page=theia-post-slider&tab=contact">Contact Us</a>.
                    <?php
                }
                ?>
            </p>
        </form>

        <br>

        <iframe class="theiaPostSlider_news" src="https://wecodepixels.com/theia-post-slider-for-wordpress-news/" scrolling="no"></iframe>
        <script src="<?php echo plugins_url( '/dist/js/iframeResizer.min.js', THEIA_POST_SLIDER_MAIN ) ?>"></script>
        <script>
            iFrameResize({log: false}, '.theiaPostSlider_news');
        </script>

        <br>
        <br>
        <br>

        <h3>Credits</h3>

        <a href="#" onclick="jQuery(this).hide(); jQuery('._credits-section').slideToggle(); return false">See the full list of credits</a>

        <div class="_credits-section" style="display: none">
            Many thanks go out to the following:

            <ul>
                <li>
                    <a href="http://www.doublejdesign.co.uk/products-page/icons/super-mono-icons/">Super Mono Icons</a>
                    by
                    <a href="http://www.doublejdesign.co.uk/">Double-J Design</a>
                </li>
                <li>
                    <a href="http://p.yusukekamiyamane.com/">Fugue Icons</a>
                    by
                    <a href="http://yusukekamiyamane.com/">Yusuke Kamiyamane</a>
                </li>
                <li>
                    <a href="http://www.brightmix.com/blog/brightmix-icon-set-free-for-all/">Brightmix icon set</a>
                    by
                    <a href="http://www.brightmix.com">Brightmix</a>
                </li>
                <li>
                    <a href="http://freebiesbooth.com/hand-drawn-web-icons">Hand Drawn Web icons</a>
                    by
                    <a href="http://highonpixels.com/">Pawel Kadysz</a>
                </li>
                <li>
                    <a href="http://icondock.com/free/20-free-marker-style-icons">20 Free Marker-Style Icons</a>
                    by
                    <a href="http://icondock.com">IconDock</a>
                </li>
                <li>
                    <a href="http://taytel.deviantart.com/art/ORB-Icons-87934875">ORB Icons</a>
                    by
                    <a href="http://taytel.deviantart.com">~taytel</a>
                </li>
                <li>
                    <a href="http://www.visualpharm.com/must_have_icon_set/">Must Have Icon Set</a>
                    by
                    <a href="http://www.visualpharm.com">VisualPharm</a>
                </li>
                <li>
                    Arrow designed by <a href="http://www.thenounproject.com/sapi">Stefan Parnarov</a>
                    from the <a href="http://www.thenounproject.com">Noun Project</a>
                </li>
                <li>
                    Arrow designed by <a href="http://www.thenounproject.com/shailendra007">Shailendra Chouhan</a>
                    from the <a href="http://www.thenounproject.com">Noun Project</a>
                </li>
                <li>
                    Arrow designed by <a href="http://www.thenounproject.com/rajputrajesh448">Rajesh Rajput</a>
                    from the <a href="http://www.thenounproject.com">Noun Project</a>
                </li>
                <li>
                    Arrow designed by <a href="http://www.thenounproject.com/chrisburton">Chris Burton</a>
                    from the <a href="http://www.thenounproject.com">Noun Project</a>
                </li>
                <li>
                    Arrow designed by <a href="http://www.thenounproject.com/MisterPixel">Mister Pixel</a>
                    from the <a href="http://www.thenounproject.com">Noun Project</a>
                </li>
                <li>
                    Arrow designed by <a href="http://www.thenounproject.com/winthropite">Mike Jewett</a>
                    from the <a href="http://www.thenounproject.com">Noun Project</a>
                </li>
                <li>
                    Left and Right designed by <a href="http://www.thenounproject.com/cengizsari">Cengiz SARI</a>
                    from the <a href="http://www.thenounproject.com">Noun Project</a>
                </li>
                <li>
                    Left and Right designed by <a href="http://www.thenounproject.com/desbenoit">Desbenoit</a>
                    from the <a href="http://www.thenounproject.com">Noun Project</a>
                </li>
                <li>
                    Various other icons purchased from the <a href="http://www.thenounproject.com">Noun Project</a>
                </li>
            </ul>
        </div>
        <?php
    }
}
