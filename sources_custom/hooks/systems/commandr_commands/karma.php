<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_commandr_command_karma
{
    /**
     * Run function for Commandr hooks.
     *
     * @param  array $options The options with which the command was called
     * @param  array $parameters The parameters with which the command was called
     * @param  object $commandr_fs A reference to the Commandr filesystem object
     * @return array Array of stdcommand, stdhtml, stdout, and stderr responses
     */
    public function run(array $options, array $parameters, object &$commandr_fs) : array
    {
        $err = new Tempcode();
        if (!addon_installed__messaged('karma', $err)) {
            return ['', '', '', $err->evaluate()];
        }

        require_lang('karma');

        if ((array_key_exists('h', $options)) || (array_key_exists('help', $options))) {
            return ['', do_command_help('karma', ['h'], [true]), '', ''];
        }

        if (!array_key_exists(0, $parameters)) {
            $member_id = get_member();
        } else {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($parameters[0]);
            if (($member_id === null) || (is_guest($member_id))) {
                return ['', '', '', do_lang('MEMBER_NO_EXIST')];
            }
        }

        if (($member_id != get_member()) && (!has_privilege(get_member(), 'view_others_karma'))) {
            require_lang('permissions');
            return ['', '', '', do_lang('ACCESS_DENIED__PRIVILEGE', $GLOBALS['FORUM_DRIVER']->get_username(get_member()), 'view_others_karma')];
        }

        require_code('karma');

        $karma = get_karma($member_id);

        if (has_privilege(get_member(), 'view_bad_karma')) {
            return ['', '', do_lang('GOOD_BAD_KARMA', integer_format($karma[0]), integer_format($karma[1])), ''];
        }

        return ['', '', do_lang('TOTAL_KARMA', integer_format($karma[0] - $karma[1])), ''];
    }
}
