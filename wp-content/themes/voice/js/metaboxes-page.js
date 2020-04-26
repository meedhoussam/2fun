(function($) {
    $(document).ready(function() {

        /* Image opts selection */
        $('body').on('click', 'img.vce-img-select', function(e) {
            e.preventDefault();
            $(this).closest('ul').find('img.vce-img-select').removeClass('selected');
            $(this).addClass('selected');
            $(this).closest('ul').find('input').removeAttr('checked');
            $(this).closest('li').find('input').attr('checked', 'checked');

            if ($(this).closest('ul').hasClass('next-hide')) {
                var v = $(this).closest('li').find('input:checked').val();

                if (v == 'inherit' || v == 0) {
                    $(this).closest('.vce-opt').next().fadeOut(400);
                } else {
                    $(this).closest('.vce-opt').next().fadeIn(400);
                }
            }
        });

        if (!vce_js_settings.is_gutenberg) {
            tagBox.init();
        }

        /* Dynamicaly apply select value */
        $('body').on('change', '.vce-opt-select', function(e) {
            //e.preventDefault();
            var sel = $(this).val();
            $(this).find('option').removeAttr('selected');
            $(this).find('option[value=' + sel + ']').attr('selected', 'selected');
        });

        /* Module form tabs */
        $('body').on('click', '.vce-opt-tabs a', function(e) {
            e.preventDefault();
            $(this).parent().find('a').removeClass('active');
            $(this).addClass('active');
            $(this).closest('.vce-module-form').find('.vce-tab').hide();
            $(this).closest('.vce-module-form').find('.vce-tab').eq($(this).index()).show();

        });

        var vce_current_module;
        var vce_module_type;


        /* Add new module */
        $('body').on('click', '.vce-add-module', function(e) {
            e.preventDefault();
            vce_module_type = $(this).attr('data-type');
            var $modal = $($.parseHTML('<div class="vce-module-form">' + $('#vce-module-clone .' + vce_module_type + ' .vce-module-form').html() + '</div>'));
            vce_dialog($modal, 'Add New Module', 'vce-save-module');

            /* Make some options sortable */
            vce_sort_items($(".vce-opt-content .sortable"));
            vce_sort_searched_items();

        });

        /* Edit module */
        $('body').on('click', '.vce-edit-module', function(e) {
            e.preventDefault();
            vce_current_module = parseInt($(this).closest('.vce-module').attr('data-module'));
            var $modal = $(this).closest('.vce-module').find('.vce-module-form').clone();

            vce_dialog($modal, 'Edit Module', 'vce-save-module');

            /* Make some options sortable */
            vce_sort_items($(".vce-opt-content .sortable"));
            vce_sort_searched_items();

        });

        /* Remove module */
        $('body').on('click', '.vce-remove-module', function(e) {
            e.preventDefault();
            var remove = vce_confirm();
            if (remove) {
                $(this).closest('.vce-module').fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });

        /* Deactivate/Activate module */
        $('body').on('click', '.vce-deactivate-module', function(e) {
            e.preventDefault();
            var _self = $(this);
            var parent_el = _self.closest('.vce-module');
            var h_data = parent_el.find('.vce-module-deactivate').val();

            _self.find('span').toggleClass('vce-hidden');

            if (h_data == 1) {
                parent_el.find('.vce-module-deactivate').val('0');
                parent_el.addClass('vce-module-disabled');
            } else {
                parent_el.find('.vce-module-deactivate').val('1');
                parent_el.removeClass('vce-module-disabled');
            }

        });

        /* Save module */

        $('body').on('click', 'button.vce-save-module', function(e) {

            e.preventDefault();

            var $vce_form = $(this).closest('.wp-dialog').find('.vce-module-form').clone();

            /* Nah, jQuery clone bug, clone text area manually */
            var txt_content = $(this).closest('.wp-dialog').find('.vce-module-form').find("textarea").first().val();
            if (txt_content !== undefined) {
                $vce_form.find("textarea").first().val(txt_content);
            }

            if ($vce_form.hasClass('edit')) {
                $vce_form = vce_fill_form_fields($vce_form);
                var $module = $('.vce-module-' + vce_current_module);
                $module.find('.vce-module-form').html($vce_form.html());
                $module.find('.vce-module-title').text($vce_form.find('.mod-title').val());
            } else {
                var count = $('.vce-modules-count').attr('data-count');
                $vce_form = vce_fill_form_fields($vce_form, 'vce[modules][' + count + ']');
                $('.vce-modules').append($('#vce-module-clone .' + vce_module_type).html());
                var $new_module = $('.vce-modules .vce-module').last();
                $new_module.addClass('vce-module-' + parseInt(count)).attr('data-module', parseInt(count)).find('.vce-module-form').addClass('edit').html($vce_form.html());
                $new_module.find('.vce-module-title').text($vce_form.find('.mod-title').val());
                $('.vce-modules-count').attr('data-count', parseInt(count) + 1);
            }

        });

        /* Open our dialog modal */
        function vce_dialog(obj, title, action) {

            obj.dialog({
                'dialogClass': 'wp-dialog',
                'appendTo': false,
                'modal': true,
                'autoOpen': false,
                'closeOnEscape': true,
                'draggable': false,
                'resizable': false,
                'width': 800,
                'height': $(window).height() - 60,
                'title': title,
                'close': function(event, ui) {
                    $('body').removeClass('modal-open');
                },
                'buttons': [{
                    'text': "Save",
                    'class': 'button-primary ' + action,
                    'click': function() {
                        $(this).dialog('close');
                    }
                }]
            });

            obj.dialog('open');

            $('body').addClass('modal-open');
        }


        /* Fill form fields dynamically */
        function vce_fill_form_fields($obj, name) {

            $obj.find('.vce-count-me').each(function(index) {


                if (name !== undefined && !$(this).is('option')) {
                    $(this).attr('name', name + $(this).attr('name'));
                }

                if ($(this).is('textarea')) {
                    $(this).html($(this).val());
                }


                if (!$(this).is('select')) {
                    $(this).attr('value', $(this).val());
                }



                if ($(this).is(":checked")) {
                    $(this).attr('checked', 'checked');
                } else {
                    $(this).removeAttr('checked');
                }

            });

            return $obj;
        }

        function vce_confirm() {
            var ret_val = confirm("Are you sure?");
            return ret_val;
        }


        /* Make modules sortable */
        $(".vce-modules").sortable({
            revert: false,
            cursor: "move",
            placeholder: "vce-module-drop"
        });

        vce_template_metaboxes();

        $('#page_template').change(function(e) {
            vce_template_metaboxes(true);
        });

        $('body').on('change', '#template-selector-0', function(e) {
            vce_template_metaboxes(true);
        });


        /* Metabox switch - do not show every metabox for every template */

        var vce_is_gutenberg = vce_js_settings.is_gutenberg && typeof wp.apiFetch !== 'undefined';

        var vce_template_selector = vce_is_gutenberg ? '.editor-page-attributes__template select' : '#page_template';

        if (vce_is_gutenberg) {

            var post_id = $('#post_ID').val();
            wp.apiFetch({ path: '/wp/v2/pages/' + post_id, method: 'GET' }).then(function(obj) {
                vce_template_metaboxes(false, obj.template);
            });

        } else {
            vce_template_metaboxes(false);
        }

        $('body').on('change', vce_template_selector, function(e) {
            vce_template_metaboxes(true);
        });


        function vce_template_metaboxes(scroll_to, t) {

            var template = t ? t : $(vce_template_selector).val();

            if (template == 'template-modules.php') {
                $('#vce_hp_fa').fadeIn(300);
                $('#vce_hp_modules').fadeIn(300);
                $('#vce_hp_content').fadeIn(300);
                $('#vce_layout').fadeOut(300);
                if (scroll_to) {
                    target = $('#vce_hp_modules').attr('id');
                    $('html, body').stop().animate({
                        'scrollTop': $('#' + target).offset().top
                    }, 900, 'swing', function() {
                        window.location.hash = target;
                    });
                }
            } else {
                $('#vce_hp_fa').fadeOut(300);
                $('#vce_hp_modules').fadeOut(300);
                $('#vce_hp_content').fadeOut(300);
            }

            if (template == 'template-authors.php') {
                $('#vce_authors').fadeIn(300);
                $('#vce_layout').fadeOut(300);
                if (scroll_to) {
                    target = $('#vce_authors').attr('id');
                    $('html, body').stop().animate({
                        'scrollTop': $('#' + target).offset().top
                    }, 900, 'swing', function() {
                        window.location.hash = target;
                    });
                }
            } else {
                $('#vce_authors').fadeOut(300);
            }

            if (template == 'template-no-title.php') {
                $('#vce_layout').fadeOut(300);
            }

            if (template == 'default') {
                $('#vce_layout').fadeIn(300);
            }

        }

        /* Show hide actions */

        $("body").on("click", ".vce-action-pick", function(e) {
            var class_prefix = $(this).val();
            $(this).closest('.vce-tab').find('.hideable').hide();
            $(this).closest('.vce-tab').find('.vce-' + class_prefix + '-wrap').fadeIn(300);
        });



        /* Call live search */
        vce_live_search('vce_ajax_search');

        /* Live search functionality */
        function vce_live_search(search_ajax_action) {

            $('body').on('focus', '.vce-live-search', function() {

                var $this = $(this),
                    get_module_type = 'posts';

                if ($this.hasClass('vce-live-search-with-cpts')) {
                    get_module_type = $this.closest('.vce-opt-box').find('.vce-fa-post-type').val();
                    if (get_module_type === 'post') {
                        get_module_type = 'featured';
                    }
                } else {
                    get_module_type = $this.closest('.vce-live-search-opt').find('.vce-live-search-hidden').data('type');
                }

                $this.autocomplete({
                    source: function(req, response) {
                        $.getJSON(vce_js_settings.ajax_url + '?callback=?&action=' + search_ajax_action + '&type=' + get_module_type, req, response);
                    },
                    delay: 300,
                    minLength: 4,
                    select: function(event, ui) {

                        var $this = $(this);
                        var wrap = $this.closest('.vce-live-search-opt');

                        wrap.find('.vce-live-search-items').append('<span><button type="button" class="ntdelbutton" data-id="' + ui.item.id + '"><span class="remove-tag-icon"></span></button><span class="vce-searched-title">' + ui.item.label + '</span></span>');
                        vce_update_items($this);
                        wrap.find('.vce-live-search').val('');

                        return false;
                    }
                });

            });

            vce_sort_searched_items();
            vce_remove_all_search_items_on_post_type_change();
            vce_remove_searched_items();


        }

        /**
         * Sort/reorder searched items from list 
         */
        function vce_sort_searched_items() {
            $('.vce-live-search-items.tagchecklist').sortable({
                revert: false,
                cursor: "move",
                containment: "parent",
                opacity: 0.8,
                update: function(event, ui) {
                    vce_update_items($(this));
                }
            });
        }

        /**
         * Remove searched item from list 
         */
        function vce_remove_searched_items() {
            $('body').on('click', '.vce-live-search-opt .ntdelbutton', function(e) {
                var $this = $(this);
                var parent = $this.closest('.vce-live-search-items');
                $this.parent().remove();
                vce_update_items(parent);
            });
        }

        /**
         * Sync/update hander function for list items on add, reorder or remove actions
         */
        function vce_update_items(object) {

            var wrapper = object.closest('.vce-live-search-opt');
            var hidden_field = wrapper.find('.vce-live-search-hidden');
            var hidden_val = [];

            wrapper.find('.ntdelbutton').each(function() {
                hidden_val.push($(this).attr('data-id'));
            });

            hidden_field.val(hidden_val.toString());
        }

        /**
         * Remove searched item from list
         */
        function vce_remove_all_search_items_on_post_type_change() {
            $('body').on('change', '.vce-fa-post-type', function() {
                var $searched_items = $('.vce-live-search-items'),
                    $search = $('.vce-live-search-hidden');

                $searched_items.html('');
                $search.val('');
            });
        }

        /* Sortable functionality */
        function vce_sort_items(object) {
            object.sortable({
                revert: false,
                cursor: "move",
                placeholder: 'vce-fields-placeholder',
                opacity: 0.8
            });
        }


        var vce_watch_for_changes = {

            init: function() {
                var $watchers = $('.vce-watch-for-changes');

                if (vce_empty($watchers)) {
                    return;
                }

                $watchers.each(this.initWatching);
            },

            initWatching: function(i, elem) {
                var $elem = $(elem),
                    watchedElemClass = $elem.data('watch'),
                    showOnValue = $elem.data('show-on-value'),
                    hideOnValue = $elem.data('hide-on-value');

                if (!vce_empty(showOnValue)) {
                    $('body').on('change', '.' + watchedElemClass, showByValue);
                } else {
                    $('body').on('change', '.' + watchedElemClass, hideByValue);
                }

                function hideByValue() {
                    var $this = $(this);

                    if (!$this.hasClass(watchedElemClass)) {
                        $this = $('.' + watchedElemClass + ':checked, ' + '.' + watchedElemClass + ':checked, ' + '.' + watchedElemClass + ':selected');
                    }

                    if (vce_empty($this)) {
                        return false;
                    }

                    var val = $this.val();

                    if (val === hideOnValue) {
                        $elem.hide();
                        return true;
                    }

                    $elem.show();
                    return false;
                }

                function showByValue() {
                    var $this = $(this);

                    if (!$this.hasClass(watchedElemClass)) {
                        $this = $('.' + watchedElemClass + ':checked, ' + '.' + watchedElemClass + ':checked, ' + '.' + watchedElemClass + ' > option:selected');
                    }

                    if (vce_empty($this)) {
                        return false;
                    }

                    var val = $this.val();

                    if (val === showOnValue) {
                        $elem.show();
                        return true;
                    }

                    $elem.hide();
                    return false;
                }

                showByValue();
                hideByValue();
            }

        };

        vce_watch_for_changes.init();
        /**
         * Checks if variable is empty or not
         *
         * @param variable
         * @returns {boolean}
         */
        function vce_empty(variable) {

            if (typeof variable === 'undefined') {
                return true;
            }

            if (variable === 0 || variable === '0') {
                return true;
            }

            if (variable === null) {
                return true;
            }

            if (variable.length === 0) {
                return true;
            }

            if (variable === "") {
                return true;
            }

            if (variable === false) {
                return true;
            }

            if (typeof variable === 'object' && $.isEmptyObject(variable)) {
                return true;
            }

            return false;
        }

    });
})(jQuery);