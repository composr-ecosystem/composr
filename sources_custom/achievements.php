<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/*EXTRA FUNCTIONS: simplexml_.**/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    achievements
 */

function init__achievements()
{
    require_lang('achievements');
}

/**
 * Initialise the achievements system, parse the XML file, and return the achievements loader class instance.
 *
 * @param  boolean $show_errors Whether to attach validation errors as messages (false: attach a single generic error for regular members)
 * @return Achievements_loader The achievements loader instance
 */
function load_achievements(bool $show_errors = false) : object
{
    $error_msg = new Tempcode();
    if (!addon_installed__messaged('achievements', $error_msg)) {
        warn_exit($error_msg);
    }

    return new Achievements_loader($show_errors);
}

/**
 * Achievements loader.
 *
 * @package achievements
 */
class Achievements_loader
{
    private $cleanup_okay = false; // Whether the XML file we parsed was considered valid enough for cleanup operations to run

    private $xml_valid = false; // A more strict version of $cleanup_okay; will be false if any issues in the XML were present
    private $achievements = []; // Full XML achievement structure
    private $achievement_progress = []; // Cached calculated achievement progress

    // Used during parsing
    private $tag_stack;
    private $attribute_stack;
    private $text_so_far;

    private $show_errors;

    /**
     * Upon construction of the class, parse and validate the XML file.
     *
     * @param  boolean $show_errors Whether to attach validation errors as messages (false: attach a single generic error for regular members)
     */
    public function __construct(bool $show_errors = false)
    {
        $this->show_errors = $show_errors;

        if (!addon_installed('achievements')) {
            return;
        }

        if (!is_file(get_custom_file_base() . '/data_custom/xml_config/achievements.xml')) {
            return;
        }

        require_code('global3');

        $hash_check = [];

        // Begin parsing the XML file
        $contents = cms_file_get_contents_safe(get_custom_file_base() . '/data_custom/xml_config/achievements.xml');
        $ob = simplexml_load_string($contents);
        if ($ob === false) {
            warn_exit(do_lang_tempcode('ACHIEVEMENTS_INVALID_XML'));
        }

        $this->cleanup_okay = true;
        $this->xml_valid = true;

        $i = 0;
        foreach ($ob->achievement as $achievement) {
            $a_name = (string)$achievement['name'];
            $a_title = (string)$achievement['title'];
            $a_image = (string)$achievement['image'];
            $a_read_only = (string)$achievement['readOnly'];
            $a_hidden = (string)$achievement['hidden'];
            $a_permanent = (string)$achievement['permanent'];
            $a_points = (string)$achievement['points'];

            // Validation on achievement
            if ($a_name == '') {
                if ($show_errors) {
                    attach_message('achievements.xml: missing name attribute for achievement #' . strval($i + 1) . ' (it will not be used until fixed)', 'warn');
                } else {
                    attach_message('There is a problem with the achievements system configuration. Please inform the staff so they may fix it.', 'warn');
                }
                $this->cleanup_okay = false;
                $this->xml_valid = false;
                $i++;
                continue;
            }
            if ($a_title == '') {
                if ($show_errors) {
                    attach_message('achievements.xml: missing title attribute for achievement #' . strval($i + 1) . ' (it will not be used until fixed)', 'warn');
                } else {
                    attach_message('There is a problem with the achievements system configuration. Please inform the staff so they may fix it.', 'warn');
                }
                $this->cleanup_okay = false;
                $this->xml_valid = false;
                $i++;
                continue;
            }

            // Name must be alphanumeric
            $achievement_name = cms_strtolower_ascii(filter_naughty_harsh($a_name, true));

            // Do not allow duplicate achievement names! (NB: cleanup is still okay because the name exists elsewhere, thus won't be cleaned up)
            if (isset($this->achievements[$achievement_name])) {
                if ($show_errors) {
                    attach_message('achievements.xml: achievement #' . strval($i + 1) . ' has a duplicate name from a previously defined achievement (this one will not be used until fixed)', 'warn');
                } else {
                    attach_message('There is a problem with the achievements system configuration. Please inform the staff so they may fix it.', 'warn');
                }
                $this->xml_valid = false;
                $i++;
                continue;
            }

            // Skip achievements with no qualifications defined
            if (!isset($achievement->qualifications)) {
                if ($show_errors) {
                    attach_message('achievements.xml: achievement #' . strval($i + 1) . ' has no qualifications defined (it will not be used until fixed)', 'warn');
                } else {
                    attach_message('There is a problem with the achievements system configuration. Please inform the staff so they may fix it.', 'warn');
                }
                $this->cleanup_okay = false;
                $this->xml_valid = false;
                $i++;
                continue;
            }

            // Try converting title if it is a language string
            $title = do_lang($a_title, null, null, null, null, false);
            if ($title === null) {
                $title = $a_title;
            }

            // Build up the base achievement structure
            $this->achievements[$achievement_name] = [
                'name' => $achievement_name,
                'title' => $title,
                'image' => (($a_image != '') ? $a_image : null),
                'readOnly' => (($a_read_only == '1') ? true : false),
                'hidden' => (($a_hidden == '1') ? true : false),
                'permanent' => (($a_permanent == '1') ? true : false),
                'points' => (($a_points != '') ? intval($a_points) : 0),
                'qualification_groups' => [], // Will be populated later
            ];

            // Begin parsing qualifications
            $j = 0;
            foreach ($achievement->qualifications as $qualification_group) {
                // Skip qualification groups with no qualifications defined (NB: cleanup is still okay as this does not affect qualification processing)
                if (!isset($qualification_group->qualification)) {
                    if ($show_errors) {
                        attach_message('achievements.xml: achievement #' . strval($i + 1) . ', qualification group #' . strval($j + 1) . ', has no qualification tags defined', 'notice');
                    } else {
                        attach_message('There is a problem with the achievements system configuration. Please inform the staff so they may fix it.', 'warn');
                    }
                    $this->xml_valid = false;
                    $j++;
                    continue;
                }

                $this->achievements[$achievement_name]['qualification_groups'][$j] = [
                    'qualifications' => [], // Will be populated later
                ];

                $k = 0;
                foreach ($qualification_group->qualification as $qualification) {
                    $q_name = (string)$qualification['name'];
                    $q_persist = (string)$qualification['persist'];

                    // Validation on qualification (top-level)
                    if ($q_name == '') {
                        if ($show_errors) {
                            attach_message('achievements.xml: missing name for qualification #' . strval($k + 1) . ' in qualifications group #' . strval($j + 1) . ' in achievement #' . strval($i + 1) . ' (the qualification will be ignored until fixed)', 'warn');
                        } else {
                            attach_message('There is a problem with the achievements system configuration. Please inform the staff so they may fix it.', 'warn');
                        }
                        $this->xml_valid = false;
                        $this->cleanup_okay = false;
                        $k++;
                        continue;
                    }

                    // Name must be alphanumeric, and since we are evaluating this name as a PHP file, we must trigger a hack attack if it is not
                    filter_naughty_harsh($q_name);

                    // Notify if we specify a non-existing qualification hook (but don't treat as an error; it will simply and safely be ignored)
                    if (($show_errors) && (!is_file(get_custom_file_base() . '/sources_custom/hooks/systems/achievement_qualifications/' . $q_name . '.php'))) {
                        attach_message('achievements.xml: Qualification hook [tt]' . $q_name . '[/tt] does not exist (it will safely be ignored; it might belong to an addon you do not have installed, but you may want to check that you typed it correctly)', 'inform');
                    }

                    // Attributes depend on the qualification, so we must get them dynamically
                    $_params = $qualification->attributes();
                    $params = [];
                    foreach ($_params as $_key => $_param) {
                        $params[$_key] = (string)$_param[0];
                    }

                    // Unset global properties which should not be passed to the hooks nor calculated in a qualification hash
                    unset($params['persist']);

                    // We use hashes to track progress in the database (NB: cleanup is still okay because the hash exists elsewhere and will not be cleaned up)
                    $hash = md5($achievement_name . serialize($params));

                    // Actually, duplicate qualifications should be allowed; we might want to specify the same one in multiple sets if it should apply to all. And it's perfectly fine if it has the same tracking in the database (it's the same qualification, so it will always have the same progress).
                    /*
                    if (in_array($hash, $hash_check)) {
                        if ($show_errors) {
                            attach_message('achievements.xml: qualification #' . strval($k + 1) . ' in qualifications group #' . strval($j + 1) . ' in achievement #' . strval($i + 1) . ' is an exact duplicate of another qualification in the same achievement. This is not allowed. If you need a duplicate qualification in multiple groups, try adding an attribute with a different random string value (e.g. random="sdcbsidkcbjsidc") to each so they are not exact duplicates. (this qualification will be ignored until fixed)', 'notice');
                        } else {
                            attach_message('There is a problem with the achievements system configuration. Please inform the staff so they may fix it.', 'warn');
                        }
                        $this->xml_valid = false;
                        $k++;
                        continue;
                    }
                    */

                    $hash_check[] = $hash;

                    // Populate our qualification
                    // NB: we allow multiple qualifications with the same name because we might want multiple qualifications from the same hook.
                    $this->achievements[$achievement_name]['qualification_groups'][$j]['qualifications'][$k] = [
                        'hash' => $hash,
                        'hook' => $q_name,
                        'persist' => (($q_persist != '') ? ($q_persist == '1') : null),
                        'params' => $params,
                    ];

                    $k++;
                }

                $j++;
            }

            $i++;
        }

        // If no achievements exist in configuration, that is abnormal (usually you would uninstall the addon if not using achievements). Flag cleanup as not okay to run (but XML is still technically valid, so we leave $xml_valid alone).
        if (count($this->achievements) == 0) {
            $this->cleanup_okay = false;
        }
    }

