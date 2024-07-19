<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    jestr
 */

if (!function_exists('init__forum__cns')) {
    function init__forum__cns($in)
    {
        if (!addon_installed('jestr')) {
            return $in;
        }

        $in = override_str_replace_exactly(
            "return \$this->get_member_row_field(\$member_id, 'm_username');",
            "return jestr_name_filter(\$this->get_member_row_field(\$member_id, 'm_username'));",
            $in,
            1,
            true
        );

        $in = override_str_replace_exactly(
            "\$avatar = \$this->get_member_row_field(\$member_id, 'm_avatar_url');",
            "
            require_code('selectcode');
            \$passes = (!empty(array_intersect(@selectcode_to_idlist_using_memory(get_option('jestr_avatar_switch_shown_for', true), \$GLOBALS['FORUM_DRIVER']->get_usergroup_list()), \$GLOBALS['FORUM_DRIVER']->get_members_groups(get_member()))));
            if (\$passes) {
                if (\$member_id == get_member()) {
                    \$avatar = '';
                    \$fallback_support = false;
                } else {
                    \$avatar = \$this->get_member_row_field(get_member(), 'm_avatar_url');
                }
            } else {
                <ditto>
            }
            ",
            $in,
            1,
            true
        );

        return $in;
    }
}

function jestr_name_filter($in)
{
    $changes_shown_for = get_option('jestr_name_changes_shown_for', true);
    if (($changes_shown_for === null) || ($changes_shown_for == '')) {
        return $in;
    }

    require_code('selectcode');
    $passes = (!empty(array_intersect(@selectcode_to_idlist_using_memory($changes_shown_for, $GLOBALS['FORUM_DRIVER']->get_usergroup_list()), $GLOBALS['FORUM_DRIVER']->get_members_groups(get_member()))));
    if (!$passes) {
        return $in;
    }

    $name_changes = get_option('jestr_name_changes', true);
    if (($name_changes === null) || ($name_changes == '')) {
        return $in;
    }

    $alphabetic = @explode("\n", $name_changes);

    if ((ord($in[0]) < 128) && (cms_strtoupper_ascii($in[0]) != cms_mb_strtolower($in[0]))) {
        return $alphabetic[ord(cms_strtoupper_ascii($in[0])) - ord('A')] . ' ' . $in;
    }
    return $in;
}
