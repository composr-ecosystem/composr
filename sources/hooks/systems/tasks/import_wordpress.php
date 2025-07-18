<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    news
 */

/**
 * Hook class.
 */
class Hook_task_import_wordpress
{
    /**
     * Run the task hook.
     *
     * @param  BINARY $is_validated Whether to import as validated
     * @param  BINARY $download_images Whether to download remote images
     * @param  BINARY $to_own_account Whether to import everything to the task initiator's account
     * @param  BINARY $import_blog_comments Whether to import comments
     * @param  BINARY $import_to_blog Whether to import everything to blog news categories
     * @param  boolean $import_wordpress_users Whether to import Wordpress users
     * @return ?array A tuple of at least 2: Return mime-type, content (either Tempcode, or a string, or a filename and file-path pair to a temporary file), map of HTTP headers if transferring immediately, map of ini_set commands if transferring immediately (null: show standard success message)
     */
    public function run($is_validated, $download_images, $to_own_account, $import_blog_comments, $import_to_blog, $import_wordpress_users)
    {
        set_mass_import_mode();

        require_lang('news');
        require_code('news');
        require_code('news2');
        require_code('files');

        log_it('IMPORT_NEWS');

        $GLOBALS['LAX_COMCODE'] = true;

        $data = _get_wordpress_db_data();

        if (get_forum_type() == 'cns') {
            require_code('cns_members_action');
            require_code('cns_groups');

            $def_grp_id = get_first_default_group();
        }

        $cat_id = array();

        $NEWS_CATS = $GLOBALS['SITE_DB']->query_select('news_categories', array('*'), array('nc_owner' => null));
        $NEWS_CATS = list_to_map('id', $NEWS_CATS);

        $imported_news = array();
        $imported_pages = array();

        if (addon_installed('import')) {
            require_code('import');
        }

        foreach ($data as $values) {
            // Lookup/import member
            if ($to_own_account == 1) {
                // If post should go to own account
                $submitter_id = get_member();
            } else {
                if (get_forum_type() == 'cns') {
                    $submitter_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', array('m_username' => $values['user_login']));

                    if (is_null($submitter_id)) {
                        if ($import_wordpress_users) {
                            $submitter_id = cns_make_member($values['user_login'], $values['user_pass'], '', null, null, null, null, array(), null, $def_grp_id, 1, time(), time(), '', null, '', 0, 0, 1, '', '', '', 1, 0, null, 1, 1, null, '', false, 'wordpress');
                        } else {
                            $submitter_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username('admin');    // Set admin as owner
                            if (is_null($submitter_id)) {
                                $submitter_id = $GLOBALS['FORUM_DRIVER']->get_guest_id() + 1;
                            }
                        }
                    }
                } else {
                    $submitter_id = $GLOBALS['FORUM_DRIVER']->get_guest_id(); // Guest user
                }
            }

            if (array_key_exists('POSTS', $values)) {
                // Create news and pages
                foreach ($values['POSTS'] as $post_id => $post) {
                    // Validation status
                    $validated = $is_validated;
                    if ($post['post_status'] == 'publish') {
                        $validated = 1;
                    } else {
                        $validated = 0;
                    }
                    if (!addon_installed('unvalidated')) {
                        $validated = 1;
                    }

                    // Allow comments/trackbacks
                    $allow_comments = ($post['comment_status'] == 'open') ? 1 : 0;
                    $allow_trackbacks = ($post['ping_status'] == 'open') ? 1 : 0;

                    // Dates
                    $post_time = strtotime($post['post_date_gmt']);
                    if ($post_time === false) {
                        $post_time = strtotime($post['post_date']);
                    }
                    if (($post_time < 0) || ($post_time > 2147483647)) { // TODO: #3046 in tracker
                        $post_time = 2147483647;
                    }
                    $edit_time = is_null($post['post_modified_gmt']) ? null : strtotime($post['post_modified_gmt']);
                    if ($edit_time === false) {
                        $edit_time = strtotime($post['post_modified']);
                    }
                    if (!is_null($edit_time)) {
                        if (($edit_time < 0) || ($edit_time > 2147483647)) { // TODO: #3046 in tracker
                            $edit_time = 2147483647;
                        }
                    }

                    if ($post['post_type'] == 'post') { // News
                        if (addon_installed('import')) {
                            if (import_check_if_imported('news', strval($post_id))) {
                                continue;
                            }
                        }

                        // Work out categories
                        $owner_category_id = mixed();
                        $cat_ids = array();
                        if (array_key_exists('category', $post)) {
                            $i = 0;
                            foreach ($post['category'] as $category) {
                                if ($category == 'Uncategorized') {
                                    continue;
                                }    // Skip blank category creation

                                $cat_id = mixed();
                                foreach ($NEWS_CATS as $id => $existing_cat) {
                                    if (get_translated_text($existing_cat['nc_title']) == $category) {
                                        $cat_id = $id;
                                    }
                                }
                                if (is_null($cat_id)) { // Could not find existing category, create new
                                    $cat_id = add_news_category($category, 'newscats/community', $category);
                                    require_code('permissions2');
                                    set_global_category_access('news', $cat_id);
                                    // Need to reload now
                                    $NEWS_CATS = $GLOBALS['SITE_DB']->query_select('news_categories', array('*'));
                                    $NEWS_CATS = list_to_map('id', $NEWS_CATS);
                                }
                                if (($i == 0) && ($import_to_blog == 0)) {
                                    $owner_category_id = $cat_id; // Primary
                                } else {
                                    $cat_ids[] = $cat_id; // Secondary
                                }
                                $i++;
                            }
                        }
                        if (is_null($owner_category_id)) {
                            $owner_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('news_categories', 'id', array('nc_owner' => $submitter_id));
                        }

                        // Content
                        $news = '';
                        $news_article = import_foreign_news_html(trim($post['post_content']), true);
                        if ($post['post_password'] != '') {
                            $news_article = '[highlight]' . do_lang('POST_ACCESS_IS_RESTRICTED') . '[/highlight]' . "\n\n" . '[if_in_group="Administrators"]' . $news_article . '[/if_in_group]';
                        }

                        // Add news
                        $id = add_news(
                            $post['post_title'],
                            $news,
                            null,
                            $validated,
                            1,
                            $allow_comments,
                            $allow_trackbacks,
                            '',
                            $news_article,
                            $owner_category_id,
                            $cat_ids,
                            $post_time,
                            $submitter_id,
                            0,
                            $edit_time,
                            null,
                            ''
                        );
                        if (array_key_exists('category', $post)) {
                            require_code('seo2');
                            seo_meta_set_for_explicit('news', strval($id), implode(',', $post['category']), $news);
                        }

                        if (addon_installed('import')) {
                            import_id_remap_put('news', strval($post_id), $id);
                        }

                        // Needed for adding comments/trackbacks
                        $comment_identifier = 'news_' . strval($id);
                        $content_url = build_url(array('page' => 'news', 'type' => 'view', 'id' => $id), get_module_zone('news'), null, false, false, true);
                        $content_title = $post['post_title'];
                        $trackback_for_type = 'news';
                        $trackback_id = $id;

                        // Track import IDs
                        $imported_news[] = array(
                            //'full_url' => '', We don't know this for a database import
                            'import_id' => $id,
                            'import__news' => $news,
                            'import__news_article' => $news_article,
                        );

                        $topic_identifier = 'news_' . strval($id);
                    } elseif ($post['post_type'] == 'page') { // Page/articles
                        // If we don't have permission to write comcode pages, skip the page
                        if (!has_submit_permission('high', get_member(), get_ip_address(), null, null)) {
                            continue;
                        }

                        // Save articles as new comcode pages
                        $zone = 'site';
                        $lang = fallback_lang();
                        $file = preg_replace('#[^' . URL_CONTENT_REGEXP . ']#', '_', $post['post_name']); // Filter non-alphanumeric characters
                        $full_path = zone_black_magic_filterer(get_custom_file_base() . (($zone == '') ? '' : '/') . $zone . '/pages/comcode_custom/' . $lang . '/' . $file . '.txt');

                        // Content
                        $_content = "[title]" . comcode_escape($post['post_title']) . "[/title]\n\n";
                        $imp_con = import_foreign_news_html(trim($post['post_content']), true);
                        if ($imp_con != '') {
                            $_content .= '[surround]' . $imp_con . '[/surround]';
                        } else {
                            continue;
                        } /* Not a real page */
                        $_content .= "\n\n[block]main_comcode_page_children[/block]";
                        if ($allow_comments == 1) {
                            $_content .= "\n\n[block=\"main\"]main_comments[/block]";
                        }
                        if ($allow_trackbacks == 1) {
                            $_content .= "\n\n[block id=\"0\"]main_trackback[/block]";
                        }

                        $topic_identifier = $file . '_main';

                        // Add to the database
                        $GLOBALS['SITE_DB']->query_delete('comcode_pages', array(
                            'the_zone' => $zone,
                            'the_page' => $file,
                        ), '', 1);
                        $GLOBALS['SITE_DB']->query_insert('comcode_pages', array(
                            'the_zone' => $zone,
                            'the_page' => $file,
                            'p_parent_page' => '',
                            'p_validated' => $is_validated,
                            'p_edit_date' => $post_time,
                            'p_add_date' => $edit_time,
                            'p_submitter' => $submitter_id,
                            'p_show_as_edit' => 0,
                            'p_order' => 0,
                        ));

                        // Save to disk
                        $success_status = cms_file_put_contents_safe($full_path, $_content, FILE_WRITE_FAILURE_SILENT | FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
                        if (!$success_status) {
                            return array(null, do_lang_tempcode('COULD_NOT_SAVE_FILE', escape_html($full_path)));
                        }

                        // Meta
                        if (array_key_exists('category', $post)) {
                            require_code('seo2');
                            seo_meta_set_for_explicit('comcode_page', $zone . ':' . $file, implode(',', $post['category']), '');
                        }

                        // Track import IDs etc
                        $parent_page = $post['post_parent'];
                        if ($parent_page == 0) {
                            $parent_page = null;
                        }
                        $imported_pages[] = array(
                            'contents' => $_content,
                            'zone' => $zone,
                            'page' => $file,
                            'path' => $full_path,
                            'parent_page' => $parent_page,
                            'id' => $post['post_id'],
                        );

                        // Restricted access
                        if ($post['post_password'] != '') {
                            $usergroups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list();
                            foreach (array_keys($usergroups) as $group_id) {
                                $GLOBALS['SITE_DB']->query_delete('group_page_access', array('page_name' => $file, 'zone_name' => $zone, 'group_id' => $group_id), '', 1);
                                $GLOBALS['SITE_DB']->query_insert('group_page_access', array('page_name' => $file, 'zone_name' => $zone, 'group_id' => $group_id));
                            }
                        }

                        // Needed for adding comments/trackbacks
                        $comment_identifier = $file . '_main';
                        $content_url = build_url(array('page' => $file), $zone, null, false, false, true);
                        $content_title = $post['post_title'];
                        $trackback_for_type = $file;
                        $trackback_id = 0;
                    }

                    // Add comments
                    if ($import_blog_comments == 1) {
                        if (array_key_exists('COMMENTS', $post)) {
                            $comment_mapping = array();
                            foreach ($post['COMMENTS'] as $comment) {
                                if (get_forum_type() == 'cns') {
                                    $submitter = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', array('m_username' => $comment['comment_author']));
                                    if (is_null($submitter)) {
                                        $submitter = $GLOBALS['FORUM_DRIVER']->get_guest_id(); // If comment is made by a non-member, assign comment to guest account
                                    }
                                } else {
                                    $submitter = $GLOBALS['FORUM_DRIVER']->get_guest_id();
                                }

                                require_code('feedback');
                                $forum = (is_null(find_overridden_comment_forum('news'))) ? get_option('comments_forum_name') : find_overridden_comment_forum('news');

                                $comment_parent_id = mixed();
                                if ((get_forum_type() == 'cns') && (!is_null($comment['comment_parent'])) && (isset($comment_mapping[$comment['comment_parent']]))) {
                                    $comment_parent_id = $comment_mapping[$comment['comment_parent']];
                                }
                                if ($comment_parent_id == 0) {
                                    $comment_parent_id = null;
                                }

                                $comment_content = import_foreign_news_html(trim($comment['comment_content']), true);

                                $comment_author_url = $comment['comment_author_url'];
                                $comment_author_email = $comment['comment_author_email'];

                                $comment_type = $comment['comment_type'];
                                if (($comment_type == 'trackback') || ($comment_type == 'pingback')) {
                                    $GLOBALS['SITE_DB']->query_insert('trackbacks', array(
                                        'trackback_for_type' => $trackback_for_type,
                                        'trackback_for_id' => strval($trackback_id),
                                        'trackback_ip' => $comment['author_ip'],
                                        'trackback_time' => strtotime($comment['comment_date_gmt']),
                                        'trackback_url' => $comment_author_url,
                                        'trackback_title' => '',
                                        'trackback_excerpt' => $comment_content,
                                        'trackback_name' => $comment['comment_author'],
                                    ));
                                    continue;
                                }

                                if ($comment_author_url != '') {
                                    $comment_content .= "\n\n" . do_lang('WEBSITE') . ': [url]' . $comment_author_url . '[/url]';
                                }
                                if ($comment_author_email != '') {
                                    $comment_content .= "[staff_note]\n\n" . do_lang('EMAIL') . ': [email]' . $comment_author_email . "[/email][/staff_note]";
                                }

                                $result = $GLOBALS['FORUM_DRIVER']->make_post_forum_topic(
                                    $forum,
                                    $topic_identifier,
                                    $submitter,
                                    '', // Would be post title
                                    $comment_content,
                                    $content_title,
                                    do_lang('COMMENT'),
                                    $content_url->evaluate(),
                                    strtotime($comment['comment_date_gmt']),
                                    $comment['author_ip'],
                                    ($comment['comment_approved'] == '1') ? 1 : 0/*e.g. "spam"*/,
                                    1,
                                    false,
                                    $comment['comment_author'],
                                    $comment_parent_id,
                                    false,
                                    null,
                                    null,
                                    time()
                                );

                                if (get_forum_type() == 'cns') {
                                    $comment_mapping[$comment['comment_ID']] = $GLOBALS['LAST_POST_ID'];
                                }
                            }
                        }
                    }
                }
            }
        }

        // Download images etc
        foreach ($imported_news as $item) {
            $news = $item['import__news'];
            $news_article = $item['import__news_article'];

            $news_rows = $GLOBALS['SITE_DB']->query_select('news', array('news', 'news_article'), array('id' => $item['import_id']), '', 1);

            _news_import_grab_images_and_fix_links($download_images == 1, $news, $imported_news);
            _news_import_grab_images_and_fix_links($download_images == 1, $news_article, $imported_news);

            $map = array();
            $map += lang_remap_comcode('news', $news_rows[0]['news'], $news);
            $map += lang_remap_comcode('news_article', $news_rows[0]['news_article'], $news_article);
            $GLOBALS['SITE_DB']->query_update('news', $map, array('id' => $item['import_id']), '', 1);
        }
        foreach ($imported_pages as $item) {
            $contents = $item['contents'];
            $zone = $item['zone'];
            $page = $item['page'];
            _news_import_grab_images_and_fix_links($download_images == 1, $contents, $imported_news);
            cms_file_put_contents_safe($item['path'], $contents, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
            if (!is_null($item['parent_page'])) {
                $parent_page = mixed();
                foreach ($imported_pages as $item2) {
                    if ($item2['id'] == $item['parent_page']) {
                        $parent_page = $item2['page'];
                    }
                }
                if (!is_null($parent_page)) {
                    $GLOBALS['SITE_DB']->query_update('comcode_pages', array('p_parent_page' => $parent_page), array('the_zone' => $zone, 'the_page' => $page), '', 1);
                }
            }
        }

        $ret = do_lang_tempcode('IMPORT_WORDPRESS_DONE');
        return array('text/html', $ret);
    }
}