    /**
     * Whether the XML for the achievements system is considered fully valid.
     *
     * @return boolean Whether the XML for the achievements system is considered fully valid
     */
    public function is_xml_valid() : bool
    {
        return $this->xml_valid;
    }

    /**
     * Whether the achievements system can safely run cleanup operations.
     *
     * @return boolean Whether the achievements system can safely run cleanup operations
     */
    public function can_cleanup() : bool
    {
        return $this->cleanup_okay;
    }

    /**
     * Run cleanup operations to delete unnecessary database reacords.
     * This should be executed when saving new achievements configuration.
     */
    public function cleanup()
    {
        if (!addon_installed('achievements')) {
            return;
        }

        // Do not allow cleanup if there is potential we will delete things in error
        if ($this->can_cleanup() === false) {
            if ($this->show_errors) {
                attach_message('achievements.xml: Due to validation errors, we did not clean up / remove anything from the database (such as earned attachments you might have removed from configuration). It is highly recommended you use the revisions system below and revert your changes, then re-apply your changes (with the validation errors fixed), so cleanup operations run properly.', 'notice');
            } else {
                attach_message('There is a problem with the achievements system configuration. Please inform the staff so they may fix it.', 'warn');
            }
            return;
        }

        // Revoke achievements earned that do not exist anymore
        $rows = $GLOBALS['SITE_DB']->query_select('achievements_earned', ['a_achievement', 'a_member_id'], []);
        $achievements_removed = [];
        foreach ($rows as $row) {
            if (!isset($this->achievements[$row['a_achievement']])) {
                $achievements_removed[$row['a_achievement']] = true;
                $this->revoke_achievement($row['a_achievement'], $row['a_member_id'], true);
            }
        }
        if (($this->show_errors) && (count($achievements_removed) > 0)) {
            attach_message('achievements.xml: it appears you removed the following achievements which one or more members had unlocked: [tt]' . implode(', ', $achievements_removed) . '[/tt]. This achievement has been revoked from all members. However, any points they earned from the achievement (if applicable) will remain unless you manually reverse them in the points ledger.', 'notice');
        }

        // Remove progress tracking for qualifications that do not exist anymore
        $hashes = [];
        foreach ($this->achievements as $name => $details) {
            foreach ($details['qualification_groups'] as $group) {
                foreach ($group['qualifications'] as $qualification) {
                    $hashes[] = $qualification['hash'];
                }
            }
        }
        $rows = $GLOBALS['SITE_DB']->query_select('achievements_progress', ['id', 'ap_qualification_hash'], []);
        foreach ($rows as $row) {
            if (!in_array($row['ap_qualification_hash'], $hashes)) {
                $GLOBALS['SITE_DB']->query_delete('achievements_progress', ['id' => $row['id']]);
            }
        }
    }

