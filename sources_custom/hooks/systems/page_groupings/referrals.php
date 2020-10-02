<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    public function run($member_id = null, $extensive_docs = false)
    {
        $ret = array();

        $path = get_custom_file_base() . '/text_custom/referrals.txt';
        if (is_file($path)) {
            $ini_file = parse_ini_file($path, true);

            foreach ($ini_file as $ini_file_section_name => $ini_file_section) {
                if ($ini_file_section_name != 'global') {
                    $scheme_name = $ini_file_section_name;
                    $scheme = $ini_file_section;

                    $scheme_title = isset($scheme['title']) ? $scheme['title'] : $ini_file_section_name;

                    $ret[] = array('audit', 'menu/referrals', array('admin_referrals', array('type' => 'browse', 'scheme' => $scheme_name), get_page_zone('admin_referrals', false, 'adminzone')), $scheme_title, 'referrals:DOC_REFERRALS');
                }
            }
        }

        $ret[] = array('setup', 'menu/referrals', array('referrals', array(), get_comcode_zone('referrals', false, 'adminzone')), do_lang_tempcode('referrals:REFERRALS'), 'referrals:DOC_REFERRALS');

        return $ret;
    }
}
