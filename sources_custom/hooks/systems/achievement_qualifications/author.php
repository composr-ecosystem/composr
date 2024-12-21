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
    There are no parameters for this qualification.
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_author
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

        if (!addon_installed('authors')) {
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

        if (!addon_installed('authors')) {
            return null;
        }

        $count_done = $GLOBALS['SITE_DB']->query_select_value('authors', 'COUNT(*)', ['member_id' => $member_id]);

        return [(($count_done > 0) ? 1 : 0), 1];
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

        if (!addon_installed('authors')) {
            return null;
        }

        require_lang('achievements');

        // Progress
        if ($count_done > 0) {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_SATISFIED');
        } else {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_NOT_SATISFIED');
        }

        // Finalise
        return do_lang_tempcode('ACHIEVEMENT_AUTHOR_REQUIREMENT', protect_from_escaping($progress));
    }
}