    /**
     * Get information about a given achievement.
     *
     * @param  ID_TEXT $name The achievement codename
     * @return ?array Information about the achievement (null: not found)
     */
    public function get_achievement(string $name) : ?array
    {
        if (!isset($this->achievements[$name])) {
            return null;
        }
        return $this->achievements[$name];
    }

    /**
     * Return an array of achievements that the provided member unlocked.
     *
     * @param  MEMBER $member_id The member of which we want to get unlocked achievements
     * @param  boolean $show_hidden Whether to also include unlocked hidden achievements
     * @return array List of achievements that have been unlocked
     */
    public function get_unlocked_achievements(int $member_id, bool $show_hidden = false) : array
    {
        if (!addon_installed('achievements')) {
            return [];
        }

        // No processing on guests
        if (is_guest($member_id)) {
            return [];
        }

        // Members without permission to the achievements module have no achievements
        if (!has_actual_page_access($member_id, 'achievements', get_module_zone('achievements'))) {
            return [];
        }

        // Get unlocked achievements
        $unlocked_achievements = $GLOBALS['SITE_DB']->query_select('achievements_earned', ['*'], ['a_member_id' => $member_id]);

        // Determine which ones we will actually return
        $ret = [];
        foreach ($unlocked_achievements as $row) {
            $achievement = $row['a_achievement'];

            if (!isset($this->achievements[$achievement])) { // Achievement does not exist anymore
                continue;
            }

            // Skip hidden achievements if we did not ask for them; these should not be shown
            if (($show_hidden === false) && ($this->achievements[$achievement]['hidden'] === true)) {
                continue;
            }

            // No qualifications means the achievement is disabled
            if (count($this->achievements[$achievement]['qualification_groups']) == 0) {
                continue;
            }

            $ret[$achievement] = [
                'title' => $this->achievements[$achievement]['title'],
                'image' => $this->achievements[$achievement]['image'],
                'date_and_time' => $row['a_date_and_time'],
            ];
        }

        return $ret;
    }

