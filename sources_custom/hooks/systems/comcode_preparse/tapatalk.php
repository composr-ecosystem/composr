<?php /*

 Composr
 Copyright (c) ocProducts/Tapatalk, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Hook class.
 */
class Hook_comcode_preparse_tapatalk
{
    public function preparse(&$comcode)
    {
        if (!addon_installed('cns_tapatalk')) {
            return;
        }

        if (!addon_installed('cns_forum')) {
            return;
        }

        if (get_forum_type() != 'cns') {
            return;
        }

        if (!defined('IN_MOBIQUO')) {
            $protocol = tacit_https() ? 'https:' : 'http:';
            $comcode = preg_replace('/\[emoji(\d+)\]/i', '[img]' . $protocol . '//emoji.tapatalk-cdn.com/emoji$1.png[/img]', $comcode);
        }
    }
}
