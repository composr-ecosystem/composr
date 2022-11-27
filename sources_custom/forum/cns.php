<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    jestr
 */

if (!function_exists('init__forum__cns')) {
    function init__forum__cns($in)
    {
        $option = get_option('jestr_name_changes', true);
        if (empty($option)) {
            return $in;
        }

        $in = override_str_replace_exactly(
            "return \$this->get_member_row_field(\$member_id, 'm_username');",
            "return jestr_name_filter(\$this->get_member_row_field(\$member_id, 'm_username'));",
            $in
        );

        $in = override_str_replace_exactly(
            "\$avatar = \$this->get_member_row_field(\$member_id, 'm_avatar_url');",
            "
            require_code('selectcode');
            \$passes = (!empty(array_intersect(@selectcode_to_idlist_using_memory(get_option('jestr_avatar_switch_shown_for', true), \$GLOBALS['FORUM_DRIVER']->get_usergroup_list()), \$GLOBALS['FORUM_DRIVER']->get_members_groups(get_member()))));
            if (\$passes) {
                \$avatar = (\$member_id == get_member()) ? '' : \$this->get_member_row_field(get_member(), 'm_avatar_url');
            } else {
                <ditto>
            }
            ",
            $in
        );

        return $in;
    }
}

function jestr_name_filter($in)
{
    $option = get_option('jestr_name_changes');
    if ($option == '') {
        return $in;
    }

    $option = get_option('jestr_name_changes_shown_for');
    if ($option == '') {
        return $in;
    }

    require_code('selectcode');
    $passes = (!empty(array_intersect(selectcode_to_idlist_using_memory($option, $GLOBALS['FORUM_DRIVER']->get_usergroup_list()), $GLOBALS['FORUM_DRIVER']->get_members_groups(get_member()))));
    if (!$passes) {
        return $in;
    }

    $alphabetic = @explode("\n", $option);

    if ((ord($in[0]) < 128) && (cms_strtoupper_ascii($in[0]) != cms_mb_strtolower($in[0]))) {
        return $alphabetic[ord(cms_strtoupper_ascii($in[0])) - ord('A')] . ' ' . $in;
    }
    return $in;
}
