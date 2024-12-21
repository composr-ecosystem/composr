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
    1) page         -- Required; the name of the page to which the member must have access for this qualification to be satisfied
    2) zone         -- The zone in which the page exists (not specified: search for it)
    3) cats         -- A list of category details to require access (not specified: none)
    4) privileges   -- If specified, then the member must have at least one of these comma-delimited privilege IDs as well for this qualification to be satisfied (not specified: none)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_page_access
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

        // Read in parameters
        $page = $params['page'];
        $zone = isset($params['zone']) ? $params['zone'] : null;
        $cats = isset($params['cats']) ? explode(',', $params['cats']) : null;
        $privileges = isset($params['privileges']) ? explode(',', $params['privileges']) : null;

        if (has_actual_page_access($member_id, $page, $zone, $cats, $privileges)) {
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

        // Read in parameters
        $page = $params['page'];
        $zone = isset($params['zone']) ? $params['zone'] : null;
        $cats = isset($params['cats']) ? explode(',', $params['cats']) : null;
        $privileges = isset($params['privileges']) ? explode(',', $params['privileges']) : null;

        // Conditions
        $conditions = do_lang_tempcode('ACHIEVEMENT_PAGE_ACCESS_REQUIREMENT_PAGE', escape_html($page));
        if ($zone !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_PAGE_ACCESS_REQUIREMENT_ZONE', escape_html($zone)));
        }
        if (($cats !== null) && (count($cats) > 0)) { // TODO: turn into context
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_PAGE_ACCESS_REQUIREMENT_CATS', escape_html(implode(', ', $cats))));
        }
        if (($privileges !== null) && (count($privileges) > 0)) { // TODO: turn into context
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_PAGE_ACCESS_REQUIREMENT_PRIVILEGES', escape_html(implode(', ', $privileges))));
        }

        if ($count_done > 0) {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_SATISFIED');
        } else {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_NOT_SATISFIED');
        }

        return do_lang_tempcode('ACHIEVEMENT_PAGE_ACCESS_REQUIREMENT', protect_from_escaping($conditions), protect_from_escaping($progress));
    }
}