    /**
     * Get the progress of achievements, or a specific achievement, mainly for use in UI.
     * This function gets cached progress from the database and only calculates if it does not exist.
     *
     * @param  MEMBER $member_id The member on which we are calculating progress
     * @param  ?ID_TEXT $name The achievement of which we want to get progress (null: get all achievements)
     * @param  boolean $show_hidden Whether to include hidden achievements in the results
     * @return array Map of achievement progress details (empty: no achievements to earn, or the given $name was not found)
     */
    public function get_achievement_progress(int $member_id, ?string $name = null, bool $show_hidden = false) : array
    {
        if (!addon_installed('achievements')) {
            return [];
        }

        // No processing on guests
        if (is_guest($member_id)) {
            return [];
        }

        // Members without permission to the achievements module have no achievements
        if (!has_actual_page_access($member_id, 'achievements', get_module_zone('achievements'))) {
            return [];
        }

        if ($name !== null) {
            $achievements_to_process = [$name];
        } else {
            $achievements_to_process = array_keys($this->achievements);
        }

        if (!isset($this->achievement_progress[$member_id])) {
            $this->achievement_progress[$member_id] = [];
        }

        $_unlocked_achievements = $GLOBALS['SITE_DB']->query_select('achievements_earned', ['a_achievement', 'a_member_id'], ['a_member_id' => $member_id]);
        $unlocked_achievements = collapse_1d_complexity('a_achievement', $_unlocked_achievements);

        foreach ($achievements_to_process as $achievement) {
            if (!isset($this->achievements[$achievement])) {
                continue;
            }

            // Skip hidden achievements if we did not ask for them; these should not be shown
            if (($show_hidden === false) && ($this->achievements[$achievement]['hidden'] === true)) {
                continue;
            }

            // No qualifications means the achievement is disabled
            if (count($this->achievements[$achievement]['qualification_groups']) == 0) {
                continue;
            }

            // Already processed
            if (isset($this->achievement_progress[$member_id][$achievement])) {
                continue;
            }

            $this->achievement_progress[$member_id][$achievement] = [
                'title' => $this->achievements[$achievement]['title'],
                'image' => $this->achievements[$achievement]['image'],
                'read_only' => $this->achievements[$achievement]['readOnly'],
                'permanent' => $this->achievements[$achievement]['permanent'],
                'points' => $this->achievements[$achievement]['points'],
                'total_progress' => [0, 0],
                'total_progress_percentile' => 0.0,
                'qualification_groups' => [],
            ];

            $total_progress = [0, 0];
            $total_progress_arr = [];
            foreach ($this->achievements[$achievement]['qualification_groups'] as $i => $qualification_group) {
                $group_progress = [0, 0];
                $group_progress_arr = [];

                $this->achievement_progress[$member_id][$achievement]['qualification_groups'][$i] = [
                    'group_progress' => [0, 0],
                    'qualifications' => [],
                ];

                foreach ($qualification_group['qualifications'] as $j => $qualification) {
                    $hash = $qualification['hash'];
                    $_row = $GLOBALS['SITE_DB']->query_select('achievements_progress', ['ap_count_required', 'ap_count_done'], ['ap_member_id' => $member_id, 'ap_qualification_hash' => $hash], '', 1);

                    // Figure out our progress; calculate if necessary
                    if (array_key_exists(0, $_row)) {
                        $count_done = $_row[0]['ap_count_done'];
                        $count_required = $_row[0]['ap_count_required'];
                    } else {
                        $calculated_progress = $this->calculate_achievement_progress($qualification['hook'], $hash, $member_id, $qualification['params'], $qualification['persist']);
                        if ($calculated_progress === null) { // qualification is disabled
                            continue;
                        }
                        list($count_done, $count_required) = $calculated_progress;
                    }

                    // Add progress to total progress for the qualification group
                    if ($count_required > 0) {
                        $group_progress[0] += intval(min($count_done, $count_required));
                        $group_progress[1] += $count_required;
                        $group_progress_arr[] = intval(min($count_done, $count_required)) / $count_required;
                    } else {
                        $group_progress_arr[] = 1.0;
                    }

                    $this->achievement_progress[$member_id][$achievement]['qualification_groups'][$i]['qualifications'][$j] = [
                        'text' => $this->get_qualification_progress_text($qualification['hook'], $member_id, $qualification['params'], $count_done, $count_required),
                        'progress' => [intval(min($count_done, $count_required)), $count_required],
                    ];
                }

                $this->achievement_progress[$member_id][$achievement]['qualification_groups'][$i]['group_progress'] = $group_progress;

                // Whichever qualification group has the highest progress is the one we consider for the overall achievement progress
                if ($group_progress[1] > 0) { // Division by zero
                    if ($total_progress[1] > 0) { // Division by zero
                        $group_progress_percent = $group_progress[0] / $group_progress[1];
                        $total_progress_percent = $total_progress[0] / $total_progress[1];
                        if ($group_progress_percent > $total_progress_percent) {
                            $total_progress = $group_progress;
                            $total_progress_arr = $group_progress_arr;
                        }
                    } else {
                        $total_progress = $group_progress;
                        $total_progress_arr = $group_progress_arr;
                    }
                }
            }

            // Determine if we need to add or remove the achievement to the member
            $unlocked = false;
            $revoked = false;
            if ($total_progress[0] >= $total_progress[1]) {
                if ($this->achievements[$achievement]['readOnly'] === false) {
                    $unlocked = $this->award_achievement($achievement, $member_id);
                }
            } else {
                if ($this->achievements[$achievement]['permanent'] === false) {
                    $revoked = $this->revoke_achievement($achievement, $member_id);
                }
            }

            // Actually, if we have the achievement (perhaps the achievement is permanent), total progress should be 100% regardless...
            $this->achievement_progress[$member_id][$achievement]['unlocked'] = false;
            if (($revoked === false) && (($unlocked === true) || in_array($achievement, $unlocked_achievements))) {
                $total_progress[0] = $total_progress[1];
                $this->achievement_progress[$member_id][$achievement]['unlocked'] = true;
            }

            // Calculate total percentile (we use a special method where each qualification gets an equal piece of the bar regardless of its count)
            $this->achievement_progress[$member_id][$achievement]['total_progress'] = $total_progress;
            $progress_sum = 0.0;
            $total_sum = 0.0;
            foreach ($total_progress_arr as $percentile) {
                $progress_sum += $percentile;
                $total_sum += 1.0;
            }
            $this->achievement_progress[$member_id][$achievement]['total_progress_percentile'] = (($total_sum > 0.0) ? ($progress_sum / $total_sum) : 1.0);
        }

        /*
            [
                'achievement_name' => [
                    'title' => SHORT_TEXT,
                    'image' => ?URLPATH,
                    'read_only' => boolean,
                    'permanent' => boolean,
                    'points' => integer,
                    'unlocked' => boolean,
                    'total_progress' => [integer, out of integer],
                    'total_progress_percentile' => float,
                    'qualification_groups' => [
                        0 => [
                            'group_progress' => [integer, out of integer],
                            'qualifications' => [
                                0 => [
                                    'text' => ?Tempcode,
                                    'progress' => [integer, out of integer]
                                ],...
                            ]
                        ],...
                    ]
                ],...
            ]
        */
        return $this->achievement_progress[$member_id];
    }

