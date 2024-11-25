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

/**
 * Hook class.
 */
class Hook_contentious_overrides_jestr
{
    public function compile_included_code($path, $codename, &$code)
    {
        if (!addon_installed('jestr')) {
            return;
        }

        require_code('override_api');

        switch ($codename) {
            case 'forum/cns':
                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

                $code = override_str_replace_exactly(
                    "return \$this->get_member_row_field(\$member_id, 'm_username');",
                    "return jestr_name_filter(\$this->get_member_row_field(\$member_id, 'm_username'));",
                    $code,
                    1,
                    true
                );

                $code = override_str_replace_exactly(
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
                    $code,
                    1,
                    true
                );
                break;
            case 'forum/pages/modules/topicview.php':
                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

                // NB: cannot use overrides API for this
                $code = str_replace(
                    "\$_postdetails['post']",
                    "jestr_filtering_wrap(\$_postdetails['post']->evaluate())",
                    $code
                );
                break;
        }
    }
}
