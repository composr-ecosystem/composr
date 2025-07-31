<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    photo_verification
 */

/**
 * Module page class.
 */
class Module_photo_verification
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Patrick Schmalstig';
        $info['organisation'] = 'PDStig, LLC';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'photo_verification';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        if (addon_installed('tickets')) {
            require_code('tickets2');
            $ticket_type_id = $GLOBALS['SITE_DB']->query_select_value_if_there('ticket_types', 'id', [$GLOBALS['SITE_DB']->translate_field_ref('ticket_type_name') => 'Verification request']);
            if ($ticket_type_id !== null) {
                delete_ticket_type($ticket_type_id);
            }
        }
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
            if (addon_installed('tickets')) {
                require_code('tickets2');
                add_ticket_type('Verification request', 0, 0);
            }
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
        if (!addon_installed('photo_verification')) {
            return null;
        }
        if (!addon_installed('tickets')) {
            return null;
        }

        return [
            'browse' => ['AGE_IDENTITY_VERIFICATION', 'buttons/yes'],
        ];
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('photo_verification', $error_msg)) {
            return $error_msg;
        }
        if (!addon_installed__messaged('tickets', $error_msg)) {
            return $error_msg;
        }

        require_lang('photo_verification');

        $this->title = get_screen_title('AGE_IDENTITY_VERIFICATION');

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

        if ($type == 'browse') {
            return $this->ui();
        }
        if ($type == 'submit') {
            return $this->submit();
        }

        return new Tempcode();
    }

    /**
     * The UI to submit verification.
     *
     * @return Tempcode The UI
     */
    public function ui() : object
    {
        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        // Only supports Conversr
        if (get_forum_type() != 'cns') {
            return inform_screen($this->title, do_lang_tempcode('DESCRIPTION_VERIFICATION_NO_CNS'));
        }

        // Probation members may not get verified right now
        require_code('cns_general');
        $info = cns_read_in_member_profile(get_member(), ['on_probation_until']);
        if (array_key_exists('on_probation_until', $info) && ($info['on_probation_until'] > time())) {
            return warn_screen($this->title, do_lang_tempcode('DESCRIPTION_NO_VERIFY_PROBATION'));
        }

        // Generate a random verification code and cache it (better than using a hidden field which can be hacked)
        require_code('caches');
        require_code('caches2');
        $code = get_cache_entry('photo_verification', serialize([get_member()]), CACHE_AGAINST_MEMBER, 60 * 24);
        if ($code === null) {
            require_code('crypt');
            $code = cms_strtoupper_ascii(get_secure_random_string(8));
            set_cache_entry('photo_verification', 60 * 24, serialize([get_member()]), $code, CACHE_AGAINST_MEMBER);
        }

        // Build the form
        require_code('form_templates');
        $fields = new Tempcode();
        $fields->attach(form_input_text(do_lang_tempcode('VERIFICATION_CODE'), do_lang_tempcode('DESCRIPTION_VERIFICATION_CODE'), 'verification_code', $code, false, true));
        $fields->attach(form_input_upload(do_lang_tempcode('VERIFICATION_PHOTO'), do_lang_tempcode('DESCRIPTION_VERIFICATION_PHOTO'), 'verification', true, null, null, true, 'jpg,png,jpeg,gif,tif,tiff,webp,bmp'));

        $text = load_comcode_page('site/pages/comcode_custom/' . get_lang(get_member()) . '/_photo_verification.txt', 'site', '_photo_verification');

        $map = ['page' => '_SELF', 'type' => 'submit'];
        $url = build_url($map, '_SELF');

        return do_template('FORM_SCREEN', [
            '_GUID' => 'e762b453af4756228747bd331c743e9a',
            'HIDDEN' => new Tempcode(),
            'TITLE' => $this->title,
            'FIELDS' => $fields,
            'TEXT' => $text,
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'URL' => $url,
            'JS_FUNCTION_CALLS' => [],
        ]);
    }

    /**
     * The actualiser for submitting a verification request to the Support Tickets system.
     * We don't post to the tickets module directly because we need a secure way to recover the cached verification code without enabling a way to manipulate it (e.g. the DOM).
     *
     * @return object
     */
    public function submit() : object
    {
        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        // Recover our verification code
        require_code('caches');
        $code = get_cache_entry('photo_verification', serialize([get_member()]), CACHE_AGAINST_MEMBER, 60 * 24);
        if ($code === null) {
            return warn_screen($this->title, do_lang_tempcode('VERIFICATION_CODE_EXPIRED'));
        }

        @ignore_user_abort(true); // Must keep going till completion from this point on

        // Get our uploaded photo (save to a special directory which we git ignore, and also obfuscate its filename)
        require_code('uploads');
        $photo = get_url('verification_url', 'verification', 'uploads/verification', OBFUSCATE_LEAVE_SUFFIX, CMS_UPLOAD_IMAGE);

        require_code('tickets');
        require_code('tickets2');
        require_lang('tickets');

        // Prepare the support ticket
        $ticket_id = ticket_generate_new_id();
        $ticket_type_id = $GLOBALS['SITE_DB']->query_select_value_if_there('ticket_types', 'id', [$GLOBALS['SITE_DB']->translate_field_ref('ticket_type_name') => 'Verification request']);
        if ($ticket_type_id === null) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('931d695136935e4fbbebaa8a9ef451ee')));
        }

        $post = do_lang('VERIFICATION_TICKET_BODY', comcode_escape($code), comcode_escape($photo[0]));

        // Spam check
        require_code('antispam');
        inject_action_spamcheck(null, null);

        // Stats logging
        if (addon_installed('stats')) {
            require_code('stats');
            log_stats_event(do_lang('FORM', null, null, null, get_site_default_lang()) . '-' . 'Verification request');
        }

        // Add post to ticket...

        $ticket_url = ticket_add_post($ticket_id, $ticket_type_id, 'Verification request ' . $code, $post, false);

        // Eat the cache entry so the code cannot be used again...
        require_code('caches2');
        set_cache_entry('photo_verification', 0, serialize([get_member()]), '', CACHE_AGAINST_MEMBER);

        // Log it...

        log_it('LOG_VERIFICATION_REQUEST', strval($ticket_id), $code);

        // Auto-monitor...

        if ((has_privilege(get_member(), 'support_operator')) && (get_option('ticket_auto_assign') == '1')) {
            require_code('notifications');
            set_notifications('ticket_assigned_staff', $ticket_id);
        }

        // Send e-mail...

        // Find true ticket title
        list($ticket_title, $topic_id) = get_ticket_meta_details($ticket_id);

        $email = $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member());
        send_ticket_email($ticket_id, $ticket_title, $post, $ticket_url, ($ticket_type_id !== null) ? $email : '', $ticket_type_id, null);

        // Redirect...

        $url = build_url(['page' => 'tickets', 'type' => 'ticket', 'id' => $ticket_id], '_SELF');
        if (get_param_string('redirect', '', INPUT_FILTER_URL_INTERNAL) != '') {
            $url = make_string_tempcode(get_param_string('redirect', false, INPUT_FILTER_URL_INTERNAL));
        }
        return redirect_screen($this->title, $url, do_lang_tempcode('TICKET_STARTED'));
    }
}