    /**
     * Get text to display for a qualification's progress.
     *
     * @param  ID_TEXT $hook The qualification hook
     * @param  MEMBER $member_id The member we are processing
     * @param  array $params Array of XML parameters for this qualification
     * @param  integer $count_done Number of items completed for this qualification
     * @param  integer $count_required Number of items required to satisfy this qualification
     * @return ?Tempcode The tempcode text (null: this qualification is hidden or disabled)
     */
    protected function get_qualification_progress_text(string $hook, int $member_id, array $params, int $count_done, int $count_required) : ?object
    {
        if (!addon_installed('achievements')) {
            return null;
        }

        $ob = get_hook_ob('systems', 'achievement_qualifications', $hook, 'Hook_achievement_qualifications_', true);
        if ($ob === null) { // If the hook doesn't exist, treat it as a disabled (ignored) qualification
            return null;
        }

        // Process info
        $info = $ob->info($member_id, $params);
        if ($info === null) {
            return null;
        }

        // Return the text
        return $ob->to_text($member_id, $params, $count_done, $count_required);
    }

    /**
     * Re-calculate the progress towards an achievement (or all achievements) for a member.
     * This will store the new results in the database and unlock / revoke achievements as necessary.
     *
     * @param  MEMBER $member_id The member which we are re-calculating
     * @param  ?ID_TEXT $name The name of the achievement to re-calculate (null: all of them)
     */
    public function recalculate_achievement_progress(int $member_id, ?string $name = null)
    {
        if (!addon_installed('achievements')) {
            return;
        }

        // No processing on guests
        if (is_guest($member_id)) {
            return;
        }

        // Members without permission to the achievements module have no achievements
        if (!has_actual_page_access($member_id, 'achievements', get_module_zone('achievements'))) {
            return;
        }

        // Figure out what to process
        if ($name !== null) {
            $achievements_to_process = [$name];
        } else {
            $achievements_to_process = array_keys($this->achievements);
        }

        foreach ($achievements_to_process as $achievement) {
            // Achievement does not exist?
            if (!isset($this->achievements[$achievement])) {
                continue;
            }

            // An achievement with no qualifications means the achievement is disabled
            if (count($this->achievements[$achievement]['qualification_groups']) == 0) {
                continue;
            }

            $total_progress = [0, 0];

            // Trigger a re-calculation on all qualifications
            foreach ($this->achievements[$achievement]['qualification_groups'] as $qualification_group) {
                $group_progress = [0, 0];
                foreach ($qualification_group['qualifications'] as $qualification) {
                    $qualification_progress = $this->calculate_achievement_progress($qualification['hook'], $qualification['hash'], $member_id, $qualification['params'], $qualification['persist']);
                    if ($qualification_progress === null) {
                        continue;
                    }

                    // Add progress to total progress for the qualification group
                    if ($qualification_progress[1] > 0) {
                        $group_progress[0] += intval(min($qualification_progress[0], $qualification_progress[1]));
                        $group_progress[1] += $qualification_progress[1];
                    }
                }

                // Whichever qualification group has the highest progress is the one we consider for the overall achievement progress
                if ($group_progress[1] > 0) { // Division by zero
                    if ($total_progress[1] > 0) { // Division by zero
                        $group_progress_percent = $group_progress[0] / $group_progress[1];
                        $total_progress_percent = $total_progress[0] / $total_progress[1];
                        if ($group_progress_percent > $total_progress_percent) {
                            $total_progress = $group_progress;
                        }
                    } else {
                        $total_progress = $group_progress;
                    }
                }
            }

            // Determine if we need to add or remove the achievement to the member
            if ($total_progress[0] >= $total_progress[1]) {
                if ($this->achievements[$achievement]['readOnly'] === false) {
                    $this->award_achievement($achievement, $member_id);
                }
            } else {
                if ($this->achievements[$achievement]['permanent'] === false) {
                    $this->revoke_achievement($achievement, $member_id);
                }
            }
        }
    }

