<?php


/* Add metaboxes to category */

if (!function_exists('vce_category_add_meta_fields')) :
    function vce_category_add_meta_fields()
    {
        $vce_meta = vce_get_category_meta();
        $sidebars_lay = vce_get_sidebar_layouts(true);
        $sidebars = vce_get_sidebars_list(true);
        $post_layouts = vce_get_main_layouts(true, false);
        $starter_layouts = vce_get_main_layouts(true, true);
        $fa_layouts = vce_get_featured_area_layouts(true, true);
        ?>
        <div class="form-field">
            <label><?php _e('Featured area layout', THEME_SLUG); ?></label>
            <ul class="vce-img-select-wrap next-hide">
                <?php foreach ($fa_layouts as $id => $layout): ?>
                    <li>
                        <?php $selected_class = vce_compare($vce_meta['fa_layout'], $id) ? ' selected' : ''; ?>
                        <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                             class="vce-img-select<?php echo $selected_class; ?>">
                        <input type="radio" class="vce-hidden" name="vce[fa_layout]"
                               value="<?php echo $id; ?>" <?php checked($id, $vce_meta['fa_layout']); ?>/> </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="description"><?php _e('Choose featured area layout', THEME_SLUG); ?></p>
        </div>

        <?php $style = $vce_meta['fa_layout'] == 'inherit' || !$vce_meta['fa_layout'] ? 'style="display:none"' : ''; ?>
        <div class="form-field" <?php echo $style; ?>>
            <label><?php _e('Featured posts limit', THEME_SLUG); ?></label>
            <input type="text" name="vce[fa_limit]" value="<?php echo $vce_meta['fa_limit']; ?>"
                   style="width: 30px;"/> <?php _e('post(s)', THEME_SLUG); ?>
        </div>

        <div class="form-field">
            <label><?php _e('Posts main layout', THEME_SLUG); ?></label>
            <ul class="vce-img-select-wrap next-hide">
                <?php foreach ($post_layouts as $id => $layout): ?>
                    <li>
                        <?php $selected_class = vce_compare($vce_meta['layout'], $id) ? ' selected' : ''; ?>
                        <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                             class="vce-img-select<?php echo $selected_class; ?>">
                        <input type="radio" class="vce-hidden" name="vce[layout]"
                               value="<?php echo $id; ?>" <?php checked($id, $vce_meta['layout']); ?>/> </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="description"><?php _e('Choose posts layout for this category', THEME_SLUG); ?></p>
        </div>

        <?php $style = $vce_meta['layout'] == 'inherit' ? 'style="display:none"' : ''; ?>
        <div class="form-field" <?php echo $style; ?>>
            <label><?php _e('Number of posts per page', THEME_SLUG); ?></label>
            <input type="text" name="vce[ppp]" value="<?php echo $vce_meta['ppp']; ?>"
                   style="width: 30px;"/> <?php _e('post(s)', THEME_SLUG); ?><br/>
            <small class="description"><?php _e('Note: leave empty if you want to inherit from global category option', THEME_SLUG); ?></small>
        </div>

        <div class="form-field">
            <label><?php _e('Starter posts', THEME_SLUG); ?></label>
            <ul class="vce-img-select-wrap next-hide">
                <?php foreach ($starter_layouts as $id => $layout): ?>
                    <li>
                        <?php $selected_class = vce_compare($vce_meta['top_layout'], $id) ? ' selected' : ''; ?>
                        <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                             class="vce-img-select<?php echo $selected_class; ?>">
                        <input type="radio" class="vce-hidden" name="vce[top_layout]"
                               value="<?php echo $id; ?>" <?php checked($id, $vce_meta['top_layout']); ?>/> </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="description"><?php _e('Check if you want to use starter posts', THEME_SLUG); ?></p>
        </div>

        <?php $style = $vce_meta['top_layout'] == 'inherit' || !$vce_meta['top_layout'] ? 'style="display:none"' : ''; ?>
        <div class="form-field" <?php echo $style; ?>>
            <label><?php _e('Starter posts limit', THEME_SLUG); ?></label>
            <input type="text" name="vce[top_limit]" value="<?php echo $vce_meta['top_limit']; ?>"
                   style="width: 30px;"/> <?php _e('post(s)', THEME_SLUG); ?>
        </div>

        <div class="form-field">
            <label><?php _e('Sidebar layout', THEME_SLUG); ?></label>
            <ul class="vce-img-select-wrap">
                <?php foreach ($sidebars_lay as $id => $layout): ?>
                    <li>
                        <?php $selected_class = vce_compare($vce_meta['use_sidebar'], $id) ? ' selected' : ''; ?>
                        <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                             class="vce-img-select<?php echo $selected_class; ?>">
                        <input type="radio" class="vce-hidden" name="vce[use_sidebar]"
                               value="<?php echo $id; ?>" <?php checked($id, $vce_meta['use_sidebar']); ?>/> </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="description"><?php _e('Choose sidebar layout', THEME_SLUG); ?></p>
        </div>

        <?php if (!empty($sidebars)): ?>
        <div class="form-field">
            <label><?php _e('Standard sidebar', THEME_SLUG); ?></label>
            <select name="vce[sidebar]" class="widefat">
                <?php foreach ($sidebars as $id => $name): ?>
                    <option value="<?php echo $id; ?>" <?php selected($id, $vce_meta['sidebar']); ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php _e('Choose standard sidebar to display', THEME_SLUG); ?></p>
        </div>
        <div class="form-field">
            <label><?php _e('Sticky sidebar', THEME_SLUG); ?></label>
            <select name="vce[sticky_sidebar]" class="widefat">
                <?php foreach ($sidebars as $id => $name): ?>
                    <option value="<?php echo $id; ?>" <?php selected($id, $vce_meta['sticky_sidebar']); ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php _e('Choose sticky sidebar to display', THEME_SLUG); ?></p>
        </div>
    <?php endif; ?>

        <?php

        $most_used = get_option('vce_recent_cat_colors');

        $colors = '';

        if (!empty($most_used)) {
            $colors .= '<p>' . __('Recently used', THEME_SLUG) . ': <br/>';
            foreach ($most_used as $color) {
                $colors .= '<a href="#" style="width: 20px; height: 20px; background: ' . $color . '; float: left; margin-right:3px; border: 1px solid #aaa;" class="vce_colorpick" data-color="' . $color . '"></a>';
            }
            $colors .= '</p>';
        }

        ?>

        <div class="form-field">
            <label><?php _e('Color', THEME_SLUG); ?></label><br/>
            <label><input type="radio" name="vce[color_type]" value="inherit"
                          class="vce-radio color-type" <?php checked($vce_meta['color_type'], 'inherit'); ?>> <?php _e('Inherit from default accent color', THEME_SLUG); ?>
            </label>
            <label><input type="radio" name="vce[color_type]" value="custom"
                          class="vce-radio color-type" <?php checked($vce_meta['color_type'], 'custom'); ?>> <?php _e('Custom', THEME_SLUG); ?>
            </label>
            <div id="vce_color_wrap">
                <p>
                    <input name="vce[color]" type="text" class="vce_colorpicker"
                           value="<?php echo $vce_meta['color']; ?>"
                           data-default-color="<?php echo $vce_meta['color']; ?>"/>
                </p>
                <?php if (!empty($colors)) {
                    echo $colors;
                } ?>
            </div>
            <div class="clear"></div>
            <p class="howto"><?php _e('Choose color', THEME_SLUG); ?></p>
        </div>

	<div class="form-field">
		<label><?php esc_html_e( 'Image', THEME_SLUG ); ?></label>
		<?php $display = $vce_meta['image'] ? 'initial' : 'none'; ?>
		<p>
			<img id="vce-image-preview" src="<?php echo esc_url( $vce_meta['image'] ); ?>" style="width: 300px;  border: 2px solid #ebebeb; display:<?php echo esc_attr( $display ); ?>;">
		</p>

		<p>
			<input type="hidden" name="vce[image]" id="vce-image-url" value="<?php echo esc_attr( $vce_meta['image'] ); ?>"/>
			<input type="button" id="vce-image-upload" class="button-secondary" value="<?php esc_attr_e( 'Upload', THEME_SLUG ); ?>"/>
			<input type="button" id="vce-image-clear" class="button-secondary" value="<?php esc_attr_e( 'Clear', THEME_SLUG ); ?>" style="display:<?php echo esc_attr( $display ); ?>"/>
		</p>

		<p class="description"><?php esc_html_e( 'Upload an image for this category', THEME_SLUG ); ?></p>
	</div>

        <?php
    }
