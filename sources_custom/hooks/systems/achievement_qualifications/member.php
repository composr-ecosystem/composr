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
    This qualification is designed to be used for manual awarding of achievements by member ID. It can also be used for manual achievement progress.
    You should not use any other qualification in the same block, including other member ones.
    Note that manual progress tracking, if specified, will apply to all members defined; use separate member qualifications in separate blocks to separate progress by member.
    Also note due to a caveat in the system, the qualification text will be hidden for the member if required is > 1 and count is 0.

    Supported parameters for this qualification:
    1) ids      -- Required (but can be blank); a comma-delimited list of member IDs
    2) text     -- Optional Comcode-supported text or language string to display as a requirement (not defined: qualification is hidden)
    3) count    -- Optional number of something the members have to calculate progress (not defined: 1 if member is in ids, 0 if not)
    4) required -- Optional number of count the members must get to satisfy this qualification (not defined: 1)
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
        $count = isset($params['count']) ? intval($params['count']) : 1;
        $required = isset($params['required']) ? intval($params['required']) : 1;

        foreach ($member_ids as $id) {
            if (is_numeric($id)) {
                $awarded_members[] = intval($id);
            }
        }

        // Check requirement
        if (in_array($member_id, $awarded_members)) {
            return [$count, $required];
        }

        return [0, $required];
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

        $member_ids = explode(',', $params['ids']);

        // Special case: If this member is not in the defined qualification, and we are tracking progress across members, make sure we don't show duplicate messages
        // The caveat is that the achievement will go hidden for members with no progress; but there is no way we can look ahead in config to properly manage this
        if (($count_required > 1) && (!in_array(strval($member_id), $member_ids))) {
            return null;
        }

        // Custom text
        $_ret = do_lang($text, null, null, null, null, false);
        if ($_ret === null) {
            $ret = comcode_to_tempcode($text, null, true);
        } else {
            $ret = comcode_to_tempcode($_ret);
        }

        // Progress
        if ($count_required > 1) {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS', escape_html(integer_format($count_done)), escape_html(integer_format($count_required)));
        } elseif ($count_required == 1) {
            if ($count_done > 0) {
                $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_SATISFIED');
            } else {
                $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_NOT_SATISFIED');
            }
        } else {
            $progress = new Tempcode();
        }

        // Put it together
        return $ret->attach(paragraph($progress));
    }
}