    /**
     * Calculate the current progress of a specific qualification and store (or update) it in the database.
     *
     * @param  ID_TEXT $name The name of the qualification hook
     * @param  SHORT_TEXT $hash The hash of the qualification we are calculating
     * @param  MEMBER $member_id The member on which we are calculating
     * @param  array $params Array of parameters defined in the XML to pass to the qualification hook
     * @param  ?boolean $persist Whether to persist progress on this qualification (null: determined by the hook)
     * @return ?array A double; count of items done and count of items required to satisfy the qualification (null: qualification is disabled and should be ignored)
     */
    protected function calculate_achievement_progress(string $name, string $hash, int $member_id, array $params, ?bool $persist) : ?array
    {
        if (!addon_installed('achievements')) {
            return null;
        }

        // No processing on guests
        if (is_guest($member_id)) {
            return null;
        }

        // Members without permission to the achievements module have no achievements
        if (!has_actual_page_access($member_id, 'achievements', get_module_zone('achievements'))) {
            return null;
        }

        $ob = get_hook_ob('systems', 'achievement_qualifications', $name, 'Hook_achievement_qualifications_', true);
        if ($ob === null) { // If the hook doesn't exist, treat it as a disabled (ignored) qualification
            return null;
        }

        // Process info and if we are going to persist progress
        $info = $ob->info($member_id, $params);
        if ($info === null) {
            return null;
        }
        if ($persist === null) {
            $persist = $info['persist_progress_default'];
        }
        if ($info['supports_persist'] === false) {
            $persist = false;
        }

        // If persisting, we need to determine the time we last calculated this qualification
        $last_time = null;
        if ($persist === true) {
            $last_time = $GLOBALS['SITE_DB']->query_select_value_if_there('achievements_progress', 'ap_date_and_time', ['ap_member_id' => $member_id, 'ap_qualification_hash' => $hash], ' ORDER BY ap_date_and_time DESC');
        }

        // Run the calculations
        $calculations = $ob->run($member_id, $params, $last_time);
        if ($calculations === null) {
            return null;
        }
        list($count_done, $count_required) = $calculations;

        // If persisting, we will be adding what we got for count done to what we have logged in the database for count done
        $current_value = null;
        if ($persist === true) {
            $current_value = $GLOBALS['SITE_DB']->query_select_value_if_there('achievements_progress', 'ap_count_done', ['ap_member_id' => $member_id, 'ap_qualification_hash' => $hash]);
            if ($current_value !== null) {
                $count_done += $current_value;
            }
        }

        // Update the database with our new progress
        $GLOBALS['SITE_DB']->query_delete('achievements_progress', ['ap_member_id' => $member_id, 'ap_qualification_hash' => $hash]);
        $GLOBALS['SITE_DB']->query_insert('achievements_progress', ['ap_count_done' => $count_done, 'ap_count_required' => $count_required, 'ap_member_id' => $member_id, 'ap_qualification_hash' => $hash, 'ap_date_and_time' => time()]);

        // Clear cache
        require_code('caches');
        delete_cache_entry('achievements', [$member_id]);

        return [$count_done, $count_required];
    }

