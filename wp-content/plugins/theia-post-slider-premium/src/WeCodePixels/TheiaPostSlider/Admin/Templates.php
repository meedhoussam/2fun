<?php

/*
 * Copyright 2012-2020, Theia Post Slider, WeCodePixels, https://wecodepixels.com
 */

namespace WeCodePixels\TheiaPostSlider\Admin;

use WeCodePixels\TheiaPostSlider\Options;
use WeCodePixels\TheiaPostSlider\PostOptions;

class Templates {
	public static function getVerticalPositionHtml( $currentOptions, $postPage = false ) {
		$prefix   = $postPage ? 'tps_options' : 'tps_nav';
		$onchange = $postPage ? '' : 'updateSlider()';
		?>
		<tr valign="top">
			<th scope="row">
				<label for="tps_nav_vertical_position"><?php _e( "Vertical position:", 'theia-post-slider' ); ?></label>
			</th>
			<td>
				<select id="tps_nav_vertical_position"
				        name="<?php echo $prefix; ?>[nav_vertical_position]"
				        onchange="<?php echo $onchange; ?>">
					<?php
					$options = array();
					if ( $postPage ) {
						$options['global'] = 'Use global setting';
					}
					$options = array_merge( $options, Options::get_button_vertical_positions() );
					foreach ( $options as $key => $value ) {
						$output = '<option value="' . $key . '"' . ( $key == $currentOptions['nav_vertical_position'] ? ' selected' : '' ) . '>' . $value . '</option>' . "\n";
						echo $output;
					}
					?>
				</select>
			</td>
		</tr>
		<?php
	}

	public static function getHideNavigationBarOnFirstSlideHtml( $currentOptions, $postPage = false ) {
		$prefix = $postPage ? 'tps_options' : 'tps_nav';
		?>
		<tr valign="top">
			<th scope="row">
				<label for="tps_nav_hide_on_first_slide"><?php _e( "Hide on first slide:", 'theia-post-slider' ); ?></label>
			</th>
			<td>
				<select id="tps_nav_hide_on_first_slide"
				        name="<?php echo $prefix; ?>[nav_hide_on_first_slide]">
					<?php
					$options = Options::get_generic_boolean();
					foreach ( $options as $key => $value ) {
						$output = '<option value="' . $key . '"' . ( $key == $currentOptions['nav_hide_on_first_slide'] ? ' selected' : '' ) . '>' . $value . '</option>' . "\n";
						echo $output;
					}
					?>
				</select>
			</td>
		</tr>
		<?php
	}

    public static function getContentAboveUnderSliderAndNavigation( $options, $postPage = false ) {
    	$prefix = $postPage ? 'tps_options' : 'tps_nav';
        ?>
		<table class="form-table">
			<h3><?php _e( "Additional Content", 'theia-post-slider' ); ?></h3>

			<tr valign="top">
				<th scope="row">
					<label for="tps_content_above_slider"><?php _e( "Content above slider:", 'theia-post-slider' ); ?></label>
				</th>
				<td>
                    <textarea class="large-text"
                              id="tps_content_above_slider"
                              name="<?php echo $prefix; ?>[content_above_slider]"
                    ><?= htmlspecialchars($options['content_above_slider']) ?></textarea>

					<p class="description">
						Display any text, HTML, or shortcodes above the slider.
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="tps_content_under_top_navigation"><?php _e( "Content under top navigation:", 'theia-post-slider' ); ?></label>
				</th>
				<td>
                    <textarea class="large-text"
                              id="tps_content_under_top_navigation"
                              name="<?php echo $prefix; ?>[content_under_top_navigation]"
                    ><?= htmlspecialchars($options['content_under_top_navigation']) ?></textarea>

					<p class="description">
						Display any text, HTML, or shortcodes under the top navigation arrows.
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="tps_content_above_bottom_navigation"><?php _e( "Content above bottom navigation:", 'theia-post-slider' ); ?></label>
				</th>
				<td>
                    <textarea class="large-text"
                              id="tps_content_above_bottom_navigation"
                              name="<?php echo $prefix; ?>[content_above_bottom_navigation]"
                    ><?= htmlspecialchars($options['content_above_bottom_navigation']) ?></textarea>

					<p class="description">
						Display any text, HTML, or shortcodes above the bottom navigation arrows.
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="tps_content_under_slider"><?php _e( "Content under slider:", 'theia-post-slider' ); ?></label>
				</th>
				<td>
                    <textarea class="large-text"
                              id="tps_content_under_slider"
                              name="<?php echo $prefix; ?>[content_under_slider]"
                    ><?= htmlspecialchars($options['content_under_slider']) ?></textarea>

					<p class="description">
						Display any text, HTML, or shortcodes below the slider.
					</p>
				</td>
			</tr>
		</table>
        <?php
    }

