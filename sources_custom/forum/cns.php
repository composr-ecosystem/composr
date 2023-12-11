<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    jestr
 */

if (!function_exists('init__forum__cns')) {
    function init__forum__cns($in = null)
    {
        $option = get_option('jestr_name_changes', true);
        if (empty($option)) {
            return $in;
        }

        $in = override_str_replace_exactly(
            "return \$this->get_member_row_field(\$member, 'm_username');",
            "return jestr_name_filter(\$this->get_member_row_field(\$member, 'm_username'));",
            $in
        );

        $in = override_str_replace_exactly(
            "\$avatar = \$this->get_member_row_field(\$member, 'm_avatar_url');",
            "
            require_code('selectcode');
            \$passes = (count(array_intersect(@selectcode_to_idlist_using_memory(get_option('jestr_avatar_switch_shown_for', true), \$GLOBALS['FORUM_DRIVER']->get_usergroup_list()), \$GLOBALS['FORUM_DRIVER']->get_members_groups(get_member()))) != 0);
            if (\$passes) {
                \$avatar = (\$member == get_member()) ? '' : \$this->get_member_row_field(get_member(), 'm_avatar_url');
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
    $passes = (count(array_intersect(selectcode_to_idlist_using_memory($option, $GLOBALS['FORUM_DRIVER']->get_usergroup_list()), $GLOBALS['FORUM_DRIVER']->get_members_groups(get_member()))) != 0);
    if (!$passes) {
        return $in;
    }

    $alphabetic = @explode("\n", $option);

    if (strtoupper($in[0]) != strtolower($in[0])) {
        return $alphabetic[ord(strtoupper($in[0])) - ord('A')] . ' ' . $in;
    }
    return $in;
}
