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

/*
    Supported parameters for this qualification:
    1) count  -- the number of profile views a member must receive before this qualification is satisfied (not specified: 100)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_profile_views
{
    /**
     * Get information about this qualification.
     *
     * @param  MEMBER $member_id The member we are viewing
     * @param  array $params Map of parameters which were specified on the XML for this qualification
     * @return ?array Map of details (null: qualification is disabled)
     */
    public function info(int $member_id, array $params) : ?array
    {
        if (!addon_installed('achievements')) {
            return null;
        }

        return [
            'supports_persist' => false, // Always persists
            'persist_progress_default' => false,
        ];
    }

    /**
     * Run calculations on this qualification to see how much it has been completed.
     *
     * @param  MEMBER $member_id The member we are viewing
     * @param  array $params Map of parameters which were specified on the XML for this qualification
     * @param  ?TIME $last_time Only calculate results more recent than the given time (null: never calculated before, or not persisting progress)
     * @return ?array Double: the number accomplished, and the number needed for the qualification to be considered "complete" (null: qualification should be ignored)
     */
    public function run(int $member_id, array $params, ?int $last_time = null) : ?array
    {
        if (!addon_installed('achievements')) {
            return null;
        }

        $count_required = isset($params['count']) ? intval($params['count']) : 100;

        require_code('cns_general');
        $member_info = cns_read_in_member_profile($member_id);
        $views = $member_info['profile_views'];

        return [$views, $count_required];
    }

    /**
     * Convert information about the qualification into human-readable text where members can track their progress.
     *
     * @param  MEMBER $member_id The member we are viewing
     * @param  array $params Map of parameters which were specified on the XML for this qualification
     * @param  integer $count_done Count of items achieved for the qualification (from run)
     * @param  integer $count_required Count of items required for the qualification to be complete (from run)
     * @return ?Tempcode The text explaining this condition and the progress (null: hidden or disabled qualification)
     */
    public function to_text(int $member_id, array $params, int $count_done, int $count_required) : ?object
    {
        if (!addon_installed('achievements')) {
            return null;
        }

        require_lang('achievements');

        $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS', escape_html(integer_format($count_done)), escape_html(integer_format($count_required)));

        return do_lang_tempcode('ACHIEVEMENT_PROFILE_VIEWS_REQUIREMENT', protect_from_escaping($progress));
    }
}
