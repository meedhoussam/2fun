<?php


/* Add post metaboxes */
if (!function_exists('vce_load_post_metaboxes')) :
    function vce_load_post_metaboxes()
    {

        /* Sidebar metabox */
        add_meta_box(
            'vce_sidebar',
            __('Sidebar', THEME_SLUG),
            'vce_sidebar_metabox',
            'post',
            'side',
            'default'
        );

        /* Layout metabox */
        add_meta_box(
            'vce_layout',
            __('Layout', THEME_SLUG),
            'vce_layout_metabox',
            'post',
            'side',
            'default'
        );

        /* Display options metabox */
        add_meta_box(
            'vce_display',
            __('Display Options', THEME_SLUG),
            'vce_display_metabox',
            'post',
            'side',
            'default'
        );

    }
endif;


/* Save Post Meta */
if (!function_exists('vce_save_post_metaboxes')) :
    function vce_save_post_metaboxes($post_id, $post)
    {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (isset($_POST['vce_post_nonce'])) {
            if (!wp_verify_nonce($_POST['vce_post_nonce'], __FILE__))
                return;
        }


        if ($post->post_type == 'post' && isset($_POST['vce'])) {
            $post_type = get_post_type_object($post->post_type);
            if (!current_user_can($post_type->cap->edit_post, $post_id))
                return $post_id;

            $vce_meta = array();

            $vce_meta['use_sidebar'] = isset($_POST['vce']['use_sidebar']) ? $_POST['vce']['use_sidebar'] : 0;
            $vce_meta['sidebar'] = isset($_POST['vce']['sidebar']) ? $_POST['vce']['sidebar'] : 0;
            $vce_meta['sticky_sidebar'] = isset($_POST['vce']['sticky_sidebar']) ? $_POST['vce']['sticky_sidebar'] : 0;
            $vce_meta['layout'] = isset($_POST['vce']['layout']) ? $_POST['vce']['layout'] : 0;

            if (isset($_POST['vce']['display']) && !empty($_POST['vce']['display'])) {
                foreach ($_POST['vce']['display'] as $key => $value) {
                    if ($value != 'inherit') {
                        $vce_meta['display'][$key] = $value;
                    }
                }
            }

            update_post_meta($post_id, '_vce_meta', $vce_meta);

        }
    }
endif;


/* Create Sidebars Metabox */
if (!function_exists('vce_sidebar_metabox')) :
    function vce_sidebar_metabox($object, $box)
    {
        $vce_meta = vce_get_post_meta($object->ID);
        $sidebars_lay = vce_get_sidebar_layouts(true);
        $sidebars = vce_get_sidebars_list(true);
        ?>
        <ul class="vce-img-select-wrap">
            <?php foreach ($sidebars_lay as $id => $layout): ?>
                <li>
                    <?php $selected_class = $id == $vce_meta['use_sidebar'] ? ' selected' : ''; ?>
                    <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                         class="vce-img-select<?php echo $selected_class; ?>">
                    <span><?php echo $layout['title']; ?></span>
                    <input type="radio" class="vce-hidden" name="vce[use_sidebar]"
                           value="<?php echo $id; ?>" <?php checked($id, $vce_meta['use_sidebar']); ?>/> </label>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="description"><?php _e('Sidebar layout', THEME_SLUG); ?></p>

        <?php if (!empty($sidebars)): ?>

        <p><select name="vce[sidebar]" class="widefat">
                <?php foreach ($sidebars as $id => $name): ?>
                    <option value="<?php echo $id; ?>" <?php selected($id, $vce_meta['sidebar']); ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select></p>
        <p class="description"><?php _e('Choose standard sidebar to display', THEME_SLUG); ?></p>

        <p><select name="vce[sticky_sidebar]" class="widefat">
                <?php foreach ($sidebars as $id => $name): ?>
                    <option value="<?php echo $id; ?>" <?php selected($id, $vce_meta['sticky_sidebar']); ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select></p>
        <p class="description"><?php _e('Choose sticky sidebar to display', THEME_SLUG); ?></p>

    <?php endif; ?>
        <?php
    }
endif;

if (!function_exists('vce_layout_metabox')) :

    function vce_layout_metabox($object, $box)
    {
        $vce_meta = vce_get_post_meta($object->ID);
        $layouts = vce_get_single_layout_opts(true);

        ?>
        <ul class="vce-img-select-wrap">
            <?php foreach ($layouts as $id => $layout): ?>
                <li>
                    <?php $selected_class = $id == $vce_meta['layout'] ? ' selected' : ''; ?>
                    <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                         class="vce-img-select<?php echo $selected_class; ?>">
                    <span><?php echo $layout['title']; ?></span>
                    <input type="radio" class="vce-hidden" name="vce[layout]"
                           value="<?php echo $id; ?>" <?php checked($id, $vce_meta['layout']); ?>/> </label>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="description"><?php _e('Choose a layout for this post', THEME_SLUG); ?></p>
        <?php
    }

endif;


/* Create Display Options Metabox */
if (!function_exists('vce_display_metabox')) :
    function vce_display_metabox($object, $box)
    {
        $vce_meta = vce_get_post_meta($object->ID);
        ?>
        <p class="description"><?php _e('Override display options for this particular post instead of using global options set in Theme Options -> Single Post', THEME_SLUG); ?></p>
        <p><label><?php _e('Category link', THEME_SLUG); ?>
                :</label> <?php vce_post_display_option('show_cat', $vce_meta['display']['show_cat']) ?></p>
        <p><label><?php _e('Featured image', THEME_SLUG); ?>
                :</label> <?php vce_post_display_option('show_fimg', $vce_meta['display']['show_fimg']) ?></p>
        <p><label><?php _e('Author image', THEME_SLUG); ?>
                :</label> <?php vce_post_display_option('show_author_img', $vce_meta['display']['show_author_img']) ?>
        </p>
        <p><label><?php _e('Headline (excerpt)', THEME_SLUG); ?>
                :</label> <?php vce_post_display_option('show_headline', $vce_meta['display']['show_headline']) ?></p>
        <p><label><?php _e('Tags', THEME_SLUG); ?>
                :</label> <?php vce_post_display_option('show_tags', $vce_meta['display']['show_tags']) ?></p>
        <p><label><?php _e('Prev/next posts', THEME_SLUG); ?>
                :</label> <?php vce_post_display_option('show_prev_next', $vce_meta['display']['show_prev_next']) ?></p>
        <p><label><?php _e('Related posts', THEME_SLUG); ?>
                :</label> <?php vce_post_display_option('show_related', $vce_meta['display']['show_related']) ?></p>
        <p><label><?php _e('Author box', THEME_SLUG); ?>
                :</label> <?php vce_post_display_option('show_author_box', $vce_meta['display']['show_author_box']) ?>
        </p>
        <?php
    }
endif;