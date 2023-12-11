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
 * Hook class.
 */
class Hook_checklist_iotds
{
    /**
     * Find items to include on the staff checklist.
     *
     * @return array An array of tuples: The task row to show, the number of seconds until it is due (or null if not on a timer), the number of things to sort out (or null if not on a queue), The name of the config option that controls the schedule (or null if no option).
     */
    public function run()
    {
        if (!addon_installed('iotds')) {
            return array();
        }

        if (!$GLOBALS['SITE_DB']->table_exists('iotd')) {
            return array();
        }

        if (get_option('iotd_update_time') == '' || get_option('iotd_update_time') == '0') {
            return array();
        }

        require_lang('iotds');

        $date = $GLOBALS['SITE_DB']->query_select_value_if_there('iotd', 'date_and_time', array('is_current' => 1));

        $limit_hours = intval(get_option('iotd_update_time'));

        $seconds_ago = mixed();
        if (!is_null($date)) {
            $seconds_ago = time() - $date;
            $status = ($seconds_ago > $limit_hours * 60 * 60) ? 0 : 1;
        } else {
            $status = 0;
        }

        $_status = ($status == 0) ? do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_0') : do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_1');

        require_code('config2');
        $config_url = config_option_url('iotd_update_time');

        $url = build_url(array('page' => 'cms_iotds', 'type' => 'edit'), get_module_zone('cms_iotds'));
        $num_queue = $this->get_num_iotd_queue();
        list($info, $seconds_due_in) = staff_checklist_time_ago_and_due($seconds_ago, $limit_hours);
        $info->attach(do_lang_tempcode('NUM_QUEUE', escape_html(integer_format($num_queue))));
        $tpl = do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM', array('_GUID' => '5c55aed7bedca565c8aa553548b88e64', 'CONFIG_URL' => $config_url, 'URL' => $url, 'STATUS' => $_status, 'TASK' => do_lang_tempcode('PRIVILEGE_choose_iotd'), 'INFO' => $info));
        return array(array($tpl, $seconds_due_in, null, 'iotd_update_time'));
    }

    /**
     * Get the number of IOTDs in the queue.
     *
     * @return integer Number in queue
     */
    public function get_num_iotd_queue()
    {
        $c = $GLOBALS['SITE_DB']->query_select_value_if_there('iotd', 'COUNT(*)', array('is_current' => 0, 'used' => 0));
        if (is_null($c)) {
            return 0;
        }
        return $c;
    }
}