endif;

add_action('category_add_form_fields', 'vce_category_add_meta_fields', 10, 2);

if (!function_exists('vce_category_edit_meta_fields')) :
    function vce_category_edit_meta_fields($term)
    {
        $vce_meta = vce_get_category_meta($term->term_id);
        $sidebars_lay = vce_get_sidebar_layouts(true);
        $sidebars = vce_get_sidebars_list(true);
        $post_layouts = vce_get_main_layouts(true);
        $starter_layouts = vce_get_main_layouts(true, true);
        $fa_layouts = vce_get_featured_area_layouts(true, true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label><?php _e('Featured area layout', THEME_SLUG); ?></label>
            </th>
            <td>
                <ul class="vce-img-select-wrap next-hide">
                    <?php foreach ($fa_layouts as $id => $layout): ?>
                        <li>
                            <?php $selected_class = vce_compare($vce_meta['fa_layout'], $id) ? ' selected' : ''; ?>
                            <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                 class="vce-img-select<?php echo $selected_class; ?>">
                            <input type="radio" class="vce-hidden" name="vce[fa_layout]"
                                   value="<?php echo $id; ?>" <?php checked($id, $vce_meta['fa_layout']); ?>/> </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="description"><?php _e('Choose featured area layout', THEME_SLUG); ?></p>
            </td>
        </tr>

        <?php $style = $vce_meta['fa_layout'] == 'inherit' || !$vce_meta['fa_layout'] ? 'style="display:none"' : ''; ?>
        <tr class="form-field" <?php echo $style; ?>>
            <th scope="row" valign="top">
                <label><?php _e('Featured area posts limit', THEME_SLUG); ?></label>
            </th>
            <td>
                <input type="text" name="vce[fa_limit]" value="<?php echo $vce_meta['fa_limit']; ?>"
                       style="width: 30px;"/> <?php _e('post(s)', THEME_SLUG); ?>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top">
                <label><?php _e('Posts main layout', THEME_SLUG); ?></label>
            </th>
            <td>
                <ul class="vce-img-select-wrap next-hide">
                    <?php foreach ($post_layouts as $id => $layout): ?>
                        <li>
                            <?php $selected_class = vce_compare($vce_meta['layout'], $id) ? ' selected' : ''; ?>
                            <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                 class="vce-img-select<?php echo $selected_class; ?>">
                            <input type="radio" class="vce-hidden" name="vce[layout]"
                                   value="<?php echo $id; ?>" <?php checked($id, $vce_meta['layout']); ?>/> </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="description"><?php _e('Choose posts layout for this category', THEME_SLUG); ?></p>
            </td>
        </tr>

        <?php $style = $vce_meta['layout'] == 'inherit' ? 'style="display:none"' : ''; ?>
        <tr class="form-field" <?php echo $style; ?>>
            <th scope="row" valign="top">
                <label><?php _e('Number of posts per page', THEME_SLUG); ?></label>
            </th>
            <td>
                <input type="text" name="vce[ppp]" value="<?php echo $vce_meta['ppp']; ?>"
                       style="width: 30px;"/> <?php _e('post(s)', THEME_SLUG); ?><br/>
                <small class="description"><?php _e('Note: leave empty if you want to inherit from global category option', THEME_SLUG); ?></small>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top">
                <label><?php _e('Starter posts layout', THEME_SLUG); ?></label>
            </th>
            <td>
                <ul class="vce-img-select-wrap next-hide">
                    <?php foreach ($starter_layouts as $id => $layout): ?>
                        <li>
                            <?php $selected_class = vce_compare($vce_meta['top_layout'], $id) ? ' selected' : ''; ?>
                            <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                 class="vce-img-select<?php echo $selected_class; ?>">
                            <input type="radio" class="vce-hidden" name="vce[top_layout]"
                                   value="<?php echo $id; ?>" <?php checked($id, $vce_meta['top_layout']); ?>/> </label>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p class="description"><?php _e('Check if you want to use starter posts', THEME_SLUG); ?></p>
            </td>
        </tr>

        <?php $style = $vce_meta['top_layout'] == 'inherit' || !$vce_meta['top_layout'] ? 'style="display:none"' : ''; ?>
        <tr class="form-field" <?php echo $style; ?>>
            <th scope="row" valign="top">
                <label><?php _e('Starter posts limit', THEME_SLUG); ?></label>
            </th>
            <td>
                <input type="text" name="vce[top_limit]" value="<?php echo $vce_meta['top_limit']; ?>"
                       style="width: 30px;"/> <?php _e('post(s)', THEME_SLUG); ?>
            </td>
        </tr>


        <tr class="form-field">
            <th scope="row" valign="top">
                <label><?php _e('Sidebar layout', THEME_SLUG); ?></label>
            </th>
            <td>
                <ul class="vce-img-select-wrap">
                    <?php foreach ($sidebars_lay as $id => $layout): ?>
                        <li>
                            <?php $selected_class = vce_compare($vce_meta['use_sidebar'], $id) ? ' selected' : ''; ?>
                            <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                 class="vce-img-select<?php echo $selected_class; ?>">
                            <input type="radio" class="vce-hidden" name="vce[use_sidebar]"
                                   value="<?php echo $id; ?>" <?php checked($id, $vce_meta['use_sidebar']); ?>/> </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="description"><?php _e('Choose sidebar layout', THEME_SLUG); ?></p>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top">
                <label><?php _e('Standard sidebar', THEME_SLUG); ?></label>
            </th>
            <td>
                <select name="vce[sidebar]" class="widefat">
                    <?php foreach ($sidebars as $id => $name): ?>
                        <option value="<?php echo $id; ?>" <?php selected($id, $vce_meta['sidebar']); ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php _e('Choose standard sidebar to display', THEME_SLUG); ?></p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label><?php _e('Sticky sidebar', THEME_SLUG); ?></label>
            </th>
            <td>
                <select name="vce[sticky_sidebar]" class="widefat">
                    <?php foreach ($sidebars as $id => $name): ?>
                        <option value="<?php echo $id; ?>" <?php selected($id, $vce_meta['sticky_sidebar']); ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php _e('Choose sticky sidebar to display', THEME_SLUG); ?></p>
            </td>
        </tr>

        <?php

        $most_used = get_option('vce_recent_cat_colors');

        $colors = '';

        if (!empty($most_used)) {
            $colors .= '<p>' . __('Recently used', THEME_SLUG) . ': <br/>';
            foreach ($most_used as $color) {
                $colors .= '<a href="#" style="width: 20px; height: 20px; background: ' . $color . '; float: left; margin-right:3px; border: 1px solid #aaa;" class="vce_colorpick" data-color="' . $color . '"></a>';
            }
            $colors .= '</p>';
        }

        ?>

        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e('Color', THEME_SLUG); ?></label></th>
            <td>
                <label><input type="radio" name="vce[color_type]" value="inherit"
                              class="vce-radio color-type" <?php checked($vce_meta['color_type'], 'inherit'); ?>> <?php _e('Inherit from default accent color', THEME_SLUG); ?>
                </label> <br/>
                <label><input type="radio" name="vce[color_type]" value="custom"
                              class="vce-radio color-type" <?php checked($vce_meta['color_type'], 'custom'); ?>> <?php _e('Custom', THEME_SLUG); ?>
                </label>
                <div id="vce_color_wrap">
                    <p>
                        <input name="vce[color]" type="text" class="vce_colorpicker"
                               value="<?php echo $vce_meta['color']; ?>"
                               data-default-color="<?php echo $vce_meta['color']; ?>"/>
                    </p>
                    <?php if (!empty($colors)) {
                        echo $colors;
                    } ?>
                </div>
                <div class="clear"></div>
                <p class="howto"><?php _e('Choose color', THEME_SLUG); ?></p>
            </td>
        </tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Image', THEME_SLUG ); ?></label></th>
			<?php $display = $vce_meta['image'] ? 'initial' : 'none'; ?>
			<td>
			<p>
				<img id="vce-image-preview" src="<?php echo esc_url( $vce_meta['image'] ); ?>" style="width: 300px;  border: 2px solid #ebebeb; display:<?php echo esc_attr( $display ); ?>;">
			</p>

			<p>
				<input type="hidden" name="vce[image]" id="vce-image-url" value="<?php echo esc_attr( $vce_meta['image'] ); ?>"/>
				<input type="button" id="vce-image-upload" class="button-secondary" value="<?php esc_attr_e( 'Upload', THEME_SLUG ); ?>"/>
				<input type="button" id="vce-image-clear" class="button-secondary" value="<?php esc_attr_e( 'Clear', THEME_SLUG ); ?>" style="display:<?php echo esc_attr( $display ); ?>"/>
			</p>

			<p class="description"><?php esc_html_e( 'Upload an image for this category', THEME_SLUG ); ?></p>
			</td>
		</tr>        

        <?php
    }
endif;

add_action('category_edit_form_fields', 'vce_category_edit_meta_fields', 10, 2);


if (!function_exists('vce_save_category_meta_fields')) :
    function vce_save_category_meta_fields($term_id)
    {

        if (isset($_POST['vce'])) {

            $vce_meta = array();

            $vce_meta['layout'] = isset($_POST['vce']['layout']) ? $_POST['vce']['layout'] : 0;
            $vce_meta['ppp'] = isset($_POST['vce']['ppp']) && !empty($_POST['vce']['ppp']) ? absint($_POST['vce']['ppp']) : '';
            $vce_meta['top_layout'] = isset($_POST['vce']['top_layout']) ? $_POST['vce']['top_layout'] : 0;
            $vce_meta['top_limit'] = isset($_POST['vce']['top_limit']) ? $_POST['vce']['top_limit'] : 0;
            $vce_meta['fa_layout'] = isset($_POST['vce']['fa_layout']) ? $_POST['vce']['fa_layout'] : 0;
            $vce_meta['fa_limit'] = isset($_POST['vce']['fa_limit']) ? $_POST['vce']['fa_limit'] : 0;
            $vce_meta['use_sidebar'] = isset($_POST['vce']['use_sidebar']) ? $_POST['vce']['use_sidebar'] : 0;
            $vce_meta['sidebar'] = isset($_POST['vce']['sidebar']) ? $_POST['vce']['sidebar'] : 0;
            $vce_meta['sticky_sidebar'] = isset($_POST['vce']['sticky_sidebar']) ? $_POST['vce']['sticky_sidebar'] : 0;
            $vce_meta['color_type'] = isset($_POST['vce']['color_type']) ? $_POST['vce']['color_type'] : 0;
            $vce_meta['color'] = isset($_POST['vce']['color']) ? $_POST['vce']['color'] : 0;
            $vce_meta['image'] = isset( $_POST['vce']['image']) ? $_POST['vce']['image'] : '';

            update_option('_vce_category_' . $term_id, $vce_meta);

            if ($vce_meta['color_type'] == 'custom') {
                vce_update_recent_cat_colors($vce_meta['color']);
            }

            vce_update_cat_colors($term_id, $vce_meta['color'], $vce_meta['color_type']);
        }

    }
endif;

add_action('edited_category', 'vce_save_category_meta_fields', 10, 2);
add_action('create_category', 'vce_save_category_meta_fields', 10, 2);
