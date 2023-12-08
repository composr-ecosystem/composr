<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_pointstore_community_billboard
{
    /**
     * Standard pointstore item initialisation function.
     */
    public function init()
    {
        require_lang('community_billboard');
    }

    /**
     * Standard pointstore item "shop front" function.
     *
     * @return array The "shop fronts"
     */
    public function info()
    {
        if (!$GLOBALS['SITE_DB']->table_exists('community_billboard')) {
            return array();
        }

        if (get_option('is_on_community_billboard_buy') == '1') {
            $community_billboard_url = build_url(array('page' => '_SELF', 'type' => 'community_billboard', 'id' => 'community_billboard'), '_SELF');

            return array(do_template('POINTSTORE_COMMUNITY_BILLBOARD_2', array('_GUID' => 'c4e067ab5eca19875f8d92a276cfcf05', 'COMMUNITY_BILLBOARD_URL' => $community_billboard_url)));
        }
        return array();
    }

    /**
     * Standard stage of pointstore item purchase.
     *
     * @return Tempcode The UI
     */
    public function community_billboard()
    {
        if (get_option('is_on_community_billboard_buy') == '0') {
            return new Tempcode();
        }

        $title = get_screen_title('TITLE_NEWCOMMUNITY_BILLBOARD');

        $queue = $GLOBALS['SITE_DB']->query_select_value('community_billboard', 'SUM(days) AS days', array('activation_time' => null));
        if (is_null($queue)) {
            $queue = 0;
        }
        $community_billboard_url = build_url(array('page' => '_SELF', 'type' => '_community_billboard', 'id' => 'community_billboard'), '_SELF');
        $cost = intval(get_option('community_billboard'));

        return do_template('POINTSTORE_COMMUNITY_BILLBOARD_SCREEN', array('_GUID' => '92d51c5b87745c31397d9165595262d3', 'TITLE' => $title, 'COMMUNITY_BILLBOARD_URL' => $community_billboard_url, 'QUEUE' => integer_format($queue), 'COST' => integer_format($cost)));
    }

    /**
     * Standard stage of pointstore item purchase.
     *
     * @return Tempcode The UI
     */
    public function _community_billboard()
    {
        if (get_option('is_on_community_billboard_buy') == '0') {
            return new Tempcode();
        }

        $title = get_screen_title('TITLE_NEWCOMMUNITY_BILLBOARD');

        // Build up fields
        require_code('form_templates');
        $fields = new Tempcode();
        $fields->attach(form_input_line_comcode(do_lang_tempcode('MESSAGE'), do_lang_tempcode('MESSAGE_DESCRIPTION'), 'message', '', true));
        $fields->attach(form_input_integer(do_lang_tempcode('NUMBER_DAYS'), do_lang_tempcode('NUMBER_DAYS_DESCRIPTION'), 'days', 1, true));

        $price = intval(get_option('community_billboard'));
        $text = paragraph(do_lang_tempcode('COMMUNITY_BILLBOARD_GUIDE', escape_html(integer_format($price))));

        // Return template
        $post_url = build_url(array('page' => '_SELF', 'type' => '__community_billboard', 'id' => 'community_billboard'), '_SELF');
        return do_template('FORM_SCREEN', array('_GUID' => '3584ba6a16c9a51829dc3b25b58067f6', 'HIDDEN' => '', 'TITLE' => $title, 'ACTION' => do_lang_tempcode('TITLE_NEWCOMMUNITY_BILLBOARD'), 'TEXT' => $text, 'URL' => $post_url, 'SUBMIT_ICON' => 'buttons__proceed', 'SUBMIT_NAME' => do_lang_tempcode('PURCHASE'), 'FIELDS' => $fields));
    }

    /**
     * Standard stage of pointstore item purchase.
     *
     * @return Tempcode The UI
     */
    public function __community_billboard()
    {
        if (get_option('is_on_community_billboard_buy') == '0') {
            return new Tempcode();
        }

        $title = get_screen_title('TITLE_NEWCOMMUNITY_BILLBOARD');

        // So we don't need to call this big long ugly name...
        $days = post_param_integer('days');
        $message = post_param_string('message');

        $member_id = get_member();
        $points_left = available_points($member_id);

        // First we need to know the price of the number of days we ordered.
        // After that will be compare that price with our users current number of points.
        $day_price = intval(get_option('community_billboard'));
        $total = $day_price * $days;

        if (!($days >= 1)) {
            return warn_screen($title, do_lang_tempcode('COMMUNITY_BILLBOARD_POSITIVE_DAYS'));
        }

        if (($points_left < $total) && (!has_privilege(get_member(), 'give_points_self'))) {
            return warn_screen($title, do_lang_tempcode('COMMUNITY_BILLBOARD_LACK_POINTS', escape_html(integer_format($days)), escape_html(integer_format($total)), array(integer_format($points_left))));
        }

        // The order screen...
        $action = do_lang_tempcode('CONFIRM_COMMUNITY_BILLBOARD', escape_html(integer_format($days)));
        $keep = form_input_hidden('message', $message);
        $keep->attach(form_input_hidden('days', strval($days)));
        $proceed_url = build_url(array('page' => '_SELF', 'type' => '___community_billboard', 'id' => 'community_billboard'), '_SELF');

        return do_template('POINTSTORE_CONFIRM_SCREEN', array(
            '_GUID' => 'e2b139122d95af6a1930e84b41609145',
            'TITLE' => $title,
            'KEEP' => $keep,
            'ACTION' => $action,
            'COST' => integer_format($total),
            'POINTS_AFTER' => integer_format($points_left - $total),
            'PROCEED_URL' => $proceed_url,
            'MESSAGE' => comcode_to_tempcode($message),
            'CANCEL_URL' => build_url(array('page' => '_SELF'), '_SELF'),
        ));
    }

    /**
     * Standard stage of pointstore item purchase.
     *
     * @return Tempcode The UI
     */
    public function ___community_billboard()
    {
        if (get_option('is_on_community_billboard_buy') == '0') {
            return new Tempcode();
        }

        $title = get_screen_title('TITLE_NEWCOMMUNITY_BILLBOARD');

        // Define variables
        $member_id = get_member();
        $message = post_param_string('message');
        $days = post_param_integer('days');
        $points_left = available_points($member_id);

        // First we need to know the price of the number of days we ordered. After that, compare that price with our users current number of points.
        $day_price = intval(get_option('community_billboard'));
        $total = $day_price * $days;

        if (($points_left < $total) && (!has_privilege(get_member(), 'give_points_self'))) {
            return warn_screen($title, do_lang_tempcode('COMMUNITY_BILLBOARD_LACK_POINTS', escape_html(integer_format($days)), escape_html(integer_format($total)), escape_html(integer_format($points_left))));
        }

        // Add this to the database
        $map = array(
            'notes' => '',
            'activation_time' => null,
            'active_now' => 0,
            'member_id' => $member_id,
            'days' => $days,
            'order_time' => time(),
        );
        $map += insert_lang_comcode('the_message', $message, 2);
        $GLOBALS['SITE_DB']->query_insert('community_billboard', $map);

        // Mail off the notice
        require_code('notifications');
        $_url = build_url(array('page' => 'admin_community_billboard'), 'adminzone', null, false, false, true);
        $manage_url = $_url->evaluate();
        dispatch_notification('pointstore_request_community_billboard', null, do_lang('TITLE_NEWCOMMUNITY_BILLBOARD', null, null, null, get_site_default_lang()), do_notification_lang('MAIL_COMMUNITY_BILLBOARD_TEXT', $message, comcode_escape($manage_url), null, get_site_default_lang()));

        // Now, deduct the points from our member's account
        require_code('points2');
        charge_member($member_id, $total, do_lang('PURCHASED_COMMUNITY_BILLBOARD'));

        $url = build_url(array('page' => '_SELF', 'type' => 'browse'), '_SELF');
        return redirect_screen($title, $url, do_lang_tempcode('ORDER_COMMUNITY_BILLBOARD_DONE'));
    }
}