	public static function get_slide_loading_mechanism_html( $currentOptions, $postPage = false ) {
		$prefix = $postPage ? 'tps_options' : 'tps_advanced';
		?>
		<tr valign="top">
			<th scope="row">
				<label for="tps_nav_hide_on_first_slide"><?php _e( "Slide loading mechanism:", 'theia-post-slider' ); ?></label>
			</th>
			<td>
				<?php if ( $postPage ): ?>
					<label>
						<input type="radio"
						       name="<?php echo $prefix; ?>[slide_loading_mechanism]"
						       value="global" <?php echo $currentOptions['slide_loading_mechanism'] == 'global' ? 'checked' : ''; ?>>
						Use global setting.
						<p></p>
					</label>
					<br>
				<?php endif; ?>
				<label>
					<input type="radio"
					       name="<?php echo $prefix; ?>[slide_loading_mechanism]"
					       value="ajax" <?php echo $currentOptions['slide_loading_mechanism'] == 'ajax' ? 'checked' : ''; ?>>
					Load slides efficiently using AJAX.
					<p class="description">
						Recommended. Most efficient option and offers best user experience. Uses preloading and
						caching methods.
					</p>
				</label>
				<br>
				<label>
					<input type="radio"
					       name="<?php echo $prefix; ?>[slide_loading_mechanism]"
					       value="all" <?php echo $currentOptions['slide_loading_mechanism'] == 'all' ? 'checked' : ''; ?>>
					Load all slides at once.
					<p class="description">
						Legacy mode. Use this option if you have compatibility issues.
					</p>
				</label>

				<p></p>
			</td>
		</tr>
		<?php
	}

	public static function getAdBehaviorSelect( $currentOptions) {
        ?>
		<tr valign="top">
			<th scope="row">
				<label for="tps_select_global_vs_post"><?php self::outputAdBehaviorTitle(); ?></label>
			</th>
			<td>
				<select id="tps_select_global_vs_post" name="tps_options[select_global_vs_post]">
                    <?php
                    foreach ( PostOptions::get_global_vs_post_ad_select_options() as $key => $value ) {
                        $output = '<option value="' . $key . '"' . ( $key == $currentOptions['select_global_vs_post'] ? ' selected' : '' ) . '>' . $value . '</option>' . "\n";
                        echo $output;
                    }
                    ?>
				</select>
			</td>
		</tr>
        <?php
    }

	public static function getOptionsAdBehaviorHtml( $currentOptions, $postPage = false) {
		$prefix = $postPage ? 'tps_options' : 'tps_advanced';
        ?>
		<tr valign="top" class="conditional-ad-options">
			<th scope="row">
				<label for="tps_refresh_ads"><?php _e( "Ad refreshing:", 'theia-post-slider' ); ?></label>
			</th>
			<td>
				<label>
					<input type="hidden" name="<?php echo $prefix; ?>[refresh_ads]" value="false">
					<input type="checkbox"
					       id="tps_refresh_ads"
					       name="<?php echo $prefix; ?>[refresh_ads]"
					       value="true"
                        <?php echo $currentOptions['refresh_ads'] ? 'checked' : ''; ?>
                        <?php if ( !$postPage ) {
                            echo 'onchange = "updateForm()"';
                        } else {
                            echo $currentOptions['select_global_vs_post'] === 'post' ? '' : 'disabled';
                        }
                        ?>
					/>
					Refresh ads when navigating to another slide.
				</label>
			</td>
		</tr>

		<tr valign="top" class="conditional-ad-options">
			<th scope="row">
				<label for="tps_refresh_ads_every_n_slides"><?php _e( "Refresh ads every N slides:", 'theia-post-slider' ); ?></label>
			</th>
			<td>
				<label>
					<input type="number"
					       id="tps_refresh_ads_every_n_slides"
					       name="<?php echo $prefix; ?>[refresh_ads_every_n_slides]"
                           <?php if ( $postPage ) {
                              echo $currentOptions['select_global_vs_post'] === 'post' ? '' : 'disabled';
                           } ?>
						   value="<?php echo htmlentities( $currentOptions['refresh_ads_every_n_slides'] ); ?>"
					/>
					<p class="description">
						Use "1" to refresh ads on every slide.
					</p>
				</label>
			</td>
		</tr>

		<tr valign="top" class="conditional-ad-options">
			<th scope="row">
				<label><?php _e( "Ad refreshing mechanism:", 'theia-post-slider' ); ?></label>
			</th>
			<td>
				<label>
					<input type="radio"
					       id="tps_ad_refreshing_mechanism_javascript"
					       name="<?php echo $prefix; ?>[ad_refreshing_mechanism]"
					       value="javascript"
                           <?php if ( $postPage ) {
                              echo $currentOptions['select_global_vs_post'] === 'post' ? '' : 'disabled';
                           } ?>
                           <?php echo $currentOptions['ad_refreshing_mechanism'] === 'javascript' ? 'checked' : ''; ?>
					/>
					Refresh ads using JavaScript.
					<p class="description">
						Works with Google DoubleClick and partners. Requires that you use
						<strong>
							<a href="https://support.google.com/dfp_premium/answer/177207">
								GPT (Google Publishing Tags)
							</a>
						</strong>
						and
						<strong>
							<a href="https://support.google.com/dfp_premium/answer/183282">
								asynchronous rendering
							</a>
						</strong>.
						DART tags are not supported. Google AdSense is not supported because their Terms of
						Service forbid this kind of behavior.
					</p>
				</label>
				<br>
				<label>
					<input type="radio"
					       id="tps_ad_refreshing_mechanism_page"
					       name="<?php echo $prefix; ?>[ad_refreshing_mechanism]"
					       value="page"
	                       <?php if ( $postPage ) {
	                           echo $currentOptions['select_global_vs_post'] === 'post' ? '' : 'disabled';
	                       } ?>
                           <?php echo $currentOptions['ad_refreshing_mechanism'] === 'page' ? 'checked' : ''; ?>
					/>
					Refresh ads by refreshing the entire page.
					<p class="description">
						Works with any other ads, including Google AdSense. Transition effects will not be used when refreshing the page.
					</p>
				</label>
			</td>
		</tr>
        <?php
    }

    public static function outputAdBehaviorTitle() {
		?><h3><?php _e( "Ad behavior", 'theia-post-slider' ); ?></h3><?php
    }
}
