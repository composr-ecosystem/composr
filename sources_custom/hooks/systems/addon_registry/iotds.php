<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Hook class.
 */
class Hook_addon_registry_iotds
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return [
            'iotds_addon',
            'iotds_addon_thumbs',
        ];
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'New Features';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Choose and display Images Of The Day.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return [
            'tut_featured',
        ];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return [
            'requires' => [],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images_custom/icons/menu/rich_content/iotds.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'sources_custom/hooks/systems/privacy/iotds.php',
            'themes/default/images_custom/icons/menu/rich_content/iotds.svg',
            'themes/default/images_custom/icons/menu/rich_content/index.html',
            'themes/default/images_custom/icons_monochrome/menu/rich_content/iotds.svg',
            'themes/default/images_custom/icons_monochrome/menu/rich_content/index.html',
            'sources_custom/iotds2.php',
            'sources_custom/hooks/systems/notifications/iotd_chosen.php',
            'sources_custom/hooks/systems/config/iotd_update_time.php',
            'sources_custom/hooks/systems/config/points_ADD_IOTD.php',
            'sources_custom/hooks/systems/config/points_CHOOSE_IOTD.php',
            'sources_custom/hooks/systems/content_meta_aware/iotd.php',
            'sources_custom/hooks/systems/commandr_fs/iotds.php',
            'sources_custom/hooks/systems/addon_registry/iotds.php',
            'sources_custom/hooks/modules/admin_setupwizard/iotds.php',
            'sources_custom/hooks/modules/admin_import_types/iotds.php',
            'sources_custom/blocks/main_iotd.php',
            'sources_custom/hooks/blocks/main_staff_checklist/iotds.php',
            'sources_custom/hooks/modules/search/iotds.php',
            'sources_custom/hooks/systems/page_groupings/iotds.php',
            'sources_custom/hooks/systems/rss/iotds.php',
            'sources_custom/hooks/systems/trackback/iotds.php',
            'sources_custom/iotds.php',
            'sources_custom/hooks/systems/preview/iotd.php',
            'sources_custom/hooks/systems/sitemap/iotd.php',
            'themes/default/templates_custom/IOTD_BOX.tpl',
            'themes/default/templates_custom/IOTD_ENTRY_SCREEN.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_IOTD.tpl',
            'themes/default/templates_custom/IOTD_ADMIN_CHOOSE_SCREEN.tpl',
            'themes/default/css_custom/iotds.css',
            'cms/pages/modules_custom/cms_iotds.php',
            'lang_custom/EN/iotds.ini',
            'site/pages/modules_custom/iotds.php',
            'uploads/iotds_addon/index.html',
            'uploads/iotds_addon_thumbs/index.html',
            'sources_custom/hooks/systems/config/search_iotds.php',
            'themes/default/javascript_custom/iotds.js',
            'sources_custom/hooks/systems/actionlog/iotds.php',
        ];
    }

    /**
     * Get mapping between template names and the method of this class that can render a preview of them.
     *
     * @return array The mapping
     */
    public function tpl_previews()
    {
        return [
            'templates/IOTD_ADMIN_CHOOSE_SCREEN.tpl' => 'administrative__iotd_admin_choose_screen',
            'templates/BLOCK_MAIN_IOTD.tpl' => 'block_main_iotd',
            'templates/IOTD_BOX.tpl' => 'iotd_view_screen_iotd',
            'templates/IOTD_ENTRY_SCREEN.tpl' => 'iotd_view_screen',
        ];
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declarative.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__iotd_admin_choose_screen()
    {
        $map = [
            'IS_CURRENT' => placeholder_number(),
            'THUMB_URL' => placeholder_image_url(),
            'IMAGE_URL' => placeholder_image_url(),
            'VIEWS' => placeholder_number(),
            'THUMB' => placeholder_image(),
            'DATE' => placeholder_date(),
            'DATE_RAW' => placeholder_date_raw(),
            'VIEW_URL' => placeholder_url(),
            'EDIT_URL' => placeholder_url(),
            'DELETE_URL' => placeholder_url(),
            'CHOOSE_URL' => placeholder_url(),
            'I_TITLE' => lorem_phrase(),
            'CAPTION' => lorem_paragraph(),
            'SUBMITTER' => placeholder_id(),
            'USERNAME' => lorem_word(),
            'GIVE_CONTEXT' => true,
        ];
        $current_iotd = do_lorem_template('IOTD_BOX', $map + ['ID' => placeholder_id() . '_1']);
        $unused_iotd = do_lorem_template('IOTD_BOX', $map + ['ID' => placeholder_id() . '_2']);
        $used_iotd = do_lorem_template('IOTD_BOX', $map + ['ID' => placeholder_id() . '_3']);

        return [
            lorem_globalise(do_lorem_template('IOTD_ADMIN_CHOOSE_SCREEN', [
                'SHOWING_OLD' => lorem_phrase(),
                'TITLE' => lorem_title(),
                'USED_URL' => placeholder_url(),
                'CURRENT_IOTD' => $current_iotd,
                'UNUSED_IOTD' => $unused_iotd,
                'USED_IOTD' => $used_iotd,
            ]), null, '', true)
        ];
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declarative.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_main_iotd()
    {
        return [
            lorem_globalise(do_lorem_template('BLOCK_MAIN_IOTD', [
                'BLOCK_ID' => lorem_word(),
                'SUBMITTER' => placeholder_id(),
                'THUMB_URL' => placeholder_image_url(),
                'FULL_URL' => placeholder_image_url(),
                'I_TITLE' => lorem_phrase(),
                'CAPTION' => lorem_paragraph(),
                'IMAGE' => placeholder_image(),
                'VIEW_URL' => placeholder_url(),
                'SUBMIT_URL' => placeholder_url(),
                'ARCHIVE_URL' => placeholder_url(),
                'ID' => placeholder_id(),
            ]), null, '', true)
        ];
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declarative.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__iotd_view_screen_iotd()
    {
        $content = new Tempcode();
        $content->attach(do_lorem_template('IOTD_BOX', [
            'SUBMITTER' => placeholder_id(),
            'ID' => placeholder_id(),
            'VIEWS' => placeholder_number(),
            'THUMB' => placeholder_image(),
            'DATE' => placeholder_date(),
            'DATE_RAW' => placeholder_date_raw(),
            'URL' => placeholder_url(),
            'CAPTION' => lorem_phrase(),
        ]));

        return [
            lorem_globalise(do_lorem_template('PAGINATION_SCREEN', [
                'TITLE' => lorem_title(),
                'CONTENT' => $content,
            ]), null, '', true)
        ];
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declarative.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__iotd_view_screen()
    {
        require_lang('cns');
        require_lang('trackbacks');

        if (addon_installed('captcha')) {
            require_lang('captcha');
        }

        $trackbacks = new Tempcode();
        foreach (placeholder_array(1) as $k => $v) {
            $trackbacks->attach(do_lorem_template('TRACKBACK', [
                'ID' => placeholder_id(),
                '_DATE' => placeholder_date_raw(),
                'DATE' => placeholder_date(),
                'URL' => placeholder_url(),
                'TITLE' => lorem_phrase(),
                'EXCERPT' => lorem_paragraph(),
                'NAME' => placeholder_id(),
            ]));
        }
        $trackback_details = do_lorem_template('TRACKBACK_WRAPPER', [
            'TRACKBACKS' => $trackbacks,
            'TRACKBACK_PAGE' => placeholder_id(),
            'TRACKBACK_ID' => placeholder_id(),
            'TRACKBACK_TITLE' => lorem_phrase(),
        ]);

        $rating_details = new Tempcode();

        $review_titles = [];
        $review_titles[] = [
            'REVIEW_TITLE' => lorem_word(),
            'REVIEW_RATING' => make_string_tempcode(float_format(10.0))
        ];

        $comments = '';

        $form = do_lorem_template('COMMENTS_POSTING_FORM', [
            'TITLE' => lorem_phrase(),
            'JOIN_BITS' => lorem_phrase_html(),
            'USE_CAPTCHA' => false,
            'GET_EMAIL' => true,
            'EMAIL_OPTIONAL' => true,
            'GET_TITLE' => true,
            'TITLE_OPTIONAL' => true,
            'DEFAULT_TITLE' => '',
            'POST_WARNING' => '',
            'RULES_TEXT' => '',
            'ATTACHMENTS' => null,
            'ATTACH_SIZE_FIELD' => null,
            'TRUE_ATTACHMENT_UI' => false,
            'EMOTICONS' => placeholder_emoticon_chooser(),
            'REVIEW_RATING_CRITERIA' => $review_titles,
            'EXPAND_TYPE' => 'expand',
            'DISPLAY' => 'block',
            'FIRST_POST_URL' => '',
            'FIRST_POST' => '',
            'COMMENT_URL' => placeholder_url(),
        ]);

        $comment_details = do_lorem_template('COMMENTS_WRAPPER', [
            'TYPE' => lorem_phrase(),
            'ID' => placeholder_id(),
            'REVIEW_RATING_CRITERIA' => $review_titles,
            'AUTHORISED_FORUM_URL' => placeholder_url(),
            'FORM' => $form,
            'COMMENTS' => $comments,
            'SORT' => 'relevance',
        ]);

        return [
            lorem_globalise(do_lorem_template('IOTD_ENTRY_SCREEN', [
                'TITLE' => lorem_title(),
                'SUBMITTER' => placeholder_id(),
                'I_TITLE' => lorem_phrase(),
                'CAPTION' => lorem_phrase(),
                'DATE_RAW' => placeholder_date_raw(),
                'ADD_DATE_RAW' => placeholder_date_raw(),
                'EDIT_DATE_RAW' => placeholder_date_raw(),
                'DATE' => placeholder_date(),
                'ADD_DATE' => placeholder_date(),
                'EDIT_DATE' => placeholder_date(),
                'VIEWS' => placeholder_number(),
                'TRACKBACK_DETAILS' => $trackback_details,
                'RATING_DETAILS' => $rating_details,
                'COMMENT_DETAILS' => $comment_details,
                'EDIT_URL' => placeholder_url(),
                'URL' => placeholder_image_url(),
            ]), null, '', true)
        ];
    }
}
