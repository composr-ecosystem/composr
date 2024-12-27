<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_cron_cmsusers
{
    /**
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  ?boolean $calculate_num_queued Calculate the number of items queued, if possible (null: the hook may decide / low priority)
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, ?bool $calculate_num_queued) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }

        return [
            'label' => 'Check site install status of CMS users',
            'num_queued' => 10, // We only check up to 10 at a time
            'minutes_between_runs' => 15,
            'enabled_by_default' => true,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        $start = 0;
        $max = 100;
        $count_checked = 0;
        do {
            // Get installed sites
            $select = 'website_url,id,MAX(hittime) AS hittime';
            $where = 'website_url NOT LIKE \'%.composr.info%\''; // LEGACY

            // Ignore local installs
            $where .= ' AND ' . db_string_not_equal_to('website_url', '%://localhost%') . ' AND ' . db_string_not_equal_to('website_url', '%://127.0.0.1%') . ' AND ' . db_string_not_equal_to('website_url', '%://192.168.%') . ' AND ' . db_string_not_equal_to('website_url', '%://10.0.%');

            $sql = 'SELECT ' . $select . ' FROM ' . get_table_prefix() . 'logged WHERE ' . $where . ' GROUP BY website_url';
            $rows = $GLOBALS['SITE_DB']->query($sql, $max, $start);

            foreach ($rows as $i => $r) {
                // That's enough for this Cron iteration
                if ($count_checked >= 10) {
                    break;
                }

                $days_since_adminzone_access = intval(round((time() - $r['hittime']) / 60 / 60 / 24));

                // Check if the site is still installed (but only once every day)
                $active = get_value_newer_than('testing__' . $r['website_url'] . '/data/installed.php', time() - 60 * 60 * 24, true);
                if ($active === null) {
                    $test_2 = cms_http_request($r['website_url'] . '/data/installed.php', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'byte_limit' => (1024 * 4), 'ua' => get_brand_base_url() . ' install stats', 'timeout' => 3.0]);
                    $count_checked++;

                    if ($test_2->data === 'Remove me!') { // The site wants us to remove them immediately, so let's do so.
                        $GLOBALS['SITE_DB']->query_delete('logged', ['id' => $r['id']]);
                        $GLOBALS['SITE_DB']->query_delete('may_feature', ['url' => $r['website_url']]);
                        delete_value('testing__' . $r['website_url'] . '/data/installed.php');
                        continue;
                    } elseif ($test_2->data === 'Yes') {
                        $active = do_lang('YES');
                    } else {
                        $active = @strval($test_2->message);
                        if ($active == '') { // File exists but did not explicitly say 'Yes' the software is still installed, so certainly it is not.
                            $active = do_lang('NO');
                        } else {
                            $active .= do_lang('CMS_WHEN_CHECKING');
                        }

                        // If the site is not reporting installed, and last Admin Zone access was over a year ago, probably a dead site. Forget about it.
                        if ($days_since_adminzone_access >= 365) {
                            $GLOBALS['SITE_DB']->query_delete('logged', ['id' => $r['id']]);
                            $GLOBALS['SITE_DB']->query_delete('may_feature', ['url' => $r['website_url']]);
                            delete_value('testing__' . $r['website_url'] . '/data/installed.php');
                            continue;
                        }
                    }
                    set_value('testing__' . $r['website_url'] . '/data/installed.php', $active, true);
                }
            }

            $start += $max;
        } while (count($rows) > 0);
    }
}
