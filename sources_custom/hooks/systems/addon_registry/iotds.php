<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array(
            'iotds_addon',
            'iotds_addon_thumbs',
        );
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
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'New Features';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Choose and display Images Of The Day.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_featured',
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
            'requires' => array(),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images_custom/icons/48x48/menu/rich_content/iotds.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images_custom/icons/24x24/menu/rich_content/iotds.png',
            'themes/default/images_custom/icons/48x48/menu/rich_content/iotds.png',
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
            'templates/IOTD_ADMIN_CHOOSE_SCREEN.tpl' => 'administrative__iotd_admin_choose_screen',
            'templates/BLOCK_MAIN_IOTD.tpl' => 'block_main_iotd',
            'templates/IOTD_BOX.tpl' => 'iotd_view_screen_iotd',
            'templates/IOTD_ENTRY_SCREEN.tpl' => 'iotd_view_screen'
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__iotd_admin_choose_screen()
    {
        $map = array(
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
        );
        $current_iotd = do_lorem_template('IOTD_BOX', $map + array('ID' => placeholder_id() . '_1'));
        $unused_iotd = do_lorem_template('IOTD_BOX', $map + array('ID' => placeholder_id() . '_2'));
        $used_iotd = do_lorem_template('IOTD_BOX', $map + array('ID' => placeholder_id() . '_3'));

        return array(
            lorem_globalise(do_lorem_template('IOTD_ADMIN_CHOOSE_SCREEN', array(
                'SHOWING_OLD' => lorem_phrase(),
                'TITLE' => lorem_title(),
                'USED_URL' => placeholder_url(),
                'CURRENT_IOTD' => $current_iotd,
                'UNUSED_IOTD' => $unused_iotd,
                'USED_IOTD' => $used_iotd,
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
    public function tpl_preview__block_main_iotd()
    {
        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_IOTD', array(
                'SUBMITTER' => placeholder_number(),
                'THUMB_URL' => placeholder_image_url(),
                'FULL_URL' => placeholder_image_url(),
                'I_TITLE' => lorem_phrase(),
                'CAPTION' => lorem_paragraph(),
                'IMAGE' => placeholder_image(),
                'VIEW_URL' => placeholder_url(),
                'SUBMIT_URL' => placeholder_url(),
                'ARCHIVE_URL' => placeholder_url(),
                'ID' => placeholder_id(),
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
    public function tpl_preview__iotd_view_screen_iotd()
    {
        $content = new Tempcode();
        $content->attach(do_lorem_template('IOTD_BOX', array(
            'SUBMITTER' => placeholder_id(),
            'ID' => placeholder_id(),
            'VIEWS' => placeholder_number(),
            'THUMB' => placeholder_image(),
            'DATE' => placeholder_date(),
            'DATE_RAW' => placeholder_date_raw(),
            'URL' => placeholder_url(),
            'CAPTION' => lorem_phrase(),
        )));

        return array(
            lorem_globalise(do_lorem_template('PAGINATION_SCREEN', array(
                'TITLE' => lorem_title(),
                'CONTENT' => $content,
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
    public function tpl_preview__iotd_view_screen()
    {
        require_lang('cns');
        require_lang('trackbacks');

        if (addon_installed('captcha')) {
            require_lang('captcha');
        }

        require_javascript('editing');

        $trackbacks = new Tempcode();
        foreach (placeholder_array(1) as $k => $v) {
            $trackbacks->attach(do_lorem_template('TRACKBACK', array(
                'ID' => placeholder_id(),
                'TIME_RAW' => placeholder_date_raw(),
                'TIME' => placeholder_date(),
                'URL' => placeholder_url(),
                'TITLE' => lorem_phrase(),
                'EXCERPT' => lorem_paragraph(),
                'NAME' => placeholder_id(),
            )));
        }
        $trackback_details = do_lorem_template('TRACKBACK_WRAPPER', array(
            'TRACKBACKS' => $trackbacks,
            'TRACKBACK_FEEDBACK_TYPE' => placeholder_id(),
            'TRACKBACK_ID' => placeholder_id(),
            'TRACKBACK_TITLE' => lorem_phrase(),
        ));

        $rating_details = new Tempcode();

        $review_titles = array();
        $review_titles[] = array(
            'REVIEW_TITLE' => lorem_word(),
            'REVIEW_RATING' => make_string_tempcode(float_format(10.0))
        );

        $comments = '';

        $form = do_lorem_template('COMMENTS_POSTING_FORM', array(
            'JOIN_BITS' => lorem_phrase_html(),
            'FIRST_POST_URL' => placeholder_url(),
            'FIRST_POST' => lorem_paragraph_html(),
            'TYPE' => 'downloads',
            'ID' => placeholder_id(),
            'REVIEW_RATING_CRITERIA' => $review_titles,
            'USE_CAPTCHA' => addon_installed('captcha'),
            'GET_EMAIL' => false,
            'EMAIL_OPTIONAL' => true,
            'GET_TITLE' => true,
            'POST_WARNING' => do_lang('POST_WARNING'),
            'COMMENT_TEXT' => get_option('comment_text'),
            'EM' => placeholder_emoticon_chooser(),
            'DISPLAY' => 'block',
            'COMMENT_URL' => placeholder_url(),
            'TITLE' => lorem_word(),
            'MAKE_POST' => true,
            'CREATE_TICKET_MAKE_POST' => true,
            'NAME' => 'field',
        ));

        $comment_details = do_lorem_template('COMMENTS_WRAPPER', array(
            'TYPE' => lorem_phrase(),
            'ID' => placeholder_id(),
            'REVIEW_RATING_CRITERIA' => $review_titles,
            'AUTHORISED_FORUM_URL' => placeholder_url(),
            'FORM' => $form,
            'COMMENTS' => $comments,
            'SORT' => 'relevance',
        ));

        return array(
            lorem_globalise(do_lorem_template('IOTD_ENTRY_SCREEN', array(
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
            )), null, '', true)
        );
    }
}
