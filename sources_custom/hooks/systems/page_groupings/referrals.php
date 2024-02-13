<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    referrals
 */

/**
 * Hook class.
 */
class Hook_page_groupings_referrals
{
    /**
     * Run function for do_next_menu hooks. They find links to put on standard navigation menus of the system.
     *
     * @param  ?MEMBER $member_id Member ID to run as (null: current member)
     * @param  boolean $extensive_docs Whether to use extensive documentation tooltips, rather than short summaries
     * @return array List of tuple of links (page grouping, icon, do-next-style linking data), label, help (optional) and/or nulls
     */
    public function run(?int $member_id = null, bool $extensive_docs = false) : array
    {
        if (!addon_installed('referrals')) {
            return [];
        }

        require_code('files');

        $ret = [];

        $path = get_custom_file_base() . '/text_custom/referrals.txt';
        if (!is_file($path)) {
            $path = get_file_base() . '/text_custom/referrals.txt';
        }

        if (is_file($path)) {
            $ini_file = cms_parse_ini_file_safe($path, true);

            foreach ($ini_file as $ini_file_section_name => $ini_file_section) {
                if ($ini_file_section_name != 'global') {
                    $scheme_name = $ini_file_section_name;
                    $scheme = $ini_file_section;

                    $scheme_title = isset($scheme['title']) ? $scheme['title'] : $ini_file_section_name;

                    $ret[] = ['audit', 'spare/referrals', ['admin_referrals', ['type' => 'browse', 'scheme' => $scheme_name], get_page_zone('admin_referrals', false)], $scheme_title, 'referrals:DOC_REFERRALS'];
                }
            }
        }

        $ret[] = ['setup', 'spare/referrals', ['referrals', [], get_page_zone('referrals', false)], do_lang_tempcode('referrals:REFERRALS'), 'referrals:DOC_REFERRALS'];

        return $ret;
    }
}
