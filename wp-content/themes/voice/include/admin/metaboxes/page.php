<?php

/* Add page metaboxes */
if (!function_exists('vce_load_page_metaboxes')) :
    function vce_load_page_metaboxes()
    {

        /* Sidebar metabox */
        add_meta_box(
            'vce_sidebar',
            __('Sidebar', THEME_SLUG),
            'vce_sidebar_metabox',
            'page',
            'side',
            'default'
        );

        /* Layout metabox */
        add_meta_box(
            'vce_layout',
            __('Layout', THEME_SLUG),
            'vce_layout_metabox_page',
            'page',
            'side',
            'default'
        );

        /* Featured area metabox */
        add_meta_box(
            'vce_hp_fa',
            __('Featured Area/Slider', THEME_SLUG),
            'vce_fa_metabox',
            'page',
            'normal',
            'high'
        );

        /* Modules metabox */
        add_meta_box(
            'vce_hp_modules',
            __('Modules', THEME_SLUG),
            'vce_modules_metabox',
            'page',
            'normal',
            'high'
        );

        /* Authors Metabox */
        add_meta_box(
            'vce_authors',
            __('Authors', THEME_SLUG),
            'vce_authors_metabox',
            'page',
            'side',
            'default'
        );

        /* Content metabox */
        add_meta_box(
            'vce_hp_content',
            __('Page Content/Editor Options', THEME_SLUG),
            'vce_page_content_metabox',
            'page',
            'normal',
            'high'
        );


    }
endif;


/* Save Page Meta */
if (!function_exists('vce_save_page_metaboxes')) :
    function vce_save_page_metaboxes($post_id, $post)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (isset($_POST['vce_page_nonce'])) {
            if (!wp_verify_nonce($_POST['vce_page_nonce'], __FILE__))
                return;
        }

        if ($post->post_type == 'page' && isset($_POST['vce'])) {
            $post_type = get_post_type_object($post->post_type);
            if (!current_user_can($post_type->cap->edit_post, $post_id))
                return $post_id;

            $vce_meta = array();

            $vce_meta['use_sidebar'] = isset($_POST['vce']['use_sidebar']) ? $_POST['vce']['use_sidebar'] : 0;
            $vce_meta['sidebar'] = isset($_POST['vce']['sidebar']) ? $_POST['vce']['sidebar'] : 0;
            $vce_meta['sticky_sidebar'] = isset($_POST['vce']['sticky_sidebar']) ? $_POST['vce']['sticky_sidebar'] : 0;
            $vce_meta['layout'] = isset($_POST['vce']['layout']) ? $_POST['vce']['layout'] : '';

            $vce_meta['fa_layout'] = isset($_POST['vce']['fa_layout']) ? $_POST['vce']['fa_layout'] : 0;

            if ($vce_meta['fa_layout']) {
                $vce_meta['fa_limit'] = isset($_POST['vce']['fa_limit']) ? absint($_POST['vce']['fa_limit']) : 0;
                $vce_meta['fa_time'] = isset($_POST['vce']['fa_time']) ? $_POST['vce']['fa_time'] : 0;
                $vce_meta['fa_order'] = isset($_POST['vce']['fa_order']) ? $_POST['vce']['fa_order'] : 0;
                $vce_meta['fa_exclude'] = isset($_POST['vce']['fa_exclude']) ? $_POST['vce']['fa_exclude'] : 0;
                $vce_meta['fa_sort'] = isset($_POST['vce']['fa_sort']) ? $_POST['vce']['fa_sort'] : 'DESC';
                $vce_meta['fa_author'] = isset($_POST['vce']['fa_author']) && !empty($_POST['vce']['fa_author']) ? vce_get_authors_id_by_username($_POST['vce']['fa_author']) : array();
                $vce_meta['fa_author_inc_exc'] = isset($_POST['vce']['fa_author_inc_exc']) ? $_POST['vce']['fa_author_inc_exc'] : 'in';
                $vce_meta['fa_exclude_by_id'] = isset($_POST['vce']['fa_exclude_by_id']) && !empty($_POST['vce']['fa_exclude_by_id']) ? array_map('absint', explode(",", $_POST['vce']['fa_exclude_by_id'])) : array();
                $vce_meta['fa_post_type'] = $_POST['vce']['fa_post_type'];
                
	            $post_type_with_taxonomies = vce_get_post_type_with_taxonomies($_POST['vce']['fa_post_type']);
	            
	            if(!empty($post_type_with_taxonomies->taxonomies)){
		            foreach ( $post_type_with_taxonomies->taxonomies as $taxonomy ) {
			
			            $taxonomy_id = vce_patch_taxonomy_id($taxonomy['id']);
			
			            if(empty($_POST['vce']['fa_' . $taxonomy_id])){
			                continue;
			            }
			                
                        $vce_meta['fa_' . $taxonomy_id . '_inc_exc'] = $_POST['vce']['fa_' . $taxonomy_id . '_inc_exc'];
			            $vce_meta['fa_'. $taxonomy_id] = $_POST['vce']['fa_' . $taxonomy_id];
			            
                        if($taxonomy['hierarchical']){
	                       $vce_meta['fa_' . $taxonomy_id . '_child'] = $_POST['vce']['fa_' . $taxonomy_id . '_child'];
                        }else{
	                        $new_tags = explode(",",  $_POST['vce']['fa_' . $taxonomy_id]);
	                        if ($new_tags) {
		                        foreach ($new_tags as $new_tag) {
			                        wp_insert_term($new_tag, $taxonomy['id']);
		                        }
	                        }
                        }
		            }
	            }
	            
                if (isset($_POST['vce']['fa_manual']) && !empty($_POST['vce']['fa_manual'])) {
                    $vce_meta['fa_manual'] = array_map('absint', explode(",", $_POST['vce']['fa_manual']));
                }

            }

            if (isset($_POST['vce']['authors'])) {
                $vce_meta['authors'] = array();
                $vce_meta['authors']['orderby'] = isset($_POST['vce']['authors']['orderby']) ? $_POST['vce']['authors']['orderby'] : 0;
                $vce_meta['authors']['order'] = isset($_POST['vce']['authors']['order']) ? $_POST['vce']['authors']['order'] : 'DESC';
                $vce_meta['authors']['exclude'] = isset($_POST['vce']['authors']['exclude']) ? $_POST['vce']['authors']['exclude'] : '';
            }

            if (isset($_POST['vce']['modules'])) {

                //vce_log( $_POST['vce']['modules'] );

                $vce_meta['modules'] = array_values($_POST['vce']['modules']);

                //vce_log( $vce_meta['modules'] );

                foreach ($vce_meta['modules'] as $i => $module) {
                    if (isset($module['manual']) && !empty($module['manual'])) {
                        $vce_meta['modules'][$i]['manual'] = array_map('absint', explode(",", $module['manual']));
                    }

                    if (isset($module['tag']) && !empty($module['tag'])) {
                        $module_tags = explode(",", $module['tag']);
                        {
                            foreach ($module_tags as $module_tag) {
                                wp_insert_term($module_tag, 'post_tag');
                            }
                        }
                    }

                    if (isset($module['author']) && !empty($module['author'])) {
                        $vce_meta['modules'][$i]['author'] = vce_get_authors_id_by_username($module['author']);
                        $vce_meta['modules'][$i]['author_inc_exc'] = isset($vce_meta['modules'][$i]['author_inc_exc']) ? $module['author_inc_exc'] : 'in';
                    }

                    if (isset($module['exclude_by_id']) && !empty($module['exclude_by_id'])) {
                        $vce_meta['modules'][$i]['exclude_by_id'] = array_map('absint', explode(",", $module['exclude_by_id']));
                    }

                    $vce_meta['modules'][$i]['cat'] = !empty($module['cat']) ? $module['cat'] : array();


                    if (!empty($module['tax'])) {

                        $taxonomies = array();
                        foreach ($module['tax'] as $k => $tax) {

                            if (!empty($tax)) {

                                if (is_array($tax)) {
                                    $taxonomies[$k] = $tax;
                                } else {
                                    $taxonomies[$k] = vce_get_tax_term_id_by_name($tax, $k);
                                }
                            }

                        }

                        $vce_meta['modules'][$i]['tax'] = $taxonomies;
                    }

                }

            }

            $vce_meta['display_content'] = isset($_POST['vce']['display_content']) ? $_POST['vce']['display_content'] : array();

            update_post_meta($post_id, '_vce_meta', $vce_meta);

        }
    }
endif;

if (!function_exists('vce_layout_metabox_page')) :

    function vce_layout_metabox_page($object, $box)
    {
        $vce_meta = vce_get_page_meta($object->ID);
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
        <p class="description"><?php _e('Choose a layout for this page', THEME_SLUG); ?></p>
        <?php
    }

endif;

