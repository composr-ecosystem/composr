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
 * Module page class.
 */
class Module_achievements
{
    public $title;

    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Patrick Schmalstig';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'achievements';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'achievements_earned',
            'achievements_progress',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('achievements_earned', [
                'id' => '*AUTO',
                'a_member_id' => '*MEMBER',
                'a_achievement' => '*ID_TEXT',
                'a_date_and_time' => 'TIME',
            ]);
            $GLOBALS['SITE_DB']->create_table('achievements_progress', [
                'id' => '*AUTO',
                'ap_member_id' => 'MEMBER',
                'ap_qualification_hash' => 'SHORT_TEXT',
                'ap_count_required' => 'INTEGER',
                'ap_count_done' => 'INTEGER',
                'ap_date_and_time' => 'TIME',
            ]);
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('achievements')) {
            return null;
        }

        return [
            'browse' => ['ACHIEVEMENTS', 'spare/popular'],
        ];
    }

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('achievements', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        require_lang('achievements');
        require_code('achievements');

        if ($type == 'browse') {
            $this->title = get_screen_title('ACHIEVEMENTS');
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        $type = get_param_string('type', 'browse');

        // Decide what to do
        if ($type == 'browse') {
            return $this->achievements_list();
        }

        return new Tempcode();
    }

    /**
     * Load a table of public and earnable achievements as well as current member progress.
     *
     * @return Tempcode The table
     */
    public function achievements_list() : object
    {
        // Try cache first
        require_code('caches');
        $data = get_cache_entry('achievements_member', serialize([get_member()]), CACHE_AGAINST_NOTHING_SPECIAL, 60);

        // No cache available; we have to get the data from the achievements system
        if ($data === null) {
            $achievements = load_achievements();
            $data = $achievements->get_achievement_progress(get_member());

            require_code('caches2');
            set_cache_entry('achievements_member', 60, serialize([get_member()]), $data, CACHE_AGAINST_NOTHING_SPECIAL);
        }

        require_code('templates_results_table');
        require_code('form_templates');
        require_code('urls');
        require_code('images');
        require_code('tempcode');

        $map = [
            do_lang_tempcode('IMAGE'),
            do_lang_tempcode('ACHIEVEMENT'),
            do_lang_tempcode('ACHIEVEMENT_QUALIFICATIONS'),
        ];
        $header_row = results_header_row($map);

        $result_entries = new Tempcode();

        foreach ($data as $achievement_name => $achievement_details) {
            // Process image
            $image = $achievement_details['image'];
            if ($image !== null) {
                if (!looks_like_url($image)) {
                    $_image = get_custom_file_base() . '/' . $image;
                    if (!is_image($_image, IMAGE_CRITERIA_WEBSAFE)) {
                        $image = find_theme_image($image, true);
                    }
                } elseif (!is_image($image, IMAGE_CRITERIA_WEBSAFE)) {
                    $image = '';
                }
            } else {
                $image = '';
            }

            $image_tempcode = new Tempcode();
            if ($image != '') {
                $image_thumb = symbol_tempcode('THUMBNAIL', [$image, strval(100)]);
                $image_tempcode->attach('<img src="' . escape_html($image_thumb->evaluate()) . '" alt="' . escape_html($achievement_details['title']) . '" />');
            }

            // Process title (we add a progress bar below it)
            $title = new Tempcode();
            $title->attach(do_template('ANCHOR', ['_GUID' => '460996116cc958e09719cc8dde6c9ee2', 'NAME' => 'achievement_' . $achievement_name]));
            $title->attach(escape_html($achievement_details['title']));
            $additional_classes = null;
            if ($achievement_details['unlocked'] === true) { // A 100% progress does not necessarily mean unlocked; it might have been a read-only achievement
                $additional_classes = 'progress-complete';
            } elseif ($achievement_details['read_only'] === true) {
                $additional_classes = 'progress-read-only';
            }
            $progress_tpl = do_template('ACHIEVEMENT_PROGRESS', [
                '_GUID' => 'ae16de932ca556909d19fd7c1dd37dfd',
                'PROGRESS' => float_to_raw_string($achievement_details['total_progress_percentile'] * 100.0),
                'PROGRESS_TITLE' => do_lang_tempcode('ACHIEVEMENT_PROGRESS_TOOLTIP', escape_html(float_format($achievement_details['total_progress_percentile'] * 100.0))),
                'ADDITIONAL_CLASSES' => $additional_classes,
            ]);
            $title->attach(paragraph($progress_tpl));
            if ($achievement_details['read_only'] === true) {
                $title->attach(paragraph(do_lang_tempcode('ACHIEVEMENT_READ_ONLY')));
            }
            if ($achievement_details['permanent'] === true) {
                $title->attach(paragraph(do_lang_tempcode('ACHIEVEMENT_PERMANENT')));
            }
            if (addon_installed('points') && ($achievement_details['points'] > 0)) {
                $title->attach(paragraph(do_lang_tempcode('ACHIEVEMENT_POINTS', escape_html(integer_format($achievement_details['points'])))));
            }

            // Process qualifications
            $qualifications = new Tempcode();
            foreach ($achievement_details['qualification_groups'] as $i => $qualification_group) {
                if ($i > 0) {
                    $qualifications->attach(do_template('ACHIEVEMENT_QUALIFICATIONS_OR'));
                }
                foreach ($qualification_group['qualifications'] as $qualification) {
                    if ($qualification['text'] === null) {
                        continue;
                    }
                    $qualifications->attach($qualification['text']);
                }
            }

            $map = [$image_tempcode, $title, $qualifications];
            $result_entries->attach(results_entry($map, true));
        }

        $results_table = results_table(do_lang_tempcode('ACHIEVEMENTS'), 0, 'start', 1000, 'max', count($data), $header_row, $result_entries);

        // We do not internalise since we have no pagination nor sorting
        return do_template('RESULTS_TABLE_SCREEN', [
            '_GUID' => 'faca19dbf9ac531c9bb6f03f92d8ec5c',
            'TITLE' => $this->title,
            'TEXT' => do_lang_tempcode('DESCRIPTION_ACHIEVEMENTS'),
            'RESULTS_TABLE' => $results_table,
        ]);
    }
}
