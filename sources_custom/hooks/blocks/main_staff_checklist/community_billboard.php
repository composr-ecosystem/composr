<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_checklist_community_billboard
{
    /**
     * Find items to include on the staff checklist.
     *
     * @return array An array of tuples: The task row to show, the number of seconds until it is due (or null if not on a timer), the number of things to sort out (or null if not on a queue), The name of the config option that controls the schedule (or null if no option)
     */
    public function run() : array
    {
        if (!addon_installed('community_billboard')) {
            return [];
        }

        require_lang('community_billboard');

        $num_queue = $this->get_num_community_billboard_queue();

        $rows = $GLOBALS['SITE_DB']->query_select('community_billboard', ['activation_time', 'days'], ['active_now' => 1]);
        if ($rows === null) {
            return [];
        }
        $seconds_due_in = null;
        if (array_key_exists(0, $rows)) {
            $activation_time = $rows[0]['activation_time'];
            $days = $rows[0]['days'];

            $date = $activation_time + $days * 24 * 60 * 60;

            $seconds_due_in = $date - time();
            $status = ($seconds_due_in <= 0) ? 0 : 1;
        } else {
            $status = ($num_queue == 0) ? 1 : 0; // If none set, but one waiting, task is not done

            if ($num_queue != 0) {
                $seconds_due_in = 0;
            }
        }

        $_status = ($status == 0) ? do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_0') : do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_1');

        $url = build_url(['page' => 'admin_community_billboard', 'type' => 'browse'], get_module_zone('admin_community_billboard'));
        $num_queue = $this->get_num_community_billboard_queue();
        list($info, $seconds_due_in) = staff_checklist_time_ago_and_due($seconds_due_in);
        $info->attach(do_lang_tempcode('NUM_QUEUE', escape_html(integer_format($num_queue, 0))));
        $tpl = do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM', [
            '_GUID' => '820e0e3cd80754dc7dfd9a0d05a43ec0',
            'URL' => $url,
            'STATUS' => $_status,
            'TASK' => do_lang_tempcode('CHOOSE_COMMUNITY_BILLBOARD'),
            'INFO' => $info,
        ]);
        return [[$tpl, $seconds_due_in, null, null]];
    }

    /**
     * Get the number of community_billboard community billboard messages in the queue.
     *
     * @return integer Number in queue
     */
    public function get_num_community_billboard_queue() : int
    {
        $c = $GLOBALS['SITE_DB']->query_select_value('community_billboard', 'COUNT(*)', ['activation_time' => null]);
        if ($c === null) {
            return 0;
        }
        return $c;
    }
}