/* Create Featured area Metabox */
if (!function_exists('vce_fa_metabox')) :
    function vce_fa_metabox($object, $box)
    {
        $vce_meta = vce_get_page_meta($object->ID);
        $fa_layouts = vce_get_featured_area_layouts(false, true);
        $order = vce_get_post_order_opts();
        $time = vce_get_time_diff_opts();
	    $post_types = vce_get_posts_types_with_taxonomies(array('page'));
        ?>
        <div class="vce-opt-box">
            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                    <?php _e('Choose layout', THEME_SLUG); ?>:
                </div>

                <div class="vce-opt-content">
                    <ul class="vce-img-select-wrap">
                        <?php foreach ($fa_layouts as $id => $layout): ?>
                            <li>
                                <?php $selected_class = vce_compare($id, $vce_meta['fa_layout']) ? ' selected' : ''; ?>
                                <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                     class="vce-img-select<?php echo $selected_class; ?>">
                                <label><input type="radio" class="vce-hidden" name="vce[fa_layout]"
                                       value="<?php echo $id; ?>" <?php checked($id, $vce_meta['fa_layout']); ?>/></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>
            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                    <?php _e('Post type', THEME_SLUG); ?>:
                </div>
                <div class="vce-opt-content">
                    <select class="vce-fa-post-type" name="vce[fa_post_type]">
		                <?php foreach ($post_types as $post_type) :?>
			                <?php
			                if( empty($post_type) ){
				                continue;
			                }
			                ?>
                            <option value="<?php echo esc_attr($post_type->name)?>" <?php selected($vce_meta['fa_post_type'], $post_type->name); ?>><?php echo esc_attr($post_type->labels->singular_name); ?></option>
		                <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                    <?php _e('Number of posts to show', THEME_SLUG); ?>:
                </div>
                <div class="vce-opt-content">
                    <label><input type="text" name="vce[fa_limit]" class="small-text"
                           value="<?php echo $vce_meta['fa_limit']; ?>"/></label>
                </div>
            </div>
            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                    <?php _e('Choose manually', THEME_SLUG); ?>:
                </div>
                <div class="vce-opt-content vce-live-search-opt">
                    <input type="text" class="vce-live-search widefat vce-live-search-with-cpts" placeholder="<?php esc_html_e( 'Type to search...', THEME_SLUG ); ?>" /><br/>
                    <?php $manualy_selected_posts = vce_get_manually_selected_posts($vce_meta['fa_manual'], 'featured'); ?>
                    <?php $manual = !empty( $manualy_selected_posts ) ? implode( ",", $vce_meta['fa_manual'] ) : ''; ?>
                    <input type="hidden" class="vce-live-search-hidden" data-type="featured" name="vce[fa_manual]" value="<?php echo esc_attr($manual); ?>" />
                    <div class="vce-live-search-items tagchecklist">
                        <?php vce_display_manually_selected_posts($manualy_selected_posts); ?>
                    </div>
                </div>
            </div>

            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                    <?php _e('Order posts by', THEME_SLUG); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ($order as $id => $title) : ?>
                        <label><input type="radio" name="vce[fa_order]"
                                      value="<?php echo $id; ?>" <?php checked($vce_meta['fa_order'], $id); ?> /><?php echo $title; ?>
                        </label><br/>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                    <?php esc_html_e('Sort', THEME_SLUG); ?>:

                </div>
                <div class="vce-opt-content">
                    <label><input type="radio" name="vce[fa_sort]"
                                  value="DESC" <?php checked($vce_meta['fa_sort'], 'DESC'); ?>
                                  class="vce-count-me"/><?php esc_html_e('Descending', THEME_SLUG) ?></label><br/>
                    <label><input type="radio" name="vce[fa_sort]"
                                  value="ASC" <?php checked($vce_meta['fa_sort'], 'ASC'); ?>
                                  class="vce-count-me"/><?php esc_html_e('Ascending', THEME_SLUG) ?></label><br/><br>
                </div>
            </div>

            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                    <?php _e('Do not duplicate', THEME_SLUG); ?>
                </div>
                <div class="vce-opt-content">
                    <label><input type="checkbox" name="vce[fa_exclude]"
                           value="1" <?php checked($vce_meta['fa_exclude'], 1) ?>/></label>
                    <small class="howto"><?php _e('Check this option to always exclude featured area posts from modules below so they don\'t appear twice', THEME_SLUG); ?></small>

                </div>
            </div>

        </div>


        <div class="vce-opt-box">
	        <?php foreach ( $post_types as $post_type ) :
		
		        if ( empty( $post_type->taxonomies ) ) {
			        continue;
		        }
		
		        foreach ( $post_type->taxonomies as $taxonomy ) :
			
			        if ( ! isset( $taxonomy['hierarchical'] ) ) {
				        continue;
			        }
			
			        if( $taxonomy['hierarchical'] && empty( $taxonomy['terms'] ) ){
				        continue;
			        }
			
			        ?>

                    <div class="vce-opt-inline vce-watch-for-changes" data-watch="vce-fa-post-type" data-show-on-value="<?php echo esc_attr($post_type->name);?>">
                        <div class="vce-opt-title">
					        <?php echo esc_attr( $taxonomy['name'] ); ?>:
                        </div>
                        <div class="vce-opt-content">
					        <?php
					
					        $taxonomy_id = vce_patch_taxonomy_id($taxonomy['id']);
					
					        if ( $taxonomy['hierarchical'] ):
						        if ( empty( $taxonomy['terms'] ) ) {
							        continue;
						        }
						        ?>
                                <div class="vce-item-scroll">
							        <?php foreach ( $taxonomy['terms'] as $term ) : ?>
								        <?php $checked = !empty($vce_meta['fa_' . $taxonomy_id]) && in_array( $term->term_id, $vce_meta['fa_' . $taxonomy_id] ) ? 'checked="checked"' : ''; ?>
                                        <label>
                                            <input class="vce-count-me" type="checkbox" name="vce<?php echo esc_attr( '[fa_' . $taxonomy_id . ']' ); ?>[]" value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo $checked; ?> /><?php echo $term->name; ?>
                                        </label>
                                        <br/>
							        <?php endforeach; ?>
                                </div>
                                <br>
                                <label>
                                    <?php $apply_child =  !empty($vce_meta['fa_' . $taxonomy_id . '_child']) ? $vce_meta['fa_' . $taxonomy_id . '_child'] : 0; ?>
                                    <input type="checkbox" name="vce<?php echo esc_attr('[fa_' . $taxonomy_id . '_child]'); ?>" value="1" class="vce-count-me" <?php checked($apply_child, 1); ?>/>
                                    <strong><?php printf(esc_html__('Apply child %s', THEME_SLUG), strtolower($taxonomy['name'])); ?></strong>
                                </label>
                                <small class="howto"><?php printf(esc_html__( 'Check whether you want to display posts from specific %s only', THEME_SLUG ), strtolower($taxonomy['name'])); ?></small>
					        <?php else: ?>
                                <div class="tagsdiv" id="<?php echo esc_attr($taxonomy['id']); ?>">
                                    <?php if(!vce_is_gutenberg_page()): ?>
                                    <div class="jaxtag">
                                        <div class="nojs-tags hide-if-js">
                                            <label for="tax-input-<?php echo esc_attr($taxonomy['id']); ?>"><?php printf(esc_html__('Add or remove %s '), strtolower($taxonomy['name']))?></label>
                                            <p><input type="hidden" name="vce[fa_<?php echo esc_attr($taxonomy_id); ?>]" class="the-tags" id="tax-input-<?php echo esc_attr($taxonomy['id']); ?>"  aria-describedby="new-tag-<?php echo esc_attr($taxonomy['id']); ?>-desc" value="<?php echo !empty($vce_meta['fa_' . $taxonomy_id]) ? esc_attr($vce_meta['fa_' . $taxonomy_id]) : ''?>"></p>
                                        </div>
                                        <div class="ajaxtag hide-if-no-js">
                                            <label class="screen-reader-text" for="new-tag-<?php echo esc_attr($taxonomy['id']); ?>"><?php printf(esc_html__('Add new %s '), strtolower($taxonomy['name']))?></label>
                                            <p>
                                                <input data-wp-taxonomy="<?php echo esc_attr($taxonomy['id']); ?>" type="text" id="new-tag-<?php echo esc_attr($taxonomy['id']); ?>" name="newtag[<?php echo esc_attr($taxonomy['id']); ?>]" class="newtag form-input-tip ui-autocomplete-input" size="16" autocomplete="off" aria-describedby="new-tag-<?php echo esc_attr($taxonomy['id']); ?>-desc" value="">
                                                <input type="button" class="button tagadd" value="Add">
                                            </p>
                                        </div>
                                        <p class="howto" id="new-tag-<?php echo esc_attr($taxonomy['id']); ?>-desc"><?php printf(esc_html__('Separate %s with commas'), $taxonomy['name'])?></p>
                                    </div>
                                    <ul class="tagchecklist" role="list"></ul>
                                    <?php else: ?>
                                        <p><input type="text" name="vce[fa_<?php echo esc_attr($taxonomy_id); ?>]" value="<?php echo !empty($vce_meta['fa_' . $taxonomy_id]) ? esc_attr($vce_meta['fa_' . $taxonomy_id]) : ''?>"></p>
                                        <p class="howto"><?php printf(esc_html__('Separate %s with commas'), $taxonomy['name'])?></p>
                                    <?php endif; ?>
                                </div>
					        <?php endif; ?>
                            <?php
					         $taxonomy_inc_exc = empty($vce_meta['fa_' . $taxonomy_id . '_inc_exc' ]) ? 'in' : $vce_meta['fa_' . $taxonomy_id . '_inc_exc' ];
					        ?>
                            <br/>
                            <label><input type="radio" name="vce<?php echo esc_attr( '[fa_' . $taxonomy_id . '_inc_exc]' ); ?>" value="in" <?php checked( $taxonomy_inc_exc, 'in' ); ?> class="vce-count-me"/><?php esc_html_e( 'Include', THEME_SLUG ) ?>
                            </label><br/>
                            <label><input type="radio" name="vce<?php echo esc_attr( '[fa_' . $taxonomy_id . '_inc_exc]' ) ; ?>" value="not_in" <?php checked( $taxonomy_inc_exc, 'not_in' ); ?> class="vce-count-me"/><?php esc_html_e( 'Exclude', THEME_SLUG ) ?>
                            </label><br/>
                            <small class="howto"><?php printf(esc_html__( 'Whether to include or exclude posts from selected %s', THEME_SLUG ), strtolower($taxonomy['name'])); ?></small>
                        </div>
                        <br>
                    </div>
		        <?php endforeach; ?>
            <?php endforeach; ?>
            
            <!-- filter by author -->
            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                    <?php esc_html_e('Filter by author (username)', THEME_SLUG); ?>:
                </div>
                <div class="vce-opt-content">
                    <label><input type="text" name="vce[fa_author]"
                           value="<?php echo esc_attr(vce_get_authors_username_by_id($vce_meta['fa_author'])); ?>"
                           class="vce-count-me"/></label>
                    <br>
                    <label><input type="radio" name="vce[fa_author_inc_exc]"
                                  value="in" <?php checked($vce_meta['fa_author_inc_exc'], 'in'); ?>
                                  class="vce-count-me"/><?php esc_html_e('Include', THEME_SLUG) ?></label><br/>
                    <label><input type="radio" name="vce[fa_author_inc_exc]"
                                  value="not_in" <?php checked($vce_meta['fa_author_inc_exc'], 'not_in'); ?>
                                  class="vce-count-me"/><?php esc_html_e('Exclude', THEME_SLUG) ?></label><br/>
                    <small class="howto"><?php esc_html_e('Whether to include or exclude author posts', THEME_SLUG); ?></small>
                </div>
            </div>
            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                    <?php esc_html_e('Exclude by id', THEME_SLUG); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php $ids = !empty($vce_meta['fa_exclude_by_id']) ? implode(', ', $vce_meta['fa_exclude_by_id']) : ''; ?>
                    <input type="text" name="vce[fa_exclude_by_id]" value="<?php echo esc_attr($ids); ?>"
                           class="vce-count-me"/>
                    <small class="howto"><?php _e('Specify post ids separated by comma i.e. 213,32,12,45', THEME_SLUG); ?></small>
            </div>
            </div>
            <div class="vce-opt-inline">
                <div class="vce-opt-title">
                 <?php _e('Posts are not older than', THEME_SLUG); ?>
                </div>
                <div class="vce-opt-content">
                    <?php foreach ($time['from'] as $id => $title) : ?>
                        <label><input type="radio" name="vce[fa_time]"
                                      value="<?php echo $id; ?>" <?php checked($vce_meta['fa_time'], $id); ?> /><?php echo $title; ?>
                        </label><br/>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php
    }
