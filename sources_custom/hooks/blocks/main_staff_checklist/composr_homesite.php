<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite
 */

/**
 * Hook class.
 */
class Hook_checklist_composr_homesite
{
    /**
     * Find items to include on the staff checklist.
     *
     * @return array An array of tuples: The task row to show, the number of seconds until it is due (or null if not on a timer), the number of things to sort out (or null if not on a queue), The name of the config option that controls the schedule (or null if no option)
     */
    public function run() : array
    {
        if (!addon_installed('composr_homesite')) {
            return [];
        }

        require_lang('composr_homesite');

        list($num_relayed_errors, $num_relayed_errors_count) = $this->get_num_relayed_errors();
        if ($num_relayed_errors >= 1) {
            $status = 0;
        } else {
            $status = 1;
        }
        $_status = ($status == 0) ? do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_0') : do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_1');

        $url = build_url(['page' => 'admin_cmsusers', 'type' => 'errors'], get_module_zone('admin_cmsusers'));

        $tpl = do_template('BLOCK_MAIN_STAFF_CHECKLIST_ITEM', [
            '_GUID' => '9caec63ef20ae396f8982f63a9c345bb',
            'URL' => '',
            'STATUS' => $_status,
            'TASK' => do_lang_tempcode('NAG_CMS_SITE_ERRORS', escape_html_tempcode($url)),
            'INFO' => do_lang_tempcode('_NAG_CMS_SITE_ERRORS', escape_html(integer_format($num_relayed_errors, 0)), escape_html(integer_format($num_relayed_errors_count, 0))),
        ]);

        return [[$tpl, null, $num_relayed_errors, null]];
    }

    /**
     * Get the number of relayed errors.
     *
     * @return array A pair: Number of major things, number of minor things
     */
    public function get_num_relayed_errors() : array
    {
        $_sum = $GLOBALS['SITE_DB']->query_select_value('relayed_errors', 'COUNT(*)', ['resolved' => 0]);
        $sum = @intval($_sum);
        $_sum2 = $GLOBALS['SITE_DB']->query_select_value('relayed_errors', 'SUM(error_count)', ['resolved' => 0]);
        $sum2 = @intval($_sum2);

        return [$sum, $sum2];
    }
}
