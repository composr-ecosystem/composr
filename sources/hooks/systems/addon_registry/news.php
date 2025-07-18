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
class Hook_addon_registry_news
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'News and blogging.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_news',
            'tut_adv_news',
            'tut_information',
        );
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'news_shared',
            ),
            'recommends' => array(),
            'conflicts_with' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/rich_content/news.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images/icons/24x24/menu/rich_content/news.png',
            'themes/default/images/icons/24x24/tabs/member_account/blog.png',
            'themes/default/images/icons/48x48/menu/rich_content/news.png',
            'themes/default/images/icons/48x48/tabs/member_account/blog.png',
            'sources/hooks/systems/reorganise_uploads/news.php',
            'sources/hooks/systems/notifications/news_entry.php',
            'sources/hooks/modules/admin_setupwizard_installprofiles/blog.php',
            'sources/hooks/systems/realtime_rain/news.php',
            'sources/hooks/systems/content_meta_aware/news.php',
            'sources/hooks/systems/content_meta_aware/news_category.php',
            'sources/hooks/systems/commandr_fs/news.php',
            'sources/hooks/systems/meta/news.php',
            'sources/hooks/blocks/side_stats/stats_news.php',
            'sources/hooks/systems/addon_registry/news.php',
            'sources/hooks/modules/admin_import_types/news.php',
            'sources/hooks/systems/config/news_summary_required.php',
            'sources/hooks/systems/config/blog_update_time.php',
            'sources/hooks/systems/config/news_show_stats_count_blogs.php',
            'sources/hooks/systems/config/news_show_stats_count_total_posts.php',
            'sources/hooks/systems/config/news_update_time.php',
            'sources/hooks/systems/config/ping_url.php',
            'sources/hooks/systems/config/points_ADD_NEWS.php',
            'sources/hooks/systems/profiles_tabs/blog.php',
            'sources/hooks/systems/sitemap/news.php',
            'sources/hooks/systems/sitemap/news_category.php',
            'themes/default/templates/NEWS_ARCHIVE_SCREEN.tpl',
            'themes/default/templates/NEWS_ENTRY_SCREEN.tpl',
            'themes/default/templates/BLOCK_BOTTOM_NEWS.tpl',
            'themes/default/templates/BLOCK_MAIN_NEWS.tpl',
            'themes/default/templates/BLOCK_SIDE_NEWS_ARCHIVE.tpl',
            'themes/default/templates/BLOCK_SIDE_NEWS.tpl',
            'themes/default/templates/BLOCK_SIDE_NEWS_SUMMARY.tpl',
            'themes/default/templates/BLOCK_SIDE_NEWS_CATEGORIES.tpl',
            'themes/default/templates/NEWS_CHICKLETS.tpl',
            'themes/default/templates/NEWS_WORDPRESS_IMPORT_SCREEN.tpl',
            'themes/default/images/newscats/index.html',
            'themes/default/images/newscats/art.jpg',
            'themes/default/images/newscats/business.jpg',
            'themes/default/images/newscats/community.jpg',
            'themes/default/images/newscats/difficulties.jpg',
            'themes/default/images/newscats/entertainment.jpg',
            'themes/default/images/newscats/general.jpg',
            'themes/default/images/newscats/technology.jpg',
            'cms/pages/modules/cms_news.php',
            'cms/pages/modules/cms_blogs.php',
            'site/pages/modules/news.php',
            'sources/blocks/bottom_news.php',
            'sources/blocks/main_news.php',
            'sources/blocks/side_news.php',
            'sources/blocks/side_news_archive.php',
            'sources/blocks/side_news_categories.php',
            'sources/hooks/blocks/main_staff_checklist/news.php',
            'sources/hooks/modules/admin_setupwizard/news.php',
            'sources/hooks/modules/admin_unvalidated/news.php',
            'sources/hooks/modules/members/news.php',
            'sources/hooks/modules/search/news.php',
            'sources/hooks/systems/attachments/news.php',
            'sources/hooks/systems/page_groupings/news.php',
            'sources/hooks/systems/module_permissions/news.php',
            'sources/hooks/systems/preview/news.php',
            'sources/hooks/systems/rss/news.php',
            'sources/hooks/systems/trackback/news.php',
            'sources/news.php',
            'sources/news2.php',
            'sources/hooks/modules/admin_import/rss.php',
            'sources/hooks/modules/admin_newsletter/news.php',
            'sources/hooks/blocks/main_staff_checklist/blog.php',
            'themes/default/templates/CNS_MEMBER_PROFILE_BLOG.tpl',
            'sources/hooks/systems/block_ui_renderers/news.php',
            'sources/hooks/systems/config/news_categories_per_page.php',
            'sources/hooks/systems/config/news_entries_per_page.php',
            'sources/hooks/systems/config/enable_secondary_news.php',
            'themes/default/templates/BLOCK_MAIN_IMAGE_FADER_NEWS.tpl',
            'sources/blocks/main_image_fader_news.php',
            'sources/news_sitemap.php',
            'sources/hooks/systems/tasks/import_rss.php',
            'sources/hooks/systems/tasks/import_wordpress.php',
        );
    }

    /**
     * Get mapping between template names and the method of this class that can render a preview of them
     *
     * @return array The mapping
     */
    public function tpl_previews()
    {
        return array(
            'templates/BLOCK_SIDE_NEWS_ARCHIVE.tpl' => 'block_side_news_archive',
            'templates/BLOCK_MAIN_NEWS.tpl' => 'block_main_news',
            'templates/BLOCK_SIDE_NEWS.tpl' => 'block_side_news',
            'templates/BLOCK_SIDE_NEWS_CATEGORIES.tpl' => 'block_side_news_categories',
            'templates/BLOCK_SIDE_NEWS_SUMMARY.tpl' => 'block_side_news',
            'templates/BLOCK_BOTTOM_NEWS.tpl' => 'block_bottom_news',
            'templates/NEWS_ENTRY_SCREEN.tpl' => 'news_full_screen',
            'templates/NEWS_CHICKLETS.tpl' => 'news_chicklets',
            'templates/NEWS_ARCHIVE_SCREEN.tpl' => 'news_archive_screen',
            'templates/NEWS_WORDPRESS_IMPORT_SCREEN.tpl' => 'administrative__news_wordpress_import_screen',
            'templates/NEWS_BRIEF.tpl' => 'news_archive_screen',
            'templates/NEWS_BOX.tpl' => 'block_main_news',
            'templates/CNS_MEMBER_PROFILE_BLOG.tpl' => 'cns_member_profile_blog',
            'templates/BLOCK_MAIN_IMAGE_FADER_NEWS.tpl' => 'block_main_image_fader_news',
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_main_image_fader_news()
    {
        require_lang('news');

        $news = array();
        $news[] = array(
            'TITLE' => lorem_phrase(),
            'IMAGE_URL' => placeholder_image_url(),
            'URL' => placeholder_url(),
            'BODY' => lorem_paragraph_html(),
            'DATE' => placeholder_date(),
            'DATE_RAW' => placeholder_date_raw(),
            'SUBMITTER' => placeholder_id(),
            'AUTHOR' => lorem_phrase(),
            'AUTHOR_URL' => placeholder_url(),
        );

        $block = do_lorem_template('BLOCK_MAIN_IMAGE_FADER_NEWS', array(
            'TITLE' => lorem_phrase(),
            'ARCHIVE_URL' => placeholder_url(),
            'NEWS' => $news,
            'MILL' => '8000',
        ));
        return array(
            lorem_globalise($block, null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__cns_member_profile_blog()
    {
        require_lang('news');

        $tab_content = do_lorem_template('CNS_MEMBER_PROFILE_BLOG', array(
            'MEMBER_ID' => placeholder_id(),
            'RECENT_BLOG_POSTS' => lorem_paragraph_html(),
            'RSS_URL' => placeholder_url(),
            'ADD_BLOG_POST_URL' => placeholder_url(),
        ));
        return array(
            lorem_globalise($tab_content, null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_side_news_archive()
    {
        require_lang('news');
        require_lang('dates');

        return array(
            lorem_globalise(do_lorem_template('BLOCK_SIDE_NEWS_ARCHIVE', array(
                'TITLE' => lorem_phrase(),
                'YEARS' => array(
                    array(
                        'YEAR' => '2010',
                        'TIMES' => array(
                            array(
                                'MONTH' => '2',
                                'MONTH_STRING' => do_lang('FEBRUARY'),
                                'URL' => placeholder_url(),
                            ),
                            array(
                                'MONTH' => '1',
                                'MONTH_STRING' => do_lang('JANUARY'),
                                'URL' => placeholder_url(),
                            )
                        )
                    ),
                    array(
                        'YEAR' => '2009',
                        'TIMES' => array(
                            array(
                                'MONTH' => '12',
                                'MONTH_STRING' => do_lang('DECEMBER'),
                                'URL' => placeholder_url(),
                            ),
                            array(
                                'MONTH' => '11',
                                'MONTH_STRING' => do_lang('NOVEMBER'),
                                'URL' => placeholder_url(),
                            )
                        )
                    )
                )
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_main_news()
    {
        require_lang('news');
        require_lang('cns');
        require_css('news');

        $contents = new Tempcode();
        foreach (placeholder_array() as $k => $v) {
            $contents->attach(do_lorem_template('NEWS_BOX', array(
                'BLOG' => lorem_phrase(),
                'AUTHOR_URL' => placeholder_url(),
                'CATEGORY' => lorem_phrase(),
                '_CATEGORY' => placeholder_id(),
                'IMG' => placeholder_image_url(),
                '_IMG' => placeholder_image_url(),
                'AUTHOR' => lorem_phrase(),
                '_AUTHOR' => lorem_phrase(),
                'SUBMITTER' => placeholder_id(),
                'AVATAR' => lorem_phrase(),
                'NEWS_TITLE' => lorem_phrase(),
                'NEWS_TITLE_PLAIN' => lorem_phrase(),
                'DATE' => lorem_phrase(),
                'NEWS' => lorem_phrase(),
                'COMMENTS' => lorem_phrase(),
                'VIEW' => lorem_phrase(),
                'ID' => placeholder_id(),
                'FULL_URL' => placeholder_url(),
                'COMMENT_COUNT' => lorem_phrase(),
                'READ_MORE' => lorem_sentence(),
                'TRUNCATE' => false,
                'FIRSTTIME' => lorem_word(),
                'LASTTIME' => lorem_word_2(),
                'CLOSED' => lorem_word(),
                'FIRSTUSERNAME' => lorem_word(),
                'LASTUSERNAME' => lorem_word(),
                'FIRSTMEMBERID' => placeholder_random_id(),
                'LASTMEMBERID' => placeholder_random_id(),
                'DATE_RAW' => lorem_word(),
                'GIVE_CONTEXT' => false,
                'TAGS' => '',
            )));
        }

        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_NEWS', array(
                'BLOG' => true,
                'TITLE' => lorem_phrase(),
                'CONTENT' => $contents,
                'RSS_URL' => placeholder_url(),
                'ATOM_URL' => placeholder_url(),
                'SUBMIT_URL' => placeholder_url(),
                'ARCHIVE_URL' => placeholder_url(),
                'BRIEF' => lorem_phrase(),
                'BLOCK_PARAMS' => '',

                'START' => '0',
                'MAX' => '10',
                'START_PARAM' => 'x_start',
                'MAX_PARAM' => 'x_max',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__news_wordpress_import_screen()
    {
        require_lang('news');

        return array(
            lorem_globalise(do_lorem_template('NEWS_WORDPRESS_IMPORT_SCREEN', array(
                'TITLE' => lorem_title(),
                'XML_UPLOAD_FORM' => placeholder_form(),
                'DB_IMPORT_FORM' => placeholder_form(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__news_archive_screen()
    {
        require_lang('news');

        $content = do_lorem_template('NEWS_BRIEF', array(
            'DATE' => placeholder_date(),
            'FULL_URL' => placeholder_url(),
            'NEWS_TITLE_PLAIN' => lorem_word(),
            'ID' => placeholder_id(),
            'NEWS_TITLE' => lorem_word(),
        ));

        return array(
            lorem_globalise(do_lorem_template('NEWS_ARCHIVE_SCREEN', array(
                'TITLE' => lorem_title(),
                'CONTENT' => $content,
                'SUBMIT_URL' => placeholder_url(),
                'BLOG' => false,
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__news_chicklets()
    {
        require_lang('news');
        require_css('news');

        return array(
            lorem_globalise(do_lorem_template('NEWS_CHICKLETS', array(
                'RSS_URL' => placeholder_url(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_side_news()
    {
        require_lang('news');

        $contents = new Tempcode();
        foreach (placeholder_array() as $k => $v) {
            $contents->attach(do_lorem_template('BLOCK_SIDE_NEWS_SUMMARY', array(
                'ID' => placeholder_id(),
                'SUBMITTER' => placeholder_id(),
                'AUTHOR' => lorem_phrase(),
                'IMG_URL' => placeholder_image_url(),
                'CATEGORY' => lorem_phrase(),
                'BLOG' => true,
                'FULL_URL' => placeholder_url(),
                'NEWS' => lorem_paragraph(),
                'NEWS_TITLE' => lorem_phrase(),
                '_DATE' => placeholder_date_raw(),
                'DATE' => placeholder_date(),
            )));
        }

        return array(
            lorem_globalise(do_lorem_template('BLOCK_SIDE_NEWS', array(
                'BLOG' => true,
                'TITLE' => lorem_phrase(),
                'CONTENT' => $contents,
                'SUBMIT_URL' => placeholder_url(),
                'ARCHIVE_URL' => placeholder_url(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_side_news_categories()
    {
        require_lang('news');

        $categories = array();
        foreach (placeholder_array() as $k => $v) {
            $categories[] = array(
                'URL' => placeholder_url(),
                'NAME' => lorem_phrase(),
                'COUNT' => placeholder_number(),
            );
        }
        return array(
            lorem_globalise(do_lorem_template('BLOCK_SIDE_NEWS_CATEGORIES', array(
                'CATEGORIES' => $categories,
                'PRE' => '',
                'POST' => '',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_bottom_news()
    {
        require_lang('news');

        $contents_arr = array();
        foreach (placeholder_array() as $k => $v) {
            $contents_arr[] = array(
                'DATE' => placeholder_date(),
                'FULL_URL' => placeholder_url(),
                'NEWS_TITLE' => lorem_word(),
            );
        }
        return array(
            lorem_globalise(do_lorem_template('BLOCK_BOTTOM_NEWS', array(
                'BLOG' => true,
                'POSTS' => $contents_arr,
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__news_full_screen()
    {
        require_lang('news');

        require_javascript('editing');

        $tags = array();
        foreach (placeholder_array() as $k => $v) {
            $tags[] = array(
                'TAG' => lorem_word(),
                'LINK_LIMITEDSCOPE' => placeholder_url(),
                'LINK_FULLSCOPE' => placeholder_url(),
            );
        }

        $comment_details = do_lorem_template('COMMENTS_POSTING_FORM', array(
            'JOIN_BITS' => lorem_phrase_html(),
            'USE_CAPTCHA' => false,
            'EMAIL_OPTIONAL' => true,
            'POST_WARNING' => '',
            'COMMENT_TEXT' => '',
            'GET_EMAIL' => true,
            'GET_TITLE' => true,
            'EM' => placeholder_emoticon_chooser(),
            'DISPLAY' => 'block',
            'COMMENT_URL' => placeholder_url(),
            'TITLE' => lorem_phrase(),
            'MAKE_POST' => true,
            'CREATE_TICKET_MAKE_POST' => true,
            'FIRST_POST_URL' => '',
            'FIRST_POST' => '',
            'NAME' => 'field',
        ));

        return array(
            lorem_globalise(do_lorem_template('NEWS_ENTRY_SCREEN', array(
                'ID' => placeholder_id(),
                'CATEGORY_ID' => placeholder_id(),
                'BLOG' => true,
                '_TITLE' => lorem_phrase(),
                'TAGS' => placeholder_tags(),
                'CATEGORIES' => placeholder_array(),
                'NEWSLETTER_URL' => addon_installed('newsletter') ? placeholder_url() : '',
                'ADD_DATE_RAW' => placeholder_date_raw(),
                'EDIT_DATE_RAW' => '',
                'SUBMITTER' => placeholder_id(),
                'CATEGORY' => lorem_word(),
                'IMG' => placeholder_image(),
                'TITLE' => lorem_title(),
                'VIEWS' => '3',
                'COMMENT_DETAILS' => $comment_details,
                'RATING_DETAILS' => lorem_sentence(),
                'TRACKBACK_DETAILS' => lorem_sentence(),
                'DATE' => placeholder_date(),
                'AUTHOR' => lorem_word(),
                'AUTHOR_URL' => placeholder_url(),
                'NEWS_FULL' => lorem_paragraph(),
                'NEWS_FULL_PLAIN' => lorem_sentence(),
                'EDIT_URL' => placeholder_url(),
                'ARCHIVE_URL' => placeholder_url(),
                'SUBMIT_URL' => placeholder_url(),
                'WARNING_DETAILS' => '',
            )), null, '', true)
        );
    }
}
