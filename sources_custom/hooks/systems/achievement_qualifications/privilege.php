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
    1) privilege    -- Required; the codename of the privilege the member must have for this qualification to be satisfied
    2) page         -- The page to check the privilege against (not specified: blank / none)
    3) cats         -- The "category" parameter for has_privilege to define what category overrides to accept [string or comma-delimited list] (not specified: none)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_privilege
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
        $privilege = $params['privilege'];
        $page = isset($params['page']) ? $params['page'] : '';

        // Cats can be a string or a comma-delimited list
        $cats = mixed();
        $cats = isset($params['cats']) ? $params['cats'] : null;
        if (($cats !== null) && (strpos($cats, ',') !== false)) {
            $cats = explode(',', $cats);
        }

        if (has_privilege($member_id, $privilege, $page, $cats)) {
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
        $privilege = $params['privilege'];
        $page = isset($params['page']) ? $params['page'] : '';

        // Cats can be a string or a comma-delimited list
        $cats = mixed();
        $cats = isset($params['cats']) ? $params['cats'] : null;

        // Conditions
        $conditions = do_lang_tempcode('ACHIEVEMENT_PRIVILEGE_REQUIREMENT_PRIVILEGE', escape_html($privilege)); // TODO: turn into context
        if ($page != '') {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_PRIVILEGE_REQUIREMENT_PAGE', escape_html($page)));
        }
        if ($cats !== null) { // TODO: turn into context
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_PRIVILEGE_REQUIREMENT_CATS', escape_html($cats)));
        }

        if ($count_done > 0) {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_SATISFIED');
        } else {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_NOT_SATISFIED');
        }

        return do_lang_tempcode('ACHIEVEMENT_PRIVILEGE_REQUIREMENT', protect_from_escaping($conditions), protect_from_escaping($progress));
    }
}
