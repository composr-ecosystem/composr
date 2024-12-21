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
    1) not_staff  -- If 1, then instead of this qualification being satisfied when the member is staff, it is satisfied when they are *not* staff (not specified: 0)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_staff
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
            'supports_persist' => true,
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

        $not_staff = isset($params['not_staff']) ? ($params['not_staff'] == '1') : false;

        require_code('cns_general');
        $member_info = cns_read_in_member_profile($member_id);
        $staff = $member_info['is_staff'];

        if (($staff === true) && ($not_staff === false)) {
            return [1, 1];
        }
        if (($staff === false) && ($not_staff === true)) {
            return [1, 1];
        }

        return [0, 1];
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

        $not_staff = isset($params['not_staff']) ? ($params['not_staff'] == '1') : false;

        // Progress
        if ($count_done > 0) {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_SATISFIED');
        } else {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_NOT_SATISFIED');
        }

        // Finalise
        if ($not_staff === true) {
            return do_lang_tempcode('ACHIEVEMENT_NOT_STAFF_REQUIREMENT', protect_from_escaping($progress));
        }
        return do_lang_tempcode('ACHIEVEMENT_STAFF_REQUIREMENT', protect_from_escaping($progress));
    }
}