endif;


/* Create Modules Metabox */
if (!function_exists('vce_modules_metabox')) :
    function vce_modules_metabox($object, $box){

        $vce_meta = vce_get_page_meta($object->ID);
        $module_posts_def = vce_get_module_defaults_posts();


        $module_posts_options = vce_get_module_options_posts();
        $module_text_def = vce_get_module_defaults_text();
        $module_cpt_def = vce_get_module_defaults_cpt();
        $module_cpt_options = vce_get_module_options_cpt();
        $module_cat_def = vce_get_module_defaults_category();
        $module_cat_options = vce_get_module_options_category();

        ?>

        <div class="vce-modules">

        <?php if (!empty($vce_meta['modules'])) : ?>
        
            <?php foreach ($vce_meta['modules'] as $i => $module) : ?>

                <?php

                if (!isset($module['type'])) {
                    $vce_meta['modules'][$i]['type'] = 'posts';
                    $module['type'] = 'posts';
                }

                if ($module['type'] == 'posts') {
                    $module = wp_parse_args((array)$module, $module_posts_def);
                } else if ($module['type'] == 'blank') {
                    $module = wp_parse_args((array)$module, $module_text_def);
                } else if ($module['type'] == 'category') {
                    $module = wp_parse_args((array)$module, $module_cat_def);
                } else {
                    $module = wp_parse_args((array)$module, $module_cpt_def[$module['type']]);
                }

                $vce_module_disabled = $module['active'] == 0 ? ' vce-module-disabled ' : '';
                $vce_deactivate = $module['active'] ? '' : 'vce-hidden';
                $vce_activate = $module['active'] ? 'vce-hidden' : '';
                $edit = ($i === false) ? '' : 'edit';
                ?>

                <div data-module="<?php echo $i; ?>"
                     class="vce-module vce-module-<?php echo $i; ?> <?php echo esc_attr($vce_module_disabled); ?>">
                    <div class="left">
                            <span class="vce-module-type">
                                <?php echo(ucfirst($module['type'])); ?>
                            </span>
                        <span class="vce-module-title"><?php echo esc_html($module['title']); ?></span>
                    </div>

                    <div class="right">

                            <span class="actions">
                                <a href="javascript:void(0);"
                                   class="vce-edit-module"><?php _e('Edit', THEME_SLUG); ?></a> |
                                <a href="javascript:void(0);" class="vce-deactivate-module">
                                    <span class="<?php echo esc_attr($vce_activate); ?>"><?php _e('Activate', THEME_SLUG); ?></span>
                                    <span class="<?php echo esc_attr($vce_deactivate); ?>"><?php _e('Deactivate', THEME_SLUG); ?></span>
                                </a> |
                                <a href="javascript:void(0);"
                                   class="vce-remove-module"><?php _e('Remove', THEME_SLUG); ?></a>
                            </span>
                    </div>


                    <div class="vce-module-form <?php echo esc_attr($edit); ?>"
                         data-module="<?php echo esc_attr($i); ?>">
                        <input class="vce-module-deactivate vce-count-me" type="hidden" name="vce[modules][<?php echo $i; ?>][active]" value="<?php echo esc_attr($module['active']); ?>"/>
                        <input class="vce-count-me" type="hidden" name="vce[modules][<?php echo $i; ?>][type]" value="<?php echo esc_attr($module['type']); ?>"/>
                        <?php if ($module['type'] == 'posts') {
                            vce_generate_module_field($module, $i, $module_posts_options);
                        } else if ($module['type'] == 'blank') {
                            vce_generate_blank_module_field($module, $i, array());
                        } elseif ($module['type'] == 'category') {
                            vce_generate_cats_module_field($module, $i, $module_cat_options);
                        } else {
                            vce_generate_cpt_module_field($module, $i, $module_cpt_options[$module['type']]);
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
    <?php endif; ?>

     </div>

        <div class="vce-modules-bottom">
            <a href="javascript:void(0);"
               class="vce-add-module button-secondary" data-type="posts">
                + <?php _e('Posts module', THEME_SLUG); ?></a>
            <a href="javascript:void(0);"
               class="vce-add-module button-secondary" data-type="blank">
                + <?php _e('Blank module', THEME_SLUG); ?></a>
            <a href="javascript:void(0);"
               class="vce-add-module button-secondary" data-type="category">
                + <?php _e('Category module', THEME_SLUG); ?></a>
            <?php foreach ($module_cpt_def as $cpt): ?>
                <a href="javascript:void(0);" class="button-secondary vce-add-module"
                   data-type="<?php echo $cpt['type'] ?>">
                    + <?php echo ucfirst($cpt['type_name']) . __(' module ', THEME_SLUG); ?> </a>
            <?php endforeach ?>
        </div>

        <div class="vce-modules-count" data-count="<?php echo count($vce_meta['modules']); ?>"></div>

        <div id="vce-module-clone">

            <div class="posts">
                <div class="vce-module">
                    <div class="left">
                        <span class="vce-module-type"><?php _e('Posts', THEME_SLUG); ?></span>
                        <span class="vce-module-title"></span>
                    </div>

                    <div class="right">
                        <a href="javascript:void(0);"
                           class="vce-edit-module"><?php esc_html_e('Edit', THEME_SLUG); ?></a> |
                        <a href="javascript:void(0);"
                           class="vce-deactivate-module"><span class="vce-hidden"><?php esc_html_e('Activate', THEME_SLUG); ?></span><span><?php esc_html_e('Deactivate', THEME_SLUG); ?></span></a>
                        |
                        <a href="javascript:void(0);"
                           class="vce-remove-module"><?php esc_html_e('Remove', THEME_SLUG); ?></a>
                    </div>

                    <div class="vce-module-form edit posts">
                        <input class="vce-module-deactivate vce-count-me" type="hidden" name="[active]" value="1"/>
                        <input class="vce-count-me" type="hidden" name="[type]" value="posts"/>
                        <?php vce_generate_module_field($module_posts_def, false, $module_posts_options); ?>
                    </div>
                </div>
            </div>

            <div class="blank">
                <div class="vce-module">
                    <div class="left">
                        <span class="vce-module-type"><?php _e('Blank', THEME_SLUG); ?></span>
                        <span class="vce-module-title"></span>
                    </div>

                    <div class="right">
                        <a href="javascript:void(0);"
                           class="vce-edit-module"><?php esc_html_e('Edit', THEME_SLUG); ?></a> |
                        <a href="javascript:void(0);"
                           class="vce-deactivate-module"><span class="vce-hidden"><?php esc_html_e('Activate', THEME_SLUG); ?></span><span><?php esc_html_e('Deactivate', THEME_SLUG); ?></span></a>
                        |
                        <a href="javascript:void(0);"
                           class="vce-remove-module"><?php esc_html_e('Remove', THEME_SLUG); ?></a>
                    </div>

                    <div class="vce-module-form edit blank">
                        <input class="vce-module-deactivate vce-count-me" type="hidden" name="[active]" value="1"/>
                        <input class="vce-count-me" type="hidden" name="[type]" value="blank"/>
                        <?php vce_generate_blank_module_field($module_text_def, false, array()); ?>
                    </div>
                </div>
            </div>

            <div class="category">
                <div class="vce-module">
                    <div class="left">
                        <span class="vce-module-type"><?php _e('Category', THEME_SLUG); ?></span>
                        <span class="vce-module-title"></span>
                    </div>

                    <div class="right">
                        <a href="javascript:void(0);" class="vce-edit-module"><?php esc_html_e('Edit', THEME_SLUG); ?></a> |
                        <a href="javascript:void(0);" class="vce-deactivate-module"><span class="vce-hidden"><?php esc_html_e('Activate', THEME_SLUG); ?></span><span><?php esc_html_e('Deactivate', THEME_SLUG); ?></span></a>
                        |
                        <a href="javascript:void(0);" class="vce-remove-module"><?php esc_html_e('Remove', THEME_SLUG); ?></a>
                    </div>

                    <div class="vce-module-form edit category">
                        <input class="vce-module-deactivate vce-count-me" type="hidden" name="[active]" value="1"/>
                        <input class="vce-count-me" type="hidden" name="[type]" value="category"/>
                        <?php vce_generate_cats_module_field($module_cat_def, false, $module_cat_options); ?>
                    </div>
                </div>
            </div>
            <?php foreach ($module_cpt_def as $cpt): ?>

                <div class="<?php echo esc_attr($cpt['type']) ?>">
                    <div class="vce-module">
                        <div class="left">
                            <span class="vce-module-type"><?php echo ucfirst($cpt['type_name']) ?></span>
                            <span class="vce-module-title"></span>
                        </div>

                        <div class="right">
                            <a href="javascript:void(0);"
                               class="vce-edit-module"><?php esc_html_e('Edit', THEME_SLUG); ?></a> |
                            <a href="javascript:void(0);"
                               class="vce-deactivate-module"><span class="vce-hidden"><?php esc_html_e('Activate', THEME_SLUG); ?></span><span><?php esc_html_e('Deactivate', THEME_SLUG); ?></span></a>
                            |
                            <a href="javascript:void(0);"
                               class="vce-remove-module"><?php esc_html_e('Remove', THEME_SLUG); ?></a>
                        </div>

                        <div class="vce-module-form edit <?php echo esc_attr($cpt['type']) ?>">
                            <input class="vce-module-deactivate vce-count-me" type="hidden" name="[active]" value="1"/>
                            <input class="vce-count-me" type="hidden" name="[type]" value="<?php echo esc_attr($cpt['type']) ?>"/>
                            <?php vce_generate_cpt_module_field($module_cpt_def[$cpt['type']], false, $module_cpt_options[$cpt['type']]); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

        <?php
    }
endif;


/* Create Authors Metabox */
if (!function_exists('vce_authors_metabox')) :

    function vce_authors_metabox($object, $box)
    {
        $vce_meta = vce_get_page_meta($object->ID);

        //print_r($vce_meta['authors']);

        $order_opts = array(
            'post_count' => 'Post Count',
            'user_login' => 'Username',
            'display_name' => 'Display Name',
            'user_registered' => 'Registered Date'
        );

        ?>

        <p><strong><?php _e('Order by', THEME_SLUG); ?></strong></p>
        <?php foreach ($order_opts as $order_value => $order_name) : ?>
        <?php $checked = ($vce_meta['authors']['orderby'] == $order_value) ? 'checked' : ''; ?>
        <input type="radio" name="vce[authors][orderby]" value="<?php echo $order_value; ?>" <?php echo $checked; ?>>
        <label for="vce[authors][orderby]"><?php echo $order_name; ?></label><br/>

    <?php endforeach; ?>

        <p><strong><?php _e('Order', THEME_SLUG); ?></strong></p>
        <select name="vce[authors][order]">
            <option value="DESC" <?php selected($vce_meta['authors']['order'], 'DESC'); ?>>Descending</option>
            <option value="ASC" <?php selected($vce_meta['authors']['order'], 'ASC'); ?>>Ascending</option>
        </select>

        <p><strong><?php _e('Exclude authors', THEME_SLUG); ?></strong></p>
        <input type="text" name="vce[authors][exclude]" value="<?php echo $vce_meta['authors']['exclude']; ?>"><br>
        <small><?php _e('Enter author IDs separated by comma'); ?></small>

        <?php

    }

endif;


/* Create Content metabox */
if (!function_exists('vce_page_content_metabox')) :
    function vce_page_content_metabox($object, $box)
    {
        $vce_meta = vce_get_page_meta($object->ID);
        ?>
        <p><strong><?php _e('Display page content:', THEME_SLUG); ?></strong></p>

        <label><input type="radio" name="vce[display_content][position]"
                      value="up" <?php checked('up', $vce_meta['display_content']['position']); ?>/> <?php _e('Above modules', THEME_SLUG); ?>
        </label><br/>
        <label><input type="radio" name="vce[display_content][position]"
                      value="down" <?php checked('down', $vce_meta['display_content']['position']); ?>/> <?php _e('Below modules', THEME_SLUG); ?>
        </label><br/>
        <label><input type="radio" name="vce[display_content][position]"
                      value="0" <?php checked('0', $vce_meta['display_content']['position']); ?>/> <?php _e('Do not display', THEME_SLUG); ?>
        </label><br/><br/>

        <p><strong><?php _e('Style:', THEME_SLUG); ?></strong></p>

        <label><input type="radio" name="vce[display_content][style]"
                      value="wrap" <?php checked('wrap', $vce_meta['display_content']['style']); ?>/> <?php _e('Wrapped in box', THEME_SLUG); ?>
        </label><br/>
        <label><input type="radio" name="vce[display_content][style]"
                      value="unwrap" <?php checked('unwrap', $vce_meta['display_content']['style']); ?>/> <?php _e('Unwrapped (transparent background)', THEME_SLUG); ?>
        </label><br/>

        <p><strong><?php _e('Width:', THEME_SLUG); ?></strong></p>

        <label><input type="radio" name="vce[display_content][width]"
                      value="container" <?php checked('container', $vce_meta['display_content']['width']); ?>/> <?php _e('Container/page width', THEME_SLUG); ?>
        </label><br/>
        <label><input type="radio" name="vce[display_content][width]"
                      value="full" <?php checked('full', $vce_meta['display_content']['width']); ?>/> <?php _e('Full/browser width', THEME_SLUG); ?>
        </label><br/><br/>

        <p class="description"><?php _e('Manage display options for content/editor on this page', THEME_SLUG); ?></p>

        <?php
    }
endif;



/* Generate posts module form/field */
if ( !function_exists( 'vce_generate_module_field' ) ) :
    function vce_generate_module_field( $module, $i = false, $data ) {
        extract( $data );
        $name_prefix = ( $i === false ) ? '' : 'vce[modules][' . $i . ']';
        ?>
        <div class="vce-opt-tabs">
            <a class="active" href="javascript:void(0);"><?php _e( 'Appearance', THEME_SLUG ); ?></a>
            <a href="javascript:void(0);"><?php _e( 'Combine layouts', THEME_SLUG ); ?></a>
            <a href="javascript:void(0);"><?php _e( 'Post selection', THEME_SLUG ); ?></a>
            <a href="javascript:void(0);"><?php _e( 'Action', THEME_SLUG ); ?></a>
        </div>
        <div class="vce-tab first">
            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Title', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me mod-title" type="text" name="<?php echo $name_prefix; ?>[title]"
                           value="<?php echo esc_attr( $module['title'] ); ?>"/>
                    <label><input type="checkbox" class="vce-count-me"
                                  name="<?php echo $name_prefix; ?>[hide_title]"
                                  value="1" <?php checked( $module['hide_title'], 1 ); ?>/><?php _e( 'Do not display publicly', THEME_SLUG ); ?>
                    </label> <br/>
                    <small class="howto"><?php _e( 'Enter your module title', THEME_SLUG ); ?></small>

                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Title link', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me" type="text" name="<?php echo $name_prefix; ?>[title_link]"
                           value="<?php echo esc_attr( $module['title_link'] ); ?>"/><br/>
                    <small class="howto"><?php _e( 'Optionally, you can assign URL to title', THEME_SLUG ); ?></small>

                </div>
            </div>
            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose layout', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <ul class="vce-img-select-wrap">
                        <?php foreach ( $layouts as $id => $layout ): ?>
                            <li>
                                <?php $selected_class = vce_compare( $id, $module['layout'] ) ? ' selected' : ''; ?>
                                <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                     class="vce-img-select<?php echo $selected_class; ?>">
                                <br/><span><?php echo $layout['title']; ?></span>
                                <input type="radio" class="vce-hidden vce-count-me"
                                       name="<?php echo $name_prefix; ?>[layout]"
                                       value="<?php echo $id; ?>" <?php checked( $id, $module['layout'] ); ?>/>

                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <small class="howto"><?php _e( 'Choose your main posts layout', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Max number of posts', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me" type="text" name="<?php echo esc_attr( $name_prefix ); ?>[limit]"
                           value="<?php echo esc_attr( $module['limit'] ); ?>"/><br/>
                    <small class="howto"><?php _e( 'Specify maximum number of posts for this module', THEME_SLUG ); ?></small>
                    <br>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Make this module one-column (half width)', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <label><input type="checkbox" name="<?php echo $name_prefix; ?>[one_column]" value="1"
                                  class="vce-count-me mod-columns" <?php checked( $module['one_column'], 1 ); ?>/><strong></strong></label><br/>
                    <small class="howto"><?php _e( 'This option may apply to layouts C, D and F which are naturally listed in two columns', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Custom CSS class', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me" type="text" name="<?php echo $name_prefix; ?>[css_class]"
                           value="<?php echo esc_attr( $module['css_class'] ); ?>"/><br/>
                    <small class="howto"><?php _e( 'Specify class name for a possibility to apply custom styling to this module using CSS (i.e. my-custom-module)', THEME_SLUG ); ?></small>
                </div>
            </div>
        </div>

        <div class="vce-tab">

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose starter posts layout', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <ul class="vce-img-select-wrap next-hide">
                        <?php foreach ( $starter_layouts as $id => $layout ): ?>
                            <li>
                                <?php $selected_class = vce_compare( $module['top_layout'], $id ) ? ' selected' : ''; ?>
                                <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                     class="vce-img-select<?php echo $selected_class; ?>">
                                <br/><span><?php echo $layout['title']; ?></span>
                                <input type="radio" class="vce-hidden vce-count-me"
                                       name="<?php echo $name_prefix; ?>[top_layout]"
                                       value="<?php echo $id; ?>" <?php checked( $id, $module['top_layout'] ); ?>/>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <?php $style = !$module['top_layout'] ? 'style="display:none"' : ''; ?>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Number of starter posts', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input type="text" name="<?php echo $name_prefix; ?>[top_limit]"
                           value="<?php echo $module['top_limit']; ?>" class="vce-count-me"
                           style="width: 30px;"/>
                    <p class="howto"><?php _e( 'Choose additional layout if you want to combine two layouts in same module so the first posts will be displayed in different layout', THEME_SLUG ); ?></p>
                </div>
            </div>
        </div>

        <div class="vce-tab">
            <?php if ( !empty( $cats ) ): ?>
                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php _e( 'Filter by category', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <div class="vce-item-scroll">
                            <?php foreach ( $cats as $cat ) : ?>
                                <?php $checked = in_array( $cat->term_id, $module['cat'] ) ? 'checked="checked"' : ''; ?>
                                <label><input class="vce-count-me" type="checkbox"
                                              name="<?php echo $name_prefix; ?>[cat][]"
                                              value="<?php echo $cat->term_id ?>" <?php echo $checked; ?> /><?php echo $cat->name; ?>
                                </label><br/>
                            <?php endforeach; ?>
                        </div>
                        <small class="howto"><?php _e( 'Check whether you want to display posts from specific categories only. Or leave empty for all categories.', THEME_SLUG ); ?></small>
                        <br/>
                        <label><input type="checkbox" name="<?php echo $name_prefix; ?>[cat_child]"
                                      value="1"
                                      class="vce-count-me" <?php checked( $module['cat_child'], 1 ); ?>/><strong><?php _e( 'Apply child categories', THEME_SLUG ); ?></strong></label><br/>
                        <small class="howto"><?php _e( 'If parent category is selected, posts from child categories will be included automatically', THEME_SLUG ); ?></small>
                        <br/>
                        <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[cat_inc_exc]"
                                      value="in" <?php checked( $module['cat_inc_exc'], 'in' ); ?>
                                      class="vce-count-me"/><?php esc_html_e( 'Include', THEME_SLUG ) ?>
                        </label><br/>
                        <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[cat_inc_exc]"
                                      value="not_in" <?php checked( $module['cat_inc_exc'], 'not_in' ); ?>
                                      class="vce-count-me"/><?php esc_html_e( 'Exclude', THEME_SLUG ) ?>
                        </label><br/>
                        <small class="howto"><?php esc_html_e( 'Whether to include or exclude posts from selected categories', THEME_SLUG ); ?></small>
                        <br/>

                    </div>
                </div>

            <?php endif; ?>
            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Filter by tag', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input type="text" name="<?php echo esc_attr( $name_prefix ); ?>[tag]" value="<?php echo esc_attr($module['tag']); ?>" class="vce-count-me"/><br/>
                    <small class="howto"><?php esc_html_e( 'Specify one or more tags separated by comma. i.e. life, cooking, funny moments', THEME_SLUG ); ?></small>
                    <br>
                    <br>
                    <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[tag_inc_exc]"
                                  value="in" <?php checked( $module['tag_inc_exc'], 'in' ); ?>
                                  class="vce-count-me"/><?php esc_html_e( 'Include', THEME_SLUG ) ?>
                    </label><br/>
                    <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[tag_inc_exc]"
                                  value="not_in" <?php checked( $module['tag_inc_exc'], 'not_in' ); ?>
                                  class="vce-count-me"/><?php esc_html_e( 'Exclude', THEME_SLUG ) ?>
                    </label><br/>
                    <small class="howto"><?php esc_html_e( 'Whether to include or exclude posts from selected tags', THEME_SLUG ); ?></small>
                </div>
            </div>


            <!-- filter by author -->

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php esc_html_e( 'Filter by author (username)', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input type="text" name="<?php echo esc_attr( $name_prefix ); ?>[author]"
                           value="<?php echo esc_attr( vce_get_authors_username_by_id( $module['author'] ) ); ?>"
                           class="vce-count-me"
                    />
                    <br><br>
                    <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[author_inc_exc]"
                                  value="in" <?php checked( $module['author_inc_exc'], 'in' ); ?>
                                  class="vce-count-me"/><?php esc_html_e( 'Include', THEME_SLUG ) ?>
                    </label><br/>
                    <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[author_inc_exc]"
                                  value="not_in" <?php checked( $module['author_inc_exc'], 'not_in' ); ?>
                                  class="vce-count-me"/><?php esc_html_e( 'Exclude', THEME_SLUG ) ?>
                    </label><br/>
                    <small class="howto"><?php esc_html_e( 'Whether to include or exclude author posts', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php esc_html_e( 'Exclude by id', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php $ids = $module['exclude_by_id'] ? implode( ', ', $module['exclude_by_id'] ) : ''; ?>
                    <input type="text" name="<?php echo esc_attr( $name_prefix ); ?>[exclude_by_id]"
                           value="<?php echo esc_attr( $ids ); ?>" class="vce-count-me" style="width: 100%"/>
                    <small class="howto"><?php _e( 'Specify post ids separated by comma i.e. 213,32,12,45', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Not older than', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ( $time['from'] as $id => $title ) : ?>
                        <label><input type="radio" name="<?php echo $name_prefix; ?>[time]"
                                      value="<?php echo $id; ?>" <?php checked( $module['time'], $id ); ?>
                                      class="vce-count-me"/><?php echo $title; ?></label><br/>
                    <?php endforeach; ?>
                    <small class="howto"><?php _e( 'Display posts that are not older than some specific time', THEME_SLUG ); ?></small>
                </div>
            </div>
            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Older than', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ( $time['to'] as $id => $title ) : ?>
                        <label><input type="radio" name="<?php echo $name_prefix; ?>[timeto]"
                                      value="<?php echo $id; ?>" <?php checked( $module['timeto'], $id ); ?>
                                      class="vce-count-me"/><?php echo $title; ?></label><br/>
                    <?php endforeach; ?>
                    <small class="howto"><?php _e( 'Display posts that are older than some specific time', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Order posts by', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ( $order as $id => $title ) : ?>
                        <label><input type="radio" name="<?php echo $name_prefix; ?>[order]"
                                      value="<?php echo $id; ?>" <?php checked( $module['order'], $id ); ?>
                                      class="vce-count-me"/><?php echo $title; ?></label><br/>
                    <?php endforeach; ?>
                    <small class="howto"><?php _e( 'Specify posts ordering', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php esc_html_e( 'Sort', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[sort]"
                                  value="DESC" <?php checked( $module['sort'], 'DESC' ); ?>
                                  class="vce-count-me"/><?php esc_html_e( 'Descending', THEME_SLUG ) ?>
                    </label><br/>
                    <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[sort]"
                                  value="ASC" <?php checked( $module['sort'], 'ASC' ); ?>
                                  class="vce-count-me"/><?php esc_html_e( 'Ascending', THEME_SLUG ) ?>
                    </label><br/>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose posts (or pages) manually', THEME_SLUG ); ?>:
                    <?php $manualy_selected_posts = vce_get_manually_selected_posts( $module['manual'], $module['type'] ); ?>
                    <?php $manual = !empty( $manualy_selected_posts ) ? implode( ",", $module['manual'] ) : ''; ?>
                </div>
                <div class="vce-opt-content vce-live-search-opt">
                    <input type="text" class="widefat vce-live-search" placeholder="<?php esc_html_e( 'Type to search...', THEME_SLUG ); ?>" /><br/>
                    <input type="hidden" class="vce-count-me vce-live-search-hidden" data-type="<?php echo esc_attr($module['type']); ?>" name="<?php echo $name_prefix; ?>[manual];?>" value="<?php echo esc_attr($manual); ?>" />
                    <div class="vce-live-search-items tagchecklist">
                        <?php vce_display_manually_selected_posts($manualy_selected_posts); ?>
                    </div>
                </div>
            </div>
            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Do not duplicate (display only in this module)', THEME_SLUG ); ?>
                </div>

                <div class="vce-opt-content">
                    <label><input type="checkbox" class="vce-count-me"
                                  name="<?php echo $name_prefix; ?>[exclude]"
                                  value="1" <?php checked( $module['exclude'], 1 ) ?>/></label>
                    <br/>
                    <small class="howto"><?php _e( 'Check this option if you want posts in this module to be excluded from other modules so they don\'t appear twice', THEME_SLUG ); ?></small>
                </div>
            </div>
        </div>


        <div class="vce-tab">
            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose additional options', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ( $actions as $id => $title ) : ?>

                        <label><input type="radio" name="<?php echo $name_prefix; ?>[action]"
                                      value="<?php echo $id; ?>" <?php checked( (string)$module['action'], $id ); ?>
                                      class="vce-count-me vce-action-pick"/><?php echo $title; ?></label><br/>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php $style = vce_compare( $module['action'], 'pagination' ) ? '' : 'style="display:none"'; ?>
            <div class="vce-pagination-wrap hideable" <?php echo $style; ?>>
                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php _e( 'Choose pagination type', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <ul class="vce-img-select-wrap">
                            <?php foreach ( $paginations as $id => $pagination ): ?>
                                <li>
                                    <?php $selected_class = vce_compare( $module['pagination'], $id ) ? ' selected' : ''; ?>
                                    <img src="<?php echo $pagination['img']; ?>"
                                         title="<?php echo $pagination['title']; ?>"
                                         class="vce-img-select<?php echo $selected_class; ?>">
                                    <br/><span><?php echo $pagination['title']; ?></span>
                                    <input type="radio" class="vce-hidden vce-count-me"
                                           name="<?php echo $name_prefix; ?>[pagination]"
                                           value="<?php echo $id; ?>" <?php checked( $id, $module['pagination'] ); ?>/> </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <small class="howto"><?php _e( 'Note: Pagination can be added only for the last module on the page', THEME_SLUG ); ?></small>
                    </div>
                </div>
            </div>

            <?php $style = vce_compare( $module['action'], 'link' ) ? '' : 'style="display:none"'; ?>
            <div class="vce-link-wrap hideable" <?php echo $style; ?>>
                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php _e( 'Link/Button Text', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <input type="text" name="<?php echo $name_prefix; ?>[action_link_text]"
                               value="<?php echo esc_attr( $module['action_link_text'] ); ?>"
                               class="vce-count-me"/></p>
                        <p><strong><?php _e( 'Link/Button URL', THEME_SLUG ); ?>:</strong><br/>
                            <input type="text" name="<?php echo $name_prefix; ?>[action_link_url]"
                                   value="<?php echo esc_url( $module['action_link_url'] ); ?>"
                                   class="vce-count-me"/>
                        </p>
                    </div>
                </div>
            </div>

            <?php $style = vce_compare( $module['action'], 'slider' ) ? '' : 'style="display:none"'; ?>
            <div class="vce-slider-wrap hideable" <?php echo $style; ?>>
                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php _e( 'Autoplay slider posts', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <input type="text" name="<?php echo $name_prefix; ?>[autoplay]"
                               value="<?php echo esc_attr( $module['autoplay'] ); ?>" class="vce-count-me"/>
                        <br/>
                        <small class="howto"><?php _e( 'Specify number of seconds if you want to auto-slide posts, or leave empty for no autoplay', THEME_SLUG ); ?></small>
                        </p>

                    </div>
                </div>
            </div>


        </div>

        <?php
    }
endif;


/* Generate cpt module form/field */
if ( !function_exists( 'vce_generate_cpt_module_field' ) ) :
    function vce_generate_cpt_module_field( $module, $i = false, $data ) {

        extract( $data );
        $name_prefix = ( $i === false ) ? '' : 'vce[modules][' . $i . ']';
        $edit = ( $i === false ) ? '' : 'edit';
        ?>
        <div class="vce-opt-tabs">
            <a class="active" href="#"><?php _e( 'Appearance', THEME_SLUG ); ?></a>
            <a href="javascript:void(0)"><?php _e( 'Combine layouts', THEME_SLUG ); ?></a>
            <a href="javascript:void(0)"><?php _e( 'Post selection', THEME_SLUG ); ?></a>
            <a href="javascript:void(0)"><?php _e( 'Action', THEME_SLUG ); ?></a>
        </div>
        <div class="vce-tab first">

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Title', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me mod-title" type="text" name="<?php echo $name_prefix; ?>[title]"
                           value="<?php echo esc_attr( $module['title'] ); ?>"/>
                    &nbsp;<label><input type="checkbox" class="vce-count-me"
                                        name="<?php echo $name_prefix; ?>[hide_title]"
                                        value="1" <?php checked( $module['hide_title'], 1 ); ?>/><?php _e( 'Do not display publicly', THEME_SLUG ); ?>
                    </label> <br/>
                    <small class="howto"><?php _e( 'Enter your module title', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Title link', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me" type="text" name="<?php echo $name_prefix; ?>[title_link]"
                           value="<?php echo esc_attr( $module['title_link'] ); ?>"/><br/>
                    <small class="howto"><?php _e( 'Optionally, you can assign URL to title', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose layout', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <ul class="vce-img-select-wrap">
                        <?php foreach ( $layouts as $id => $layout ): ?>
                            <li>
                                <?php $selected_class = vce_compare( $id, $module['layout'] ) ? ' selected' : ''; ?>
                                <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                     class="vce-img-select<?php echo $selected_class; ?>">
                                <br/><span><?php echo $layout['title']; ?></span>
                                <input type="radio" class="vce-hidden vce-count-me"
                                       name="<?php echo $name_prefix; ?>[layout]"
                                       value="<?php echo $id; ?>" <?php checked( $id, $module['layout'] ); ?>/> </label>

                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <small class="howto"><?php _e( 'Choose your main posts layout', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Max number of posts', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me" type="text" name="<?php echo esc_attr( $name_prefix ); ?>[limit]"
                           value="<?php echo esc_attr( $module['limit'] ); ?>"/><br/>
                    <small class="howto"><?php _e( 'Specify maximum number of posts for this module', THEME_SLUG ); ?></small>
                </div>
            </div>


            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Make this module one-column (half width)', THEME_SLUG ); ?>
                </div>
                <div class="vce-opt-content">
                    <label><input type="checkbox" name="<?php echo $name_prefix; ?>[one_column]" value="1"
                                  class="vce-count-me mod-columns" <?php checked( $module['one_column'], 1 ); ?>/></label><br/>
                    <small class="howto"><?php _e( 'This option may apply to layouts C, D and F which are naturally listed in two columns', THEME_SLUG ); ?></small>
                </div>
            </div>


            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Custom CSS class', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me" type="text" name="<?php echo $name_prefix; ?>[css_class]"
                           value="<?php echo esc_attr( $module['css_class'] ); ?>"/><br/>
                    <small class="howto"><?php _e( 'Specify class name for a possibility to apply custom styling to this module using CSS (i.e. my-custom-module)', THEME_SLUG ); ?></small>
                </div>
            </div>

        </div>

        <div class="vce-tab">

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose starter posts layout', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">

                    <ul class="vce-img-select-wrap next-hide">
                        <?php foreach ( $starter_layouts as $id => $layout ): ?>
                            <li>
                                <?php $selected_class = vce_compare( $module['top_layout'], $id ) ? ' selected' : ''; ?>
                                <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                     class="vce-img-select<?php echo $selected_class; ?>">
                                <br/><span><?php echo $layout['title']; ?></span>
                                <input type="radio" class="vce-hidden vce-count-me"
                                       name="<?php echo $name_prefix; ?>[top_layout]"
                                       value="<?php echo $id; ?>" <?php checked( $id, $module['top_layout'] ); ?>/> </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>
            <?php $style = !$module['top_layout'] ? 'style="display:none"' : ''; ?>
            <div class="vce-opt" <?php echo $style; ?>>
                <div class="vce-opt-title">
                    <?php _e( 'Number of starter posts', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input type="text" name="<?php echo $name_prefix; ?>[top_limit]"
                           value="<?php echo $module['top_limit']; ?>" class="vce-count-me"
                           style="width: 30px;"/>
                    <p class="howto"><?php _e( 'Choose additional layout if you want to combine two layouts in same module so the first posts will be displayed in different layout', THEME_SLUG ); ?></p>
                </div>
            </div>
        </div>


        <div class="vce-tab">

            <!-- CPT taxonomies -->

            <?php if ( !empty( $taxonomies ) ): ?>
                <div class="vce-opt">
                    <?php foreach ( $taxonomies as $taxonomy ) : ?>
                        <div class="vce-opt-title">
                            <?php esc_html_e( 'In ', THEME_SLUG ); ?><?php echo $taxonomy['name']; ?>:
                        </div>
                        <div class="vce-opt-content">

                            <?php if ( $taxonomy['hierarchical'] ) : ?>

                                <div class="vce-fit-height">
                                    <?php foreach ( $taxonomy['terms'] as $term ) : ?>
                                        <?php $tax = !empty( $module['tax'][$taxonomy['id']] ) ? $module['tax'][$taxonomy['id']] : array(); ?>
                                        <?php $checked = in_array( $term->term_id, $tax ) ? 'checked="checked"' : ''; ?>
                                        <label><input class="vce-count-me" type="checkbox"
                                                      name="<?php echo esc_attr( $name_prefix ); ?>[tax][<?php echo esc_attr( $taxonomy['id'] ); ?>][]"
                                                      value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo $checked; ?> /><?php echo $term->name; ?>
                                        </label><br/>
                                    <?php endforeach; ?>
                                </div>
                                <small class="howto"><?php esc_html_e( 'Check whether you want to display posts from specific', THEME_SLUG ); ?><?php echo $taxonomy['name']; ?></small>
                            <?php else: ?>

                                <?php $tax = !empty( $module['tax'][$taxonomy['id']] ) ? vce_get_tax_term_name_by_id( $module['tax'][$taxonomy['id']], $taxonomy['id'] ) : '' ?>
                                <input type="text"
                                       name="<?php echo esc_attr( $name_prefix ); ?>[tax][<?php echo esc_attr( $taxonomy['id'] ); ?>]"
                                       value="<?php echo esc_attr( $tax ); ?>" class="vce-count-me"/><br/>
                                <small class="howto"><?php esc_html_e( 'Specify one or more terms separated by comma. i.e. life, cooking, funny moments', THEME_SLUG ); ?></small>
                            <?php endif; ?>

                            <br/>
                            <label><input type="radio"
                                          name="<?php echo esc_attr( $name_prefix ); ?>[<?php echo esc_attr( $taxonomy['id'] ) ?>_inc_exc]"
                                          value="in" <?php checked( $module[esc_attr( $taxonomy['id'] ) . '_inc_exc'], 'in' ); ?>
                                          class="vce-count-me"/><?php esc_html_e( 'Include', THEME_SLUG ) ?>
                            </label><br/>
                            <label><input type="radio"
                                          name="<?php echo esc_attr( $name_prefix ); ?>[<?php echo esc_attr( $taxonomy['id'] ) ?>_inc_exc]"
                                          value="not_in" <?php checked( $module[esc_attr( $taxonomy['id'] ) . '_inc_exc'], 'not_in' ); ?>
                                          class="vce-count-me"/><?php esc_html_e( 'Exclude', THEME_SLUG ) ?>
                            </label><br/>
                            <br/>
                        </div>

                    <?php endforeach; ?>
                </div> <!-- vce-opt-item -->
                       <!-- filter by author -->

                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php esc_html_e( 'Filter by author (username)', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <input type="text" 
                            name="<?php echo esc_attr( $name_prefix ); ?>[author]"
                            value="<?php echo esc_attr( vce_get_authors_username_by_id( $module['author'] ) ); ?>"
                            class="vce-count-me"
                        />
                        <br><br>
                        <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[author_inc_exc]"
                                      value="in" <?php checked( $module['author_inc_exc'], 'in' ); ?>
                                      class="vce-count-me"/><?php esc_html_e( 'Include', THEME_SLUG ) ?>
                        </label><br/>
                        <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[author_inc_exc]"
                                      value="not_in" <?php checked( $module['author_inc_exc'], 'not_in' ); ?>
                                      class="vce-count-me"/><?php esc_html_e( 'Exclude', THEME_SLUG ) ?>
                        </label><br/>
                        <small class="howto"><?php esc_html_e( 'Whether to include or exclude author posts', THEME_SLUG ); ?></small>

                    </div>
                </div>
                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php esc_html_e( 'Exclude by id', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <?php $ids = $module['exclude_by_id'] ? implode( ', ', $module['exclude_by_id'] ) : ''; ?>
                        <input type="text" name="<?php echo esc_attr( $name_prefix ); ?>[exclude_by_id]"
                               value="<?php echo esc_attr( $ids ); ?>" class="vce-count-me" style="width: 100%"/>
                        <small class="howto"><?php _e( 'Specify post ids separated by comma i.e. 213,32,12,45', THEME_SLUG ); ?></small>
                    </div>
                </div>
            <?php endif; ?>


            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Not older than', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ( $time['from'] as $id => $title ) : ?>
                        <label><input type="radio" name="<?php echo $name_prefix; ?>[time]"
                                      value="<?php echo $id; ?>" <?php checked( $module['time'], $id ); ?>
                                      class="vce-count-me"/><?php echo $title; ?></label><br/>
                    <?php endforeach; ?>
                    <small class="howto"><?php _e( 'Display posts that are not older than some specific time', THEME_SLUG ); ?></small>

                </div>
            </div>
            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Older than', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ( $time['to'] as $id => $title ) : ?>
                        <label><input type="radio" name="<?php echo $name_prefix; ?>[timeto]"
                                      value="<?php echo $id; ?>" <?php checked( $module['timeto'], $id ); ?>
                                      class="vce-count-me"/><?php echo $title; ?></label><br/>
                    <?php endforeach; ?>
                    <small class="howto"><?php _e( 'Display posts that are older than some specific time', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Order posts by', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ( $order as $id => $title ) : ?>
                        <label><input type="radio" name="<?php echo $name_prefix; ?>[order]"
                                      value="<?php echo $id; ?>" <?php checked( $module['order'], $id ); ?>
                                      class="vce-count-me"/><?php echo $title; ?></label><br/>
                    <?php endforeach; ?>
                    <small class="howto"><?php _e( 'Specify posts ordering', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php esc_html_e( 'Sort', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[sort]"
                                  value="DESC" <?php checked( $module['sort'], 'DESC' ); ?>
                                  class="vce-count-me"/><?php esc_html_e( 'Descending', THEME_SLUG ) ?>
                    </label><br/>
                    <label><input type="radio" name="<?php echo esc_attr( $name_prefix ); ?>[sort]"
                                  value="ASC" <?php checked( $module['sort'], 'ASC' ); ?>
                                  class="vce-count-me"/><?php esc_html_e( 'Ascending', THEME_SLUG ) ?>
                    </label><br/>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose manually', THEME_SLUG ); ?>:
                    <?php $manualy_selected_posts = vce_get_manually_selected_posts( $module['manual'], $module['type'] ); ?>
                    <?php $manual = !empty( $manualy_selected_posts ) ? implode( ",", $module['manual'] ) : ''; ?>
                </div>
                <div class="vce-opt-content vce-live-search-opt">
                    <input type="text" class="widefat vce-live-search" placeholder="<?php esc_html_e( 'Type to search...', THEME_SLUG ); ?>" /><br/>
                    <input type="hidden" class="vce-count-me vce-live-search-hidden" data-type="<?php echo esc_attr($module['type']); ?>" name="<?php echo $name_prefix; ?>[manual];?>" value="<?php echo esc_attr($manual); ?>" />
                    <div class="vce-live-search-items tagchecklist">
                        <?php vce_display_manually_selected_posts($manualy_selected_posts); ?>
                    </div>
                </div>
            </div>


            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Do not duplicate (display only in this module)', THEME_SLUG ); ?>
                </div>
                <div class="vce-opt-content">
                    <input type="checkbox" class="vce-count-me"
                           name="<?php echo $name_prefix; ?>[exclude]"
                           value="1" <?php checked( $module['exclude'], 1 ) ?>/>
                    <br/>
                    <small class="howto"><?php _e( 'Check this option if you want posts in this module to be excluded from other modules so they don\'t appear twice', THEME_SLUG ); ?></small>
                </div>
            </div>

        </div> <!-- end vce-tab -->

        <div class="vce-tab">
            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose additional options', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ( $actions as $id => $title ) : ?>
                        <label><input type="radio" name="<?php echo $name_prefix; ?>[action]"
                                      value="<?php echo $id; ?>" <?php checked( $module['action'], $id ); ?>
                                      class="vce-count-me vce-action-pick"/><?php echo $title; ?></label><br/>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php $style = vce_compare( $module['action'], 'pagination' ) ? '' : 'style="display:none"'; ?>
            <div class="vce-pagination-wrap hideable" <?php echo $style; ?>>
                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php _e( 'Choose pagination type', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <ul class="vce-img-select-wrap">
                            <?php foreach ( $paginations as $id => $pagination ): ?>
                                <li>
                                    <?php $selected_class = vce_compare( $module['pagination'], $id ) ? ' selected' : ''; ?>
                                    <img src="<?php echo $pagination['img']; ?>"
                                         title="<?php echo $pagination['title']; ?>"
                                         class="vce-img-select<?php echo $selected_class; ?>">
                                    <br/><span><?php echo $pagination['title']; ?></span>
                                    <input type="radio" class="vce-hidden vce-count-me"
                                           name="<?php echo $name_prefix; ?>[pagination]"
                                           value="<?php echo $id; ?>" <?php checked( $id, $module['pagination'] ); ?>/>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <small class="howto"><?php _e( 'Note: Pagination can be added only for the last module on the page', THEME_SLUG ); ?></small>
                    </div>
                </div>
            </div>

            <?php $style = vce_compare( $module['action'], 'link' ) ? '' : 'style="display:none"'; ?>
            <div class="vce-link-wrap hideable" <?php echo $style; ?>>

                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php _e( 'Link/Button Text', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <input type="text" name="<?php echo $name_prefix; ?>[action_link_text]"
                               value="<?php echo esc_attr( $module['action_link_text'] ); ?>"
                               class="vce-count-me"/></p>
                        <p><strong><?php _e( 'Link/Button URL', THEME_SLUG ); ?>:</strong><br/>
                            <input type="text" name="<?php echo $name_prefix; ?>[action_link_url]"
                                   value="<?php echo esc_url( $module['action_link_url'] ); ?>"
                                   class="vce-count-me"/>
                        </p>
                    </div>
                </div>
            </div>

            <?php $style = vce_compare( $module['action'], 'slider' ) ? '' : 'style="display:none"'; ?>
            <div class="vce-slider-wrap hideable" <?php echo $style; ?>>

                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php _e( 'Autoplay slider posts', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <input type="text" name="<?php echo $name_prefix; ?>[autoplay]"
                               value="<?php echo esc_attr( $module['autoplay'] ); ?>" class="vce-count-me"/>
                        <br/>
                        <small class="howto"><?php _e( 'Specify number of seconds if you want to auto-slide posts, or leave empty for no autoplay', THEME_SLUG ); ?></small>

                    </div>
                </div>

            </div>

            <input class="vce-count-me" type="hidden" name="<?php echo $name_prefix; ?>[cpt]" value="1"/>
        </div>


        <?php
    }
endif;


/* Generate blank module form/field */
if ( !function_exists( 'vce_generate_blank_module_field' ) ) :
    function vce_generate_blank_module_field( $module, $i = false, $data ) {
        extract( $data );
        $name_prefix = ( $i === false ) ? '' : 'vce[modules][' . $i . ']';
        ?>

        <div class="vce-opt">
            <div class="vce-opt-title">
                <?php _e( 'Title', THEME_SLUG ); ?>:
            </div>
            <div class="vce-opt-content">
                <input class="vce-count-me mod-title" type="text" name="<?php echo $name_prefix; ?>[title]"
                       value="<?php echo esc_attr( $module['title'] ); ?>"/>
                &nbsp;<label><input type="checkbox" class="vce-count-me"
                                    name="<?php echo $name_prefix; ?>[hide_title]"
                                    value="1" <?php checked( $module['hide_title'], 1 ); ?>/><?php _e( 'Do not display publicly', THEME_SLUG ); ?>
                </label> <br/>
                <small class="howto"><?php _e( 'Enter your module title', THEME_SLUG ); ?></small>
            </div>
        </div>
        <div class="vce-opt">
            <div class="vce-opt-title">
                <?php _e( 'Title link', THEME_SLUG ); ?>:
            </div>
            <div class="vce-opt-content">
                <input class="vce-count-me" type="text" name="<?php echo $name_prefix; ?>[title_link]"
                       value="<?php echo esc_attr( $module['title_link'] ); ?>"/><br/>
                <small class="howto"><?php _e( 'Optionally, you can assign URL to title', THEME_SLUG ); ?></small>
            </div>
        </div>
        <div class="vce-opt">
            <div class="vce-opt-title">
                <?php _e( 'Content', THEME_SLUG ); ?>:
            </div>
            <div class="vce-opt-content">
                <textarea class="vce-count-me" name="<?php echo $name_prefix; ?>[content]"
                          style="width: 100%; height: 200px;"><?php echo esc_textarea( $module['content'] ); ?></textarea>
                <small class="howto"><?php _e( 'You can put any text, HTML, JavaScript or shortcodes here', THEME_SLUG ); ?></small>
            </div>
        </div>

        <div class="vce-opt">
            <div class="vce-opt-title">
                <?php _e( 'Make this module one-column (half width)', THEME_SLUG ); ?>
            </div>
            <div class="vce-opt-content">
                <label><input type="checkbox" name="<?php echo $name_prefix; ?>[one_column]" value="1"
                              class="vce-count-me mod-columns" <?php checked( $module['one_column'], 1 ); ?>/></label><br/>
            </div>
        </div>

        <div class="vce-opt">
            <div class="vce-opt-title">
                <?php _e( 'Custom CSS class', THEME_SLUG ); ?>:
            </div>
            <div class="vce-opt-content">
                <input class="vce-count-me" type="text" name="<?php echo $name_prefix; ?>[css_class]"
                       value="<?php echo esc_attr( $module['css_class'] ); ?>"/><br/>
                <small class="howto"><?php _e( 'Specify class name for a possibility to apply custom styling to this module using CSS (i.e. my-custom-module)', THEME_SLUG ); ?></small>
            </div>
        </div>

        <?php
    }
endif;


/* Generate category module */
if ( !function_exists( 'vce_generate_cats_module_field' ) ) :
    function vce_generate_cats_module_field( $module, $i = false, $data ) {

        extract( $data );

        $name_prefix = ( $i === false ) ? '' : 'vce[modules][' . $i . ']';
        $edit = ( $i === false ) ? '' : 'edit';

        ?>

        <div class="vce-opt-tabs">
            <a class="active" href="javascript:void(0)"><?php _e( 'Appearance', THEME_SLUG ); ?></a>
            <a href="javascript:void(0)"><?php _e( 'Category selection', THEME_SLUG ); ?></a>
            <a href="javascript:void(0)"><?php _e( 'Action', THEME_SLUG ); ?></a>
        </div>

        <div class="vce-tab first">

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Title', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me mod-title" type="text" name="<?php echo $name_prefix; ?>[title]"
                           value="<?php echo esc_attr( $module['title'] ); ?>"/>
                    &nbsp;<label><input type="checkbox" class="vce-count-me"
                                        name="<?php echo $name_prefix; ?>[hide_title]"
                                        value="1" <?php checked( $module['hide_title'], 1 ); ?>/><?php _e( 'Do not display publicly', THEME_SLUG ); ?>
                    </label> <br/>
                    <small class="howto"><?php _e( 'Enter your module title', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Title link', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me" type="text" name="<?php echo $name_prefix; ?>[title_link]"
                           value="<?php echo esc_attr( $module['title_link'] ); ?>"/><br/>
                    <small class="howto"><?php _e( 'Optionally, you can assign URL to title', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose layout', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <ul class="vce-img-select-wrap">
                        <?php foreach ( $layouts as $id => $layout ): ?>
                            <li>
                                <?php $selected_class = vce_compare( $id, $module['layout'] ) ? ' selected' : ''; ?>
                                <img src="<?php echo $layout['img']; ?>" title="<?php echo $layout['title']; ?>"
                                     class="vce-img-select<?php echo $selected_class; ?>">
                                <br/><span><?php echo $layout['title']; ?></span>
                                <input type="radio" class="vce-hidden vce-count-me"
                                       name="<?php echo $name_prefix; ?>[layout]"
                                       value="<?php echo $id; ?>" <?php checked( $id, $module['layout'] ); ?>/>

                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <small class="howto"><?php _e( 'Choose your category layout', THEME_SLUG ); ?></small>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Display posts count', THEME_SLUG ); ?>?
                </div>
                <div class="vce-opt-content">
                    <input type="hidden" name="<?php echo esc_attr( $name_prefix ); ?>[display_count]" value="0"
                           class="vce-count-me"/>
                    <label><input type="checkbox" name="<?php echo esc_attr( $name_prefix ); ?>[display_count]"
                                  value="1" <?php checked( $module['display_count'], 1 ); ?>
                                  class="vce-count-me vce-next-hide"/>
                    </label>
                </div>
            </div>

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Count label', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input type="text" name="<?php echo esc_attr( $name_prefix ); ?>[count_label]"
                           value="<?php echo esc_attr( $module['count_label'] ); ?>" class="vce-count-me"/>
                </div>
            </div>


            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Custom CSS class', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <input class="vce-count-me" type="text" name="<?php echo $name_prefix; ?>[css_class]"
                           value="<?php echo esc_attr( $module['css_class'] ); ?>"/><br/>
                    <small class="howto"><?php _e( 'Specify class name for a possibility to apply custom styling to this module using CSS (i.e. my-custom-module)', THEME_SLUG ); ?></small>
                </div>
            </div>
        </div> <!-- end tab -->

        <div class="vce-tab">

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Categories', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php $cats = vce_sort_option_items( $cats, $module['cat'] ); ?>
                    <ul class="vce-sortable-items sortable">
                        <?php foreach ( $cats as $cat ) : ?>
                            <?php $checked = in_array( $cat->term_id, $module['cat'] ) ? 'checked="checked"' : ''; ?>
                            <li>
                                <label>
                                    <input class="vce-count-me" type="checkbox"
                                           name="<?php echo esc_attr( $name_prefix ); ?>[cat][]"
                                           value="<?php echo esc_attr( $cat->term_id ); ?>" <?php echo esc_attr( $checked ); ?> /><?php echo $cat->name; ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <br/>
                    <small class="howto"><?php _e( 'Note: You can reorder categories, just click hold and drag it up or down.', THEME_SLUG ); ?></small>
                </div>
            </div>

        </div> <!-- end tab -->

        <div class="vce-tab">

            <div class="vce-opt">
                <div class="vce-opt-title">
                    <?php _e( 'Choose additional options', THEME_SLUG ); ?>:
                </div>
                <div class="vce-opt-content">
                    <?php foreach ( $actions as $id => $title ) : ?>
                        <label><input type="radio" name="<?php echo $name_prefix; ?>[action]"
                                      value="<?php echo $id; ?>" <?php checked( $module['action'], $id ); ?>
                                      class="vce-count-me vce-action-pick"/><?php echo $title; ?></label><br/>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php $style = vce_compare( $module['action'], 'slider' ) ? '' : 'style="display:none"'; ?>
            <div class="vce-slider-wrap hideable" <?php echo $style; ?>>

                <div class="vce-opt">
                    <div class="vce-opt-title">
                        <?php _e( 'Autoplay slider posts', THEME_SLUG ); ?>:
                    </div>
                    <div class="vce-opt-content">
                        <input type="text" name="<?php echo $name_prefix; ?>[autoplay]"
                               value="<?php echo esc_attr( $module['autoplay'] ); ?>" class="vce-count-me"/>
                        <br/>
                        <small class="howto"><?php _e( 'Specify number of seconds if you want to auto-slide posts, or leave empty for no autoplay', THEME_SLUG ); ?></small>
                    </div>
                </div>
            </div>
        </div>

    <?php }
endif;
