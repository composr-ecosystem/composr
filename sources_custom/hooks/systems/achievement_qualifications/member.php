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
    This qualification is designed to be used for manual awarding of achievements by member ID.
    You should not use any other qualification (except manual) in the same block, including other member ones.

    Supported parameters for this qualification:
    1) ids  -- Required (but can be blank if no one earned the achievement yet); a comma-delimited list of member IDs which meet this qualification
    2) text -- Optional Comcode-supported text or language string to display as a requirement (not defined: this is hidden)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_member
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
            'supports_persist' => false,
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

        $awarded_members = [];

        // Read in options
        $member_ids = explode(',', $params['ids']);
        foreach ($member_ids as $id) {
            if (is_numeric($id)) {
                $awarded_members[] = intval($id);
            }
        }

        // Check requirement
        if (in_array($member_id, $awarded_members)) {
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
        $text = isset($params['text']) ? $params['text'] : null;
        if ($text === null) {
            return null; // Hidden unless custom text defined
        }

        $ret = do_lang($text, null, null, null, null, false);
        if ($ret === null) {
            return comcode_to_tempcode($text, null, true);
        }
        return comcode_to_tempcode($ret, null, true);
    }
}
