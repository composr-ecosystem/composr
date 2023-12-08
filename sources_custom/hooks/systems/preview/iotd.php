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
class Hook_preview_iotd
{
    /**
     * Find whether this preview hook applies.
     *
     * @return array Triplet: Whether it applies, the attachment ID type, whether the forum DB is used [optional]
     */
    public function applies()
    {
        require_code('uploads');
        $applies = (get_page_name() == 'cms_iotds') && ((get_param_string('type') == '_edit') || (get_param_string('type') == 'add')) && ((is_plupload()) || (count($_FILES) != 0));
        return array($applies, null, false);
    }

    /**
     * Run function for preview hooks.
     *
     * @return array A pair: The preview, the updated post Comcode
     */
    public function run()
    {
        require_code('uploads');
        require_code('images');

        require_lang('iotds');

        $keep_gps = (get_value('keep_gallery_gps', '0') == '1');
        set_images_cleanup_pipeline_settings(IMG_RECOMPRESS_LOSSLESS, null, null, !$keep_gps);
        $filename = '';
        $thumb_url = '';
        require_code('themes2');
        $url = post_param_image('image', 'uploads/iotds_addon', null, true, false, $filename, $thumb_url);
        reset_images_cleanup_pipeline_settings();

        if ($url == '') {
            if (!is_null(post_param_integer('id', null))) {
                $rows = $GLOBALS['SITE_DB']->query_select('iotds', array('url', 'thumb_url'), array('id' => post_param_integer('id')), '', 1);
                $urls = $rows[0];

                $url = $urls['url'];
                $thumb_url = $urls['thumb_url'];
            } else {
                warn_exit(do_lang_tempcode('IMPROPERLY_FILLED_IN_UPLOAD'));
            }
        }

        $thumb_url = url_is_local($thumb_url) ? (get_custom_base_url() . '/' . $thumb_url) : $thumb_url;
        $url = url_is_local($url) ? (get_custom_base_url() . '/' . $url) : $url;
        $thumb = do_image_thumb($thumb_url, '');

        $choose_url = mixed();
        $delete_url = mixed();
        $edit_url = mixed();

        $title = comcode_to_tempcode(post_param_string('title', ''));
        $caption = comcode_to_tempcode(post_param_string('caption', ''));
        $date = get_timezoned_date(time());

        $submitter = get_member();
        $username = $GLOBALS['FORUM_DRIVER']->get_username($submitter);

        $view_url = mixed();

        $preview = do_template('IOTD_BOX', array(
            '_GUID' => 'a6479902d2cd7b4119be7159147e0a0b',
            'VIEWS' => '',
            'THUMB' => $thumb,
            'DATE' => $date,
            'DATE_RAW' => strval(time()),
            'IS_CURRENT' => '0',
            'THUMB_URL' => $thumb_url,
            'VIEW_URL' => $view_url,
            'ID' => '',
            'EDIT_URL' => $edit_url,
            'DELETE_URL' => $delete_url,
            'CHOOSE_URL' => $choose_url,
            'I_TITLE' => $title,
            'CAPTION' => $caption,
            'SUBMITTER' => strval($submitter),
            'USERNAME' => $username,
            'GIVE_CONTEXT' => true,
        ));

        return array($preview, null);
    }
}
