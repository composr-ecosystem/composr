<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    referrals
 */

/**
 * Module page class.
 */
class Module_admin_referrals
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'referrals';
        return $info;
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
        if (!addon_installed('referrals')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        return [
            'browse' => ['REFERRALS', 'spare/referrals'],
        ];
    }

    public $title;
    public $scheme;
    public $ini_file;
    public $scheme_title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('referrals', $error_msg)) {
            return $error_msg;
        }

        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        }

        $type = get_param_string('type', 'browse');

        require_code('files');
        require_lang('referrals');

        if ($type == 'browse') {
            $this->title = get_screen_title('REFERRALS');
        }

        if ($type == 'adjust' || $type == '_adjust') {
            $scheme = get_param_string('scheme');
            $path = get_custom_file_base() . '/text_custom/referrals.txt';
            if (!is_file($path)) {
                $path = get_file_base() . '/text_custom/referrals.txt';
            }
            $ini_file = cms_parse_ini_file_safe($path, true);

            if (!array_key_exists($scheme, $ini_file)) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }

            $scheme_title = $ini_file[$scheme]['title'];

            $this->title = get_screen_title('MANUALLY_ADJUST_SCHEME_SETTINGS', true, [escape_html($scheme_title)]);

            $this->scheme = $scheme;
            $this->ini_file = $ini_file;
            $this->scheme_title = $scheme_title;
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
        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        }

        require_code('referrals');

        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            return $this->browse();
        }
        if ($type == 'adjust') {
            return $this->adjust();
        }
        if ($type == '_adjust') {
            return $this->_adjust();
        }

        return new Tempcode();
    }

    /**
     * Show a log of referrals.
     *
     * @return Tempcode The UI
     */
    public function browse() : object
    {
        require_lang('referrals');

        $table = referrer_report_script(true);

        $out = new Tempcode();
        $out->attach($this->title);
        $out->attach($table);
        return $out;
    }

    /**
     * The UI to adjust settings for a referrer.
     *
     * @return Tempcode The UI
     */
    public function adjust() : object
    {
        $scheme = $this->scheme;
        $ini_file = $this->ini_file;
        $scheme_title = $this->scheme_title;

        $member_id = get_param_integer('member_id');

        require_code('form_templates');

        $post_url = build_url(['page' => '_SELF', 'type' => '_adjust', 'scheme' => $scheme, 'member_id' => $member_id], '_SELF');
        $submit_name = do_lang_tempcode('SAVE');

        list($num_total_qualified_by_referrer) = get_referral_scheme_stats_for($member_id, $scheme);

        $referrals_count = $num_total_qualified_by_referrer;
        $is_qualified = $GLOBALS['SITE_DB']->query_select_value_if_there('referrer_override', 'o_is_qualified', ['o_referring_member' => $member_id, 'o_scheme_name' => $scheme]);

        $fields = new Tempcode();
        $fields->attach(form_input_integer(do_lang_tempcode('QUALIFIED_REFERRALS_COUNT'), '', 'referrals_count', $referrals_count, true));
        $is_qualified_list = new Tempcode();
        $is_qualified_list->attach(form_input_list_entry('', $is_qualified === null, do_lang_tempcode('IS_QUALIFIED_DETECT')));
        $is_qualified_list->attach(form_input_list_entry('1', $is_qualified === 1, do_lang_tempcode('YES')));
        $is_qualified_list->attach(form_input_list_entry('0', $is_qualified === 0, do_lang_tempcode('NO')));
        $fields->attach(form_input_list(do_lang_tempcode('IS_QUALIFIED'), '', 'is_qualified', $is_qualified_list, null, false, false));

        require_code('form_templates');
        list($warning_details, $ping_url) = handle_conflict_resolution(strval($member_id));

        return do_template('FORM_SCREEN', [
            '_GUID' => '7e28b416287fb891c2cd7029795e49f0',
            'TITLE' => $this->title,
            'HIDDEN' => '',
            'TEXT' => '',
            'FIELDS' => $fields,
            'SUBMIT_ICON' => 'buttons/save',
            'SUBMIT_NAME' => $submit_name,
            'URL' => $post_url,
            'WARNING_DETAILS' => $warning_details,
            'PING_URL' => $ping_url,
        ]);
    }

    /**
     * The actualiser to adjust settings for a referrer.
     *
     * @return Tempcode The UI
     */
    public function _adjust() : object
    {
        $scheme = $this->scheme;
        $ini_file = $this->ini_file;
        $scheme_title = $this->scheme_title;

        $member_id = get_param_integer('member_id');

        list($old_referrals_count) = get_referral_scheme_stats_for($member_id, $scheme);
        list($num_total_qualified_by_referrer) = get_referral_scheme_stats_for($member_id, $scheme, true);
        $referrals_count = post_param_integer('referrals_count');
        $referrals_dif = $referrals_count - $num_total_qualified_by_referrer;
        $is_qualified = post_param_integer('is_qualified', null);

        // Save
        $GLOBALS['SITE_DB']->query_delete('referrer_override', [
            'o_referring_member' => $member_id,
            'o_scheme_name' => $scheme,
        ]);
        $GLOBALS['SITE_DB']->query_insert('referrer_override', [
            'o_referring_member' => $member_id,
            'o_scheme_name' => $scheme,
            'o_referrals_dif' => $referrals_dif,
            'o_is_qualified' => $is_qualified,
        ]);

        log_it('_MANUALLY_ADJUST_SCHEME_SETTINGS', $scheme, strval($referrals_count - $old_referrals_count));

        // Show it worked / Refresh
        $member_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($member_id);
        return redirect_screen($this->title, $member_url, do_lang_tempcode('SUCCESS'));
    }
}