    /**
     * Award an achievement to a member.
     * Note that this does not do special checking; it immediately unlocks the achievement regardless if requirements are met.
     *
     * @param  ID_TEXT $name The name of the achievement to award
     * @param  MEMBER $member_id The member receiving the achievement
     * @return boolean Whether we actually did something
     */
    protected function award_achievement(string $name, int $member_id) : bool
    {
        if (!addon_installed('achievements')) {
            return false;
        }

        // No processing on guests
        if (is_guest($member_id)) {
            return false;
        }

        // Members without permission to the achievements module have no achievements
        if (!has_actual_page_access($member_id, 'achievements', get_module_zone('achievements'))) {
            return false;
        }

        if (!isset($this->achievements[$name])) { // Achievement doesn't exist
            return false;
        }

        $id = $GLOBALS['SITE_DB']->query_select_value_if_there('achievements_earned', 'id', ['a_member_id' => $member_id, 'a_achievement' => $name]);
        if ($id !== null) { // Member already has the achievement
            return false;
        }

        // Award the achievement
        $GLOBALS['SITE_DB']->query_insert('achievements_earned', ['a_achievement' => $name, 'a_member_id' => $member_id, 'a_date_and_time' => time()]);

        // Notify the member they just earned an achievement
        require_code('notifications');
        $subject = do_lang('ACHIEVEMENT_AWARD_NOTIFICATION_SUBJECT', comcode_escape($this->achievements[$name]['title']));
        $message = do_notification_lang('ACHIEVEMENT_AWARD_NOTIFICATION_MESSAGE', comcode_escape($this->achievements[$name]['title']));
        dispatch_notification('achievement_unlocked', null, $subject, $message, [$member_id], A_FROM_SYSTEM_UNPRIVILEGED);

        // Award points, if applicable
        if (addon_installed('points')) {
            $points = $this->achievements[$name]['points'];
            if ($points > 0) {
                require_code('points2');
                points_credit_member($member_id, do_lang('ACHIEVEMENT_AWARDED', comcode_escape($this->achievements[$name]['title'])), $points, 0, true, 0, 'achievement', 'award', $name);
            }
        }

        // Clear cache
        require_code('caches');
        delete_cache_entry('achievements', [$member_id]);

        return true;
    }

