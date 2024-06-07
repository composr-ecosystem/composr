<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_member_boxes_karma
{
    /**
     * Find member box details.
     *
     * @param  MEMBER $member_id The ID of the member we are getting extra details for
     * @return ?array Map of extra box details (null: disabled)
     */
    public function run(int $member_id) : ?array
    {
        if (!addon_installed('karma')) {
            return null;
        }

        require_lang('karma');
        require_code('karma');

        $karma = get_karma($member_id);

        if (has_privilege(get_member(), 'view_bad_karma')) {
            return [
                do_lang('KARMA') => do_lang('GOOD_BAD_KARMA', escape_html(integer_format($karma[0])), escape_html(integer_format($karma[1]))),
            ];
        }

        return [
            do_lang('KARMA') => integer_format($karma[0] - $karma[1]),
        ];
    }
}
