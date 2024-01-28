<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// Pass &debug=1 for extra checks that would not be expected to ever consistently pass

/**
 * Composr test case class (unit testing).
 */
class css_file_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();

        require_code('themes2');
        require_code('images');
        require_code('files2');
    }

    public function testClassUsage()
    {
        $debug = get_param_integer('debug', 0);
        $only_theme = get_param_string('only_theme', null);

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            if (($only_theme !== null) && ($theme != $only_theme)) {
                continue;
            }

            if ($theme == '_unnamed_') {
                continue;
            }

            $_classes_defined = array_merge($this->findClassesDefined('default'), $this->findClassesDefined($theme));
            sort($_classes_defined);
            $classes_defined = array_flip($_classes_defined);

            $_classes_used = array_merge($this->findClassesUsed('default'), $this->findClassesUsed($theme));
            sort($_classes_used);
            $classes_used = array_flip($_classes_used);

            if ($debug == 1) {
                foreach (array_keys($classes_used) as $class) {
                    // Exceptions
                    if (strpos($class, 'box___') !== false) {
                        continue;
                    }

                    $this->assertTrue(isset($classes_defined[$class]), 'CSS class used but not defined: ' . $class . ' (for theme: ' . $theme . ')');
                }
            }

            foreach (array_keys($classes_defined) as $class) {
                if ($this->isClassDefinedAndIntentionallyNotExplicitlyUsed($class)) {
                    continue;
                }

                $this->assertTrue(isset($classes_used[$class]), 'CSS class defined but not used: ' . $class . ' (for theme: ' . $theme . ')');
            }
        }
    }

    protected function findClassesDefined($theme)
    {
        $out = array();

        $directories = array(
             get_file_base() . '/themes/' . $theme . '/css_custom',
             get_file_base() . '/themes/' . $theme . '/css',
        );

        foreach ($directories as $dir) {
            $d = opendir($dir);
            while (($e = readdir($d)) !== false) {
                if (substr($e, -4) == '.css') {
                    // Exceptions
                    if ($e == 'svg.css') {
                        continue;
                    }

                    $contents = file_get_contents($dir . '/' . $e);
                    $matches = array();
                    $found = preg_match_all('#\.([a-z][a-z_\d]*)[ ,:]#i', $contents, $matches);
                    for ($i = 0; $i < $found; $i++) {
                        if ($matches[1][$i] != 'txt') {
                            $out[] = $matches[1][$i];
                        }
                    }
                }
            }
            closedir($d);
        }

        return array_unique($out);
    }

    protected function findClassesUsed($theme)
    {
        $out = array();

        $directories = array(
             get_file_base() . '/themes/' . $theme . '/templates_custom',
             get_file_base() . '/themes/' . $theme . '/templates',
             get_file_base() . '/themes/' . $theme . '/javascript_custom',
             get_file_base() . '/themes/' . $theme . '/javascript',
        );

        foreach ($directories as $dir) {
            $d = opendir($dir);
            while (($e = readdir($d)) !== false) {
                if (substr($e, -4) == '.tpl' || substr($e, -3) == '.js') {
                    $contents = file_get_contents($dir . '/' . $e);
                    $matches = array();
                    $found = preg_match_all('#class="([\w ]+)"#', $contents, $matches);
                    for ($i = 0; $i < $found; $i++) {
                        $out = array_merge($out, explode(' ', $matches[1][$i]));
                    }
                }
            }
            closedir($d);
        }

        return array_unique($out);
    }

    public function testSelectorUsage()
    {
        $only_theme = get_param_string('only_theme', null);

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            if (($only_theme !== null) && ($theme != $only_theme)) {
                continue;
            }

            $directories = array(
                 get_file_base() . '/themes/default/css_custom' => ($theme == 'default'),
                 get_file_base() . '/themes/default/css' => ($theme == 'default'),
                 get_file_base() . '/themes/default/templates_custom' => ($theme == 'default'),
                 get_file_base() . '/themes/default/templates' => ($theme == 'default'),
                 get_file_base() . '/themes/default/javascript_custom' => ($theme == 'default'),
                 get_file_base() . '/themes/default/javascript' => ($theme == 'default'),
            );
            if ($theme != 'default') {
                $directories = array_merge($directories, array(
                    get_file_base() . '/themes/' . $theme . '/css_custom' => true,
                    get_file_base() . '/themes/' . $theme . '/css' => true,
                    get_file_base() . '/themes/' . $theme . '/templates_custom' => true,
                    get_file_base() . '/themes/' . $theme . '/templates' => true,
                    get_file_base() . '/themes/' . $theme . '/javascript_custom' => true,
                    get_file_base() . '/themes/' . $theme . '/javascript' => true,
                ));
            }

            $non_css_contents = '';
            $selector_files = array();

            foreach ($directories as $dir => $to_use) {
                $dh = opendir($dir);
                while (($f = readdir($dh)) !== false) {
					if ($f == '.' || $f == '..') {
						continue;
					}

                    // Exceptions
                    $exceptions = array(
                        'columns.css',
                        'google_search.css',
                        'jquery_ui.css',
                        'mediaelementplayer.css',
                        'openid.css',
                        'skitter.css',
                        'svg.css',
                        'widget_color.css',
                        'widget_date.css',
                        'widget_select2.css',
                    );
                    if (in_array($f, $exceptions)) {
                        continue;
                    }

                    $contents = file_get_contents($dir . '/' . $f);

                    $is_css_file = (substr($f, -4) == '.css');

                    if ($is_css_file) {
                        if (!$to_use) {
                            continue;
                        }

                        // Let's do a few simple CSS checks, less than a proper validator would do
                        if (($is_css_file) && (strpos($contents, '{$,Parser hint: pure}') === false)) {
                            // Test comment/brace balancing
                            $a = substr_count($contents, '{');
                            $b = substr_count($contents, '}');
                            $this->assertTrue($a == $b, 'Mismatched braces in ' . $f . ' in ' . $theme . ', ' . integer_format($a) . ' vs ' . integer_format($b));
                            $a = substr_count($contents, '/*');
                            $b = substr_count($contents, '*/');
                            $this->assertTrue($a == $b, 'Mismatched comments in ' . $f . ' in ' . $theme . ', ' . integer_format($a) . ' vs ' . integer_format($b));

                            // Strip comments
                            $contents = preg_replace('#/\*.*\*/#s', '', $contents);

                            // Test selectors
                            $matches = array();
                            $num_matches = preg_match_all('#^\s*[^@\s].*[^%\s]\s*\{$#m', $contents, $matches); // Finds selectors. However NB: @ is media rules, % is keyframe rules, neither are selectors.
                            for ($i = 0; $i < $num_matches; $i++) {
                                $matches2 = array();
                                $num_matches2 = /*find class/ID words*/preg_match_all('#[\w\-]+#', /*strip quotes*/preg_replace('#"[^"]*"#', '', /*strip CSS syntax*/preg_replace('#[:@][\w\-]+#', '', $matches[0][$i])), $matches2);
                                for ($j = 0; $j < $num_matches2; $j++) {
                                    if (!isset($selector_files[$f])) {
                                        $selector_files[$f] = array();
                                    }
                                    $selector_files[$f][$matches2[0][$j]] = true;
                                }
                            }
                        }
                    } else {
                        $non_css_contents .= $contents;
                    }
                }
                closedir($dh);
            }

            foreach ($selector_files as $file => $selectors) {
                ksort($selectors);
                foreach (array_keys($selectors) as $selector) {
                    // Exceptions
                    if ($this->isClassDefinedAndIntentionallyNotExplicitlyUsed($selector)) {
                        continue;
                    }
                    if ($this->isIDDefinedAndIntentionallyNotExplicitlyUsed($selector)) {
                        continue;
                    }

                    $this->assertTrue(strpos($non_css_contents, $selector) !== false, 'Possibly unused CSS selector for theme ' . $theme . ', ' . $file . ': ' . $selector);
                }
            }
        }
    }

    protected function isClassDefinedAndIntentionallyNotExplicitlyUsed($class)
    {
        $prefix_exceptions = array(
            'zone_running_',
            'page_running_',
            'menu__',
            'buttons__',
            'box___block_no_entries_',
            'cns_gcol_',
            'calendar_priority_',
            'cke_',
            'bubble_',
            'attitude_',
            'input_',
            'align',
            'display_type_',
        );
        if (preg_match('#^' . implode('|', $prefix_exceptions) . '#', $class) != 0) {
            return true;
        }

        $exceptions = array(
            'active_repeating',
            'img_thumb_opaque',
            'non_docked',
            'active_item',
            'thick_border',
            'access_restricted_in_list',
            'activated_quote_button',
            'activities_content__remove_failure',
            'activities_content__remove_success',
            'adminzone_search',
            'ajax_loading_block',
            'ajax_tree_magic_button',
            'alert',
            'alt_field',
            'archive_link',
            'associated_breadcrumbs',
            'associated_details_smaller',
            'attachment_left',
            'attachment_right',
            'author_defined',
            'author_undefined',
            'being_dragged',
            'big_tab_active',
            'big_tab_inactive',
            'block_main_members__avatars',
            'block_main_members__media',
            'block_main_members__photos',
            'blue',
            'brethren_1',
            'box___block_main_content',
            'box___block_menu_embossed',
            'box___standardbox_accordion',
            'box_scroll_thumbs',
            'bubble',
            'buildr_self_member',
            'button_screen_item_tall',
            'calendar_active',
            'calendar_current',
            'calendar_day',
            'calendar_free_time',
            'calendar_free_time_hourly',
            'calendar_month_day',
            'calendar_multiple',
            'calendar_year_month_day',
            'cns_unread_topic_title',
            'chat_lobby_convos_current_tab',
            'chat_lobby_convos_tab_first',
            'chat_lobby_convos_tab_new_messages',
            'chat_lobby_convos_tab_uptodate',
            'chat_message',
            'chat_message_old',
            'chat_operator_staff',
            'chat_options',
            'checked',
            'cms_keep',
            'cms_keep_block',
            'cns_edit_forum_forum',
            'cns_forum_topic_indent',
            'cns_forum_topic_wrapper_column_column6',
            'cns_forum_topic_wrapper_column_column6_shorter',
            'cns_guest_column_b',
            'cns_member_box_avatar_touching',
            'cns_on',
            'cns_post_emphasis',
            'cns_post_map_item_unread',
            'cns_post_personal',
            'column',
            'column_wrapper',
            'column_wrapper_2',
            'com',
            'command_output',
            'comments_sorting_box',
            'confirm',
            'count_0',
            'crossword',
            'cse',
            'css',
            'csstransitions',
            'current',
            'decryption_overlay',
            'delete_cross_button',
            'dh',
            'divider',
            'doc_link',
            'e',
            'faded_tooltip_img',
            'feature_background_image',
            'feature_image',
            'feature_video',
            'feed_link',
            'fieldset',
            'filledin',
            'footer_button_loading',
            'footer_links',
            'form_table_description_above_cell',
            'form_table_description_under_cell',
            'form_table_huge_field_description_is_under',
            'fp_col_block',
            'fp_col_blocks_wrap',
            'fractional_edit',
            'frame',
            'friend_active',
            'friend_inactive',
            'ghost',
            'global_community_message',
            'global_helper_panel_text_over',
            'green',
            'h',
            'has_img',
            'has_item_width',
            'has_preview',
            'have_links',
            'helper_panel',
            'helper_panel_hidden',
            'hero_button',
            'hero_section',
            'highlighted_post',
            'hover',
            'hover__dark',
            'icon_14_export',
            'im_event',
            'im_popup_avatar',
            'im_popup_close_button',
            'image_number',
            'image_number_select',
            'info_slide_dots',
            'info_slide_thumb',
            'js',
            'js_widget',
            'label',
            'leave_native_tooltip',
            'legend',
            'light_table',
            'lightbox_image',
            'linear',
            'link',
            'link__dark',
            'link_exempt2',
            'loading_overlay',
            'magic_image_edit_link',
            'media_link',
            'media_set',
            'menu_editor_selected_field',
            'menu_type__top',
            'message',
            'mobile',
            'moono',
            'mousemove',
            'must_show_together',
            'native_ui_foreground',
            'native_ui_selected',
            'non_current',
            'notes_about',
            'notification',
            'notification_code',
            'notification_has_read',
            'odp_link',
            'ods_link',
            'odt_link',
            'openid_large_btn',
            'openid_small_btn',
            'opens_below',
            'overlay',
            'overlay_close_button',
            'p',
            'pagination_load_more',
            'paused',
            'pdf_link',
            'people_list',
            'pic',
            'play_button',
            'plupload',
            'pointstore_item_tag',
            'popup_spacer',
            'post',
            'ppt_link',
            'preview_box',
            'previous_button',
            'proceed_button_left_2',
            'radio_list_picture',
            'radio_list_picture_na',
            'rating_box',
            'rating_likers',
            'rating_star',
            'rating_star_highlight',
            'rating_stars',
            'red',
            'related_field',
            'required',
            'responsive',
            'rss_copyright',
            'rss_main',
            'rss_main_inner',
            'scroll_thumbs',
            'selected',
            'site_special_message_alt_inner',
            'site_unloading',
            'sitewide_im_popup_body',
            'skip_step_button_wrap_with_req_note',
            'sku',
            'sortable_table',
            'standard_field_name',
            'status_green',
            'status_orange',
            'tabs__member_account__warnings',
            'theme_image__background_theme_image',
            'theme_image__logo_theme_image',
            'theme_image_preview',
            'theme_image_preview_wide',
            'tooltip',
            'tooltip_img',
            'tooltip_nolayout',
            'tooltip_ownlayout',
            'tooltip_with_img',
            'topic_list_title',
            'topic_list_topic',
            'toplevel',
            'toplevel_link',
            'torrent_link',
            'touch_enabled',
            'tpl',
            'tpl_dropdown_row_a',
            'tpl_dropdown_row_b',
            'tree_list_highlighted',
            'tree_list_nonhighlighted',
            'txt_link',
            'unclosed_ticket',
            'unslider',
            'up_alert',
            'v',
            'validated_checkbox',
            'version',
            'version_button',
            'version_details',
            'version_help_icon',
            'version_news_link',
            'version_number',
            'visited',
            'vr',
            'width',
            'wysiwyg_toolbar_color_finder',
            'ze_panel_expanded',
            'ze_textarea',
            'ze_textarea_middle',
            'zebra_0',
            'zebra_1',
            'hide_if_in_panel',
            'hide_if_not_in_panel',
            'AOL',
            'Blogger',
            'ClaimID',
            'Flickr',
            'Google',
            'LiveJournal',
            'MyOpenID',
            'MySpace',
            'OpenID',
            'Technorati',
            'Vidoop',
            'Wordpress',
            'Yahoo',
            'cellDebug',
            'cellDebugA',
            'cellDebugB',
            'cellEmpty',
            'cellEmptyA',
            'cellEmptyB',
            'cellLetter',
            'cellLetterA',
            'cellLetterB',
            'cellNumber',
            'cellNumberA',
            'cellNumberB',
            'crossTable',
            'crossTableA',
            'crossTableB',
            'progressBarComplete',
            'progressBarError',
            'progressBarInProgress',
            'progressBarStatus',
            'progressCancel',
            'progressContainer',
            'progressName',
            'progressWrapper',
            'questionTable',
            'less_compact',
            'bundled',
            'non_bundled',
        );
        if (in_array($class, $exceptions)) {
            return true;
        }

        return false;
    }

    protected function isIDDefinedAndIntentionallyNotExplicitlyUsed($id)
    {
        $prefix_exceptions = array(
            't_1_',
            'jump_to_',
            'sort_filedump_',
            'type_filter_',
            'edit_panel_',
            'info_panel_',
            'settings_panel_',
            'view_panel_',
        );
        if (preg_match('#^' . implode('|', $prefix_exceptions) . '#', $id) != 0) {
            return true;
        }

        $exceptions = array(
            'xslt_introduction',
        );
        if (in_array($id, $exceptions)) {
            return true;
        }

        return false;
    }
}
