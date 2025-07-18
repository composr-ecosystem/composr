<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/**
 * Hook class.
 */
class Hook_cron_sitemap
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        if (!is_guest()) {
            return; // Bad idea
        }

        $time = time();
        $last_time = intval(get_value('last_sitemap_time_calc', null, true));

        if (get_value('sitemap_building_in_progress', null, true) == '1' && $last_time > time() - 60 * 60 * 24 * 3/*in case it stalled a few days back - force a re-try*/) {
            return;
        }

        set_value('last_sitemap_time_calc', strval($time), true);

        require_lang('menus');

        require_code('tasks');
        call_user_func_array__long_task(do_lang('GENERATE_SITEMAP'), get_screen_title('GENERATE_SITEMAP'), 'sitemap', array(), false, false, false);
    }
}
