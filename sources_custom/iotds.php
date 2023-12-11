<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Render an IOTD box.
 *
 * @param  array $myrow The IOTD row
 * @param  ID_TEXT $zone The zone the iotds module is in
 * @param  boolean $include_manage_links Whether to include extra management links (e.g. editing, choosing, archive, etc)
 * @param  boolean $give_context Whether to include context (i.e. say WHAT this is, not just show the actual content)
 * @param  ID_TEXT $guid Overridden GUID to send to templates (blank: none)
 * @return Tempcode The rendered box
 */
function render_iotd_box($myrow, $zone = '_SEARCH', $include_manage_links = false, $give_context = true, $guid = '')
{
    if (is_null($myrow)) { // Should never happen, but we need to be defensive
        return new Tempcode();
    }

    require_lang('iotds');
    require_code('images');

    if ($include_manage_links) {
        $choose_url = build_url(array('page' => 'cms_iotds', 'type' => '_choose'), get_module_zone('cms_iotds'));
        $delete_url = build_url(array('page' => 'cms_iotds', 'type' => '_delete'), get_module_zone('cms_iotds'));
        $edit_url = build_url(array('page' => 'cms_iotds', 'type' => '_edit', 'id' => $myrow['id']), get_module_zone('cms_iotds'));
    } else {
        $choose_url = mixed();
        $delete_url = mixed();
        $edit_url = mixed();
    }

    $just_iotd_row = db_map_restrict($myrow, array('id', 'i_title', 'caption'));

    $i_title = get_translated_tempcode('iotd', $just_iotd_row, 'i_title');
    $caption = get_translated_tempcode('iotd', $just_iotd_row, 'caption');
    $date = get_timezoned_date($myrow['date_and_time']);

    $submitter = $myrow['submitter'];
    $username = $GLOBALS['FORUM_DRIVER']->get_username($submitter);
    if ($username === null) {
        $username = do_lang('UNKNOWN');
    }

    $thumb_url = ensure_thumbnail($myrow['url'], $myrow['thumb_url'], 'iotds', 'iotd', $myrow['id']);
    $image_url = url_is_local($myrow['url']) ? (get_custom_base_url() . '/' . $myrow['url']) : $myrow['url'];
    $thumb = do_image_thumb($thumb_url, '');

    $view_url = build_url(array('page' => 'iotds', 'type' => 'view', 'id' => $myrow['id']), $zone);

    return do_template('IOTD_BOX', array(
        '_GUID' => ($guid != '') ? $guid : '01162a9cc9bb6c4d0e79715f30aa141e',
        'VIEWS' => integer_format($myrow['iotd_views']),
        'THUMB' => $thumb,
        'DATE' => $date,
        'DATE_RAW' => is_null($myrow['date_and_time']) ? '' : strval($myrow['date_and_time']),
        'IS_CURRENT' => ($myrow['is_current'] == 1),
        'THUMB_URL' => $thumb_url,
        'VIEW_URL' => $view_url,
        'ID' => strval($myrow['id']),
        'EDIT_URL' => $edit_url,
        'DELETE_URL' => $delete_url,
        'CHOOSE_URL' => $choose_url,
        'I_TITLE' => $i_title,
        'CAPTION' => $caption,
        'SUBMITTER' => strval($submitter),
        'USERNAME' => $username,
        'GIVE_CONTEXT' => $give_context,
    ));
}
