<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    health_check
 */

/**
 * Module page class.
 */
class Module_admin_health_check
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions.
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user).
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name).
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled).
     */
    public function get_entry_points($check_perms = true, $member_id = null, $support_crosslinks = true, $be_deferential = false)
    {
        return array(
            'browse' => array('HEALTH_CHECK', 'menu/adminzone/tools/health_check'),
        );
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        require_code('health_check');

        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            $this->title = get_screen_title('HEALTH_CHECK');
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            return $this->browse();
        }

        return new Tempcode();
    }

    /**
     * Show a log of referrals.
     *
     * @return Tempcode The UI
     */
    public function browse()
    {
        if (cms_srv('REQUEST_METHOD') == 'POST') {
            $sections_to_run = isset($_POST['sections_to_run']) ? $_POST['sections_to_run'] : array();
            if (@get_magic_quotes_gpc()) {
                $sections_to_run = array_map('stripslashes', $sections_to_run);
            }

            $passes = (post_param_integer('passes', 0) == 1);
            $skips = (post_param_integer('skips', 0) == 1);
            $manual_checks = (post_param_integer('manual_checks', 0) == 1);
        } else {
            $sections_to_run = (get_option('hc_cron_sections_to_run') == '') ? array() : explode(',', get_option('hc_cron_sections_to_run'));

            $passes = true;
            $skips = true;
            $manual_checks = true;
        }

        $automatic_repair = false; // We don't want this in the UI, it's implemented for possible future use only

        $sections = create_selection_list_health_check_sections($sections_to_run);

        if (cms_srv('REQUEST_METHOD') == 'POST') {
            $has_fails = false;
            $categories = run_health_check($has_fails, $sections_to_run, $passes, $skips, $manual_checks, $automatic_repair);

            $results = do_template('HEALTH_CHECK_RESULTS', array('_GUID' => 'f428b63eacc19bdd041d2b1a1d2f1155', 'CATEGORIES' => $categories));
        } else {
            $results = null;
        }

        return do_template('HEALTH_CHECK_SCREEN', array(
            '_GUID' => '49bd5111885d47974a4718acc5316f41',
            'TITLE' => $this->title,
            'SECTIONS' => $sections,
            'PASSES' => $passes,
            'SKIPS' => $skips,
            'MANUAL_CHECKS' => $manual_checks,
            'RESULTS' => $results,
        ));
    }
}
