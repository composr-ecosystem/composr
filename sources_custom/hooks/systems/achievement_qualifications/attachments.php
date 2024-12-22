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
    1) count        -- The number of attachments a member must make site-wide for this qualification to be met (not specified: 20)
    2) file_size    -- Only count attachments of at least this many bytes in size (not specified: no filter)
    3) downloads    -- Only count attachments that have been downloaded at least this many times (not specified: no filter)
    4) days         -- Only count attachments which have been added in the last given number of days [persist is not supported if this is specified] (not specified: no filter)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_attachments
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
            'supports_persist' => (!isset($params['days'])),
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
        $count_required = isset($params['count']) ? intval($params['count']) : 20;
        $file_size = isset($params['file_size']) ? intval($params['file_size']) : null;
        $downloads = isset($params['downloads']) ? intval($params['downloads']) : null;
        $days = isset($params['days']) ? intval($params['days']) : null;

        // Build query
        $where_map = ['a_member_id' => $member_id];
        $extra_where = '';
        if ($file_size !== null) {
            $extra_where .= ' AND a_file_size>=' . strval($file_size);
        }
        if ($downloads !== null) {
            $extra_where .= ' AND a_num_downloads>=' . strval($downloads);
        }
        if ($days !== null) {
            $extra_where .= ' AND a_add_time>=' . strval(time() - ($days * 24 * 60 * 60));
        } elseif ($last_time !== null) {
            $extra_where .= ' AND a_add_time>' . strval($last_time);
        }

        $count_done = $GLOBALS['SITE_DB']->query_select_value('attachments', 'COUNT(*)', $where_map, $extra_where);

        return [$count_done, $count_required];
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
        $file_size = isset($params['file_size']) ? intval($params['file_size']) : null;
        $downloads = isset($params['downloads']) ? intval($params['downloads']) : null;
        $days = isset($params['days']) ? intval($params['days']) : null;

        // Conditions
        $conditions = new Tempcode();
        if ($file_size !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_ATTACHMENTS_REQUIREMENT_FILE_SIZE', escape_html(integer_format($file_size))));
        }
        if ($downloads !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_ATTACHMENTS_REQUIREMENT_DOWNLOADS', escape_html(integer_format($downloads))));
        }
        if ($days !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_ATTACHMENTS_REQUIREMENT_DAYS', escape_html(integer_format($days))));
        }

        // Progress
        $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS', escape_html(integer_format($count_done)), escape_html(integer_format($count_required)));

        // Finalise
        return do_lang_tempcode('ACHIEVEMENT_ATTACHMENTS_REQUIREMENT', protect_from_escaping($conditions), protect_from_escaping($progress));
    }
}
