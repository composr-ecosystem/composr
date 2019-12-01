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
class Hook_preview_iotd
{
    /**
     * Find whether this preview hook applies.
     *
     * @return array Triplet: Whether it applies, the attachment ID type (may be null), whether the forum DB is used [optional]
     */
    public function applies()
    {
        require_code('uploads');
        $applies = (addon_installed('iotds')) && (get_page_name() == 'cms_iotds') && ((get_param_string('type') == '_edit') || (get_param_string('type') == 'add')) && ((is_plupload()) || (!empty($_FILES)));
        return [$applies, null, false];
    }

    /**
     * Run function for preview hooks.
     *
     * @return array A pair: The preview, the updated post Comcode (may be null)
     */
    public function run()
    {
        require_code('uploads');
        require_code('images');

        require_lang('iotds');

        $filename = '';
        $thumb_url = '';
        require_code('themes2');
        $url = post_param_image('image', 'uploads/iotds_addon', null, true, false, $filename, $thumb_url);

        if ($url == '') {
            if (post_param_integer('id', null) !== null) {
                $rows = $GLOBALS['SITE_DB']->query_select('iotds', ['url', 'thumb_url'], ['id' => post_param_integer('id')], '', 1);
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

        $choose_url = null;
        $delete_url = null;
        $edit_url = null;

        $title = comcode_to_tempcode(post_param_string('title', ''));
        $caption = comcode_to_tempcode(post_param_string('caption', ''));
        $date = get_timezoned_date_time(time());

        $submitter = get_member();
        $username = $GLOBALS['FORUM_DRIVER']->get_username($submitter);

        $view_url = null;

        $preview = do_template('IOTD_BOX', [
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
        ]);

        return [$preview, null];
    }
}