    /**
     * Revoke an achievement from a member.
     * Note that this does not do special checking; it immediately revokes the achievement regardless if requirements are met.
     *
     * @param  ID_TEXT $name The name of the achievement being revoked
     * @param  MEMBER $member_id The member from which we are revoking the achievement
     * @param  boolean $removed_from_system Whether the achievement itself was removed from the system
     * @return boolean Whether we actually did something
     */
    protected function revoke_achievement(string $name, int $member_id, bool $removed_from_system = false) : bool
    {
        if (!addon_installed('achievements')) {
            return false;
        }

        // No processing on guests
        if (is_guest($member_id)) {
            return false;
        }

        // Members without permission to the achievements module have no achievements
        if (!has_actual_page_access($member_id, 'achievements', get_module_zone('achievements'))) {
            return false;
        }

        if (!isset($this->achievements[$name])) { // Achievement doesn't exist; treat as removal from the system
            $removed_from_system = true;
        }

        $id = $GLOBALS['SITE_DB']->query_select_value_if_there('achievements_earned', 'id', ['a_member_id' => $member_id, 'a_achievement' => $name]);
        if ($id === null) { // Member does not actually have the achievement; nothing to do
            return false;
        }

        // Revoke the achievement
        $GLOBALS['SITE_DB']->query_delete('achievements_earned', ['a_achievement' => $name, 'a_member_id' => $member_id]);

        // Notify the member they just lost an achievement
        require_code('notifications');
        if ($removed_from_system === false) {
            $subject = do_lang('ACHIEVEMENT_REVOKE_NOTIFICATION_SUBJECT', comcode_escape($this->achievements[$name]['title']));
            $message = do_notification_lang('ACHIEVEMENT_REVOKE_NOTIFICATION_MESSAGE', comcode_escape($this->achievements[$name]['title']));
        } else {
            $subject = do_lang('ACHIEVEMENT_REVOKE_SYSTEM_NOTIFICATION_SUBJECT', comcode_escape($name));
            $message = do_notification_lang('ACHIEVEMENT_REVOKE_SYSTEM_NOTIFICATION_MESSAGE', comcode_escape($name));
        }
        dispatch_notification('achievement_revoked', null, $subject, $message, [$member_id], A_FROM_SYSTEM_UNPRIVILEGED);

        // Reverse points, if applicable (points are maintained if the achievement was removed from the system)
        if (($removed_from_system === false) && (addon_installed('points'))) {
            require_code('points2');
            points_transactions_reverse_all(true, null, $member_id, 'achievement', 'award', $name);
        }

        // Clear cache
        require_code('caches');
        delete_cache_entry('achievements', [$member_id]);

        return true;
    }
}
