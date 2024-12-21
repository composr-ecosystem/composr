<?php /*

Composr
Copyright (c) Christopher Graham, 2004-2024

See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    achievements
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_achievements
{
    public function compile_template(&$data, $template_name, $theme, $lang, $suffix, $directory)
    {
        if (!addon_installed('achievements')) {
            return;
        }

        if (($directory === null) || (strpos($directory, 'templates_custom') !== false)) {
            return;
        }

        if ($suffix != '.tpl') {
            return;
        }

        // Inject the achievements block into member profile and forum posts
        switch ($template_name) {
            case 'CNS_MEMBER_PROFILE_ABOUT':
                $data = override_str_replace_exactly(
                    "\t\t" . '{+START,IF_NON_EMPTY,{AVATAR_URL}}' . "\n\t\t\t" . '<div class="cns-member-profile-avatar">' . "\n\t\t\t\t" . '<img src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" alt="{!AVATAR}" />' . "\n\t\t\t" . '</div>' . "\n\t\t" . '{+END}',
                    "<ditto>{\$BLOCK,block=main_achievements,param={MEMBER_ID},size=32}",
                    $data,
                    1,
                    true
                );
                break;

            case 'CNS_TOPIC_POST':
                $data = override_str_replace_exactly(
                    '{POST_AVATAR}',
                    "<ditto>{\$BLOCK,block=main_achievements,param={POSTER_ID},size=24}",
                    $data,
                    1,
                    true
                );
                break;
        }
    }

    public function compile_included_code($path, $codename, &$code)
    {
        if (!addon_installed('achievements')) {
            return;
        }

        require_code('override_api');

        switch ($codename) {
            case 'users_inactive_occasionals':
                if (strpos($path, 'sources_custom/') !== false) {
                    return;
                }

                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path), $path);
                }

                // Make sure each new day a member logs in, we explicitly re-calculate their achievements
                insert_code_after__by_command(
                    $code,
                    'create_session',
                    "\$GLOBALS['SITE_DB']->query_insert('daily_visits', ['d_member_id' => \$member_id, 'd_date_and_time' => time()]);",
                    "
                    // Run achievement re-calculations for this member
                    if (addon_installed('achievements')) {
                        require_code('achievements');
                        \$ob = load_achievements();
                        if (\$ob->is_xml_valid() === true) { // For safety, do not run re-calculations if any XML issues are present in the achievements system
                            \$ob->recalculate_achievement_progress(\$member_id);
                        }
                    }
                    ",
                    1,
                    true
                );
                break;
        }
    }
}
