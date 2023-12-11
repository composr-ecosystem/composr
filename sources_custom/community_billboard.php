<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

/**
 * Add a community billboard message.
 *
 * @param  SHORT_TEXT $message The message
 * @param  integer $days The number of days to display for
 * @param  LONG_TEXT $notes Notes
 * @param  BINARY $validated Whether the message is for immediate use
 * @return AUTO_LINK The ID of the newly added message
 */
function add_community_billboard_message($message, $days, $notes, $validated)
{
    require_code('global4');
    prevent_double_submit('ADD_COMMUNITY_BILLBOARD', null, $message);

    $order_time = time();

    $map = array(
        'notes' => $notes,
        'activation_time' => null,
        'active_now' => 0,
        'days' => $days,
        'order_time' => $order_time,
        'member_id' => get_member(),
    );
    $map += insert_lang_comcode('the_message', $message, 2);
    $id = $GLOBALS['SITE_DB']->query_insert('community_billboard', $map, true);

    log_it('ADD_COMMUNITY_BILLBOARD', strval($id), $message);

    if ($validated == 1) {
        choose_community_billboard_message($id);
    }

    return $id;
}

/**
 * Edit a community billboard message.
 *
 * @param  AUTO_LINK $id The ID of what to edit
 * @param  SHORT_TEXT $message The message
 * @param  LONG_TEXT $notes Notes
 * @param  BINARY $validated Whether the message is the active message
 */
function edit_community_billboard_message($id, $message, $notes, $validated)
{
    $_message = $GLOBALS['SITE_DB']->query_select_value('community_billboard', 'the_message', array('id' => $id));
    log_it('EDIT_COMMUNITY_BILLBOARD', strval($id), $message);
    $map = array(
        'notes' => $notes,
        'active_now' => $validated,
    );
    $map += lang_remap_comcode('the_message', $_message, $message);
    $GLOBALS['SITE_DB']->query_update('community_billboard', $map, array('id' => $id), '', 1);
    if ($validated == 1) {
        choose_community_billboard_message($id);
    } else {
        persistent_cache_delete('COMMUNITY_BILLBOARD');
    }
}

/**
 * Delete a community billboard message.
 *
 * @param  AUTO_LINK $id The ID of the community billboard message to delete
 */
function delete_community_billboard_message($id)
{
    $message = $GLOBALS['SITE_DB']->query_select_value('community_billboard', 'the_message', array('id' => $id));
    $_message = get_translated_text($message);
    log_it('DELETE_COMMUNITY_BILLBOARD', strval($id), $_message);
    $GLOBALS['SITE_DB']->query_delete('community_billboard', array('id' => $id), '', 1);
    delete_lang($message);

    persistent_cache_delete('COMMUNITY_BILLBOARD'); // In case it was active
}

/**
 * Choose a community billboard message.
 *
 * @param  AUTO_LINK $id The ID of the community billboard message to choose
 */
function choose_community_billboard_message($id)
{
    $message = $GLOBALS['SITE_DB']->query_select_value('community_billboard', 'the_message', array('id' => $id));
    $message = get_translated_text($message);
    log_it('CHOOSE_COMMUNITY_BILLBOARD', strval($id), $message);
    $GLOBALS['SITE_DB']->query_update('community_billboard', array('active_now' => 0));
    $GLOBALS['SITE_DB']->query_update('community_billboard', array('activation_time' => time(), 'active_now' => 1), array('id' => $id), '', 1);

    persistent_cache_delete('COMMUNITY_BILLBOARD');
}

/**
 * Get a list of community billboard messages to choose from.
 *
 * @return Tempcode The list of community billboard messages
 */
function create_selection_list_community_billboard_messages()
{
    $rows = $GLOBALS['SITE_DB']->query_select('community_billboard', array('*'), null, 'ORDER BY order_time ASC');
    $time = $GLOBALS['SITE_DB']->query_select_value_if_there('community_billboard', 'activation_time', array('active_now' => 1));
    $out = new Tempcode();
    foreach ($rows as $row) {
        $selected = false;
        if ($row['activation_time'] < $time) {
            $status = do_lang_tempcode('USED_PREVIOUSLY');
        } elseif (is_null($row['activation_time'])) {
            $status = do_lang_tempcode('NOT_USED_PREVIOUSLY');
        } else {
            $status = do_lang_tempcode('CURRENT');
            $selected = true;
        }
        $message = get_translated_text($row['the_message']);
        $text = do_template('COMMUNITY_BILLBOARD_STORE_LIST_LINE', array('_GUID' => 'e4a5d54fa6cdc7848bd95bc017c60469', 'MESSAGE' => $message, 'STATUS' => $status));
        $out->attach(form_input_list_entry(strval($row['id']), $selected, protect_from_escaping($text->evaluate())));
    }
    return $out;
}
