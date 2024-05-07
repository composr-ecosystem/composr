<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_support_credits
 */

/**
 * Module page class.
 */
class Module_admin_customers
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
        $info['version'] = 3;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'cms_homesite_support_credits';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        /* NB: Does not delete CPFs and multi-mods. But that doesn't actually matter */
        $tables = [
            'credit_purchases',
            'credit_charge_log',
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
        if (get_forum_type() != 'cns') {
            return; // Conversr only
        }

        if ($upgrade_from === null) {
            require_lang('customers');

            // CPFs...

            require_code('cns_members_action');
            require_code('cns_members_action2');
            require_code('customers');
            $cur_id = get_credits_profile_field_id('cms_currency');
            if ($cur_id !== null) {
                $GLOBALS['FORUM_DB']->query_update('f_custom_fields', ['cf_owner_view' => 1, 'cf_owner_set' => 1], ['id' => $cur_id], '', 1);
            }
            cns_make_custom_field('cms_support_credits', 1, '', '', 0, 1, 0, 0, 'integer', 0, 0, 0, null, '', 0, '', 0, 0, '', '', '', true);
            cns_make_custom_field('cms_ftp_host', 0, do_lang('ENCRYPTED_TO_WEBSITE'), '', 0, 1, 1, 1, 'short_text', 0, 0, 0, null, '', 0, '', 0, 0, '', '', '', true);
            cns_make_custom_field('cms_ftp_path', 0, do_lang('ENCRYPTED_TO_WEBSITE'), '', 0, 1, 1, 1, 'short_text', 0, 0, 0, null, '', 0, '', 0, 0, '', '', '', true);
            cns_make_custom_field('cms_ftp_username', 0, do_lang('ENCRYPTED_TO_WEBSITE'), '', 0, 1, 1, 1, 'short_text', 0, 0, 0, null, '', 0, '', 0, 0, '', '', '', true);
            cns_make_custom_field('cms_ftp_password', 0, do_lang('ENCRYPTED_TO_WEBSITE'), '', 0, 1, 1, 1, 'password', 0, 0, 0, null, '', 0, '', 0, 0, '', '', '', true);
            cns_make_custom_field('cms_profession', 0, '', do_lang('CUSTOMER_PROFESSION_CPF_LIST'), 0, 1, 1, 0, 'list', 0, 0, 0, null, '', 0, '', 0, 0, '', '', '', true);

            // Credit logging...

            $GLOBALS['SITE_DB']->create_table('credit_purchases', [
                'id' => '*AUTO',
                'member_id' => 'MEMBER',
                'num_credits' => 'INTEGER',
                'date_and_time' => 'TIME',
                'purchase_validated' => 'BINARY',
                'is_manual' => 'BINARY',
            ]);

            $GLOBALS['SITE_DB']->create_table('credit_charge_log', [
                'id' => '*AUTO',
                'member_id' => 'MEMBER',
                'charging_member_id' => 'MEMBER',
                'num_credits' => 'INTEGER',
                'date_and_time' => 'TIME',
                'reason' => 'SHORT_TEXT',
            ]);

            // Multi-moderations...

            require_code('cns_moderation_action');
            cns_make_multi_moderation(do_lang('TICKET_MM_TAKE_OWNERSHIP'), do_lang('TICKET_MM_TAKE_OWNERSHIP_POST'), null, null, null, '*');
            cns_make_multi_moderation(do_lang('TICKET_MM_QUOTE'), do_lang('TICKET_MM_QUOTE_POST'), null, null, null, '*');
            cns_make_multi_moderation(do_lang('TICKET_MM_PRICE'), do_lang('TICKET_MM_PRICE_POST'), null, null, null, '*');
            cns_make_multi_moderation(do_lang('TICKET_MM_CLOSE'), do_lang('TICKET_MM_CLOSE_POST'), null, null, null, '*');
            cns_make_multi_moderation(do_lang('TICKET_MM_CHARGED'), do_lang('TICKET_MM_CHARGED_POST'), null, null, null, '*');
            cns_make_multi_moderation(do_lang('TICKET_MM_NOT_FOR_FREE'), do_lang('TICKET_MM_NOT_FOR_FREE_POST'), null, null, null, '*');
            cns_make_multi_moderation(do_lang('TICKET_MM_FREE_WORK'), do_lang('TICKET_MM_FREE_WORK_POST'), null, null, null, '*');
            cns_make_multi_moderation(do_lang('TICKET_MM_FREE_CREDITS'), do_lang('TICKET_MM_FREE_CREDITS_POST'), null, null, null, '*');
        }

        if (($upgrade_from !== null) && ($upgrade_from < 3)) { // LEGACY
            $GLOBALS['SITE_DB']->alter_table_field('credit_purchases', 'purchase_id', '*AUTO', 'id');

            // Add a ledger record in for support credit points; we are no longer calculating these on runtime.
            if (addon_installed('points')) {
                require_code('points2');
                $credits = $GLOBALS['SITE_DB']->query_select('credit_purchases', ['SUM(num_credits) as credits', 'member_id'], ['purchase_validated' => 1], ' GROUP BY member_id');
                foreach ($credits as $credit) {
                    points_credit_member($credit['member_id'], 'Upgrader: Importing legacy support credit points as a ledger item', (50 * $credit['credits']), 0, null, 0, 'legacy', 'upgrader', 'support_credits');
                }
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
        if (!addon_installed('cms_homesite_support_credits')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return [];
        }

        return [
            'browse' => ['CHARGE_CUSTOMER', 'admin/tool'],
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
        if (!addon_installed__messaged('cms_homesite_support_credits', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('tickets')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('tickets')));
        }
        if (!addon_installed('ecommerce')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
        }
        if (!addon_installed('points')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
        }

        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        }

        $type = get_param_string('type', 'browse');

        require_lang('customers');
        require_lang('points');

        $this->title = get_screen_title('CHARGE_CUSTOMER');

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

        if ($type == 'charge') {
            return $this->charge();
        }
        if ($type == '_charge') {
            return $this->_charge();
        }
        if ($type == 'browse') {
            return $this->charge();
        }

        return new Tempcode();
    }

    /**
     * The UI to charge a customer.
     *
     * @return Tempcode The UI
     */
    public function charge() : object
    {
        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        }

        require_code('form_templates');
        require_code('customers');

        $post_url = build_url(['page' => '_SELF', 'type' => '_charge'], '_SELF');
        $submit_name = do_lang_tempcode('CHARGE');

        $username = get_param_string('username', null, INPUT_FILTER_GET_IDENTIFIER);
        if ($username === null) {
            $member_id = get_param_integer('member_id', null);
            if ($member_id !== null) {
                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id, false, USERNAME_DEFAULT_BLANK);
            } else {
                $username = '';
            }
        } else {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
        }

        $fields = new Tempcode();
        $fields->attach(form_input_username(do_lang_tempcode('USERNAME'), '', 'member_username', $username, true));
        $fields->attach(form_input_integer(do_lang_tempcode('AMOUNT'), do_lang_tempcode('CREDIT_AMOUNT_DESCRIPTION'), 'amount', get_param_integer('amount', 3), true));
        $fields->attach(form_input_tick(do_lang_tempcode('ALLOW_OVERDRAFT'), do_lang_tempcode('DESCRIPTION_ALLOW_OVERDRAFT'), 'allow_overdraft', true));
        $fields->attach(form_input_line(do_lang_tempcode('REASON'), 'If for a ticket, you can just paste in the ticket URL.', 'reason', '', true));

        if ($member_id !== null) {
            $cpf_id = get_credits_profile_field_id();
            if ($cpf_id === null) {
                $msg_tpl = warn_screen($this->title, do_lang_tempcode('INVALID_FIELD_ID'));
                return $msg_tpl;
            }
            $num_credits = 0;
            if ($cpf_id !== null) {
                require_code('cns_members_action2');
                $_fields = cns_get_custom_field_mappings($member_id);
                $num_credits = $_fields['field_' . strval($cpf_id)];
                if ($num_credits === null) {
                    $num_credits = 0;
                }
            }

            $text = paragraph(do_lang_tempcode('CUSTOMER_CURRENTLY_HAS', escape_html(integer_format($num_credits, 0))));
        } else {
            $text = new Tempcode();
        }

        require_code('templates_columned_table');
        $rows = new Tempcode();
        $logs = $GLOBALS['SITE_DB']->query_select('credit_charge_log', ['charging_member_id', 'num_credits', 'date_and_time', 'reason'], ['member_id' => $member_id], 'ORDER BY date_and_time DESC', 10);
        foreach ($logs as $log) {
            $charging_username = $GLOBALS['FORUM_DRIVER']->get_username($log['charging_member_id'], false, USERNAME_DEFAULT_DELETED);
            $_num_credits = integer_format($log['num_credits']);
            $date = get_timezoned_date_time($log['date_and_time']);
            $reason = $log['reason'];
            $rows->attach(columned_table_row([$charging_username, $_num_credits, $date, $reason], true));
        }
        if (!$rows->is_empty()) {
            $_header_row = [
                do_lang_tempcode('USERNAME'),
                do_lang_tempcode('AMOUNT'),
                do_lang_tempcode('DATE_TIME'),
                do_lang_tempcode('REASON'),
            ];
            $header_row = columned_table_header_row($_header_row);
            $text->attach(do_template('COLUMNED_TABLE', ['_GUID' => '032e4dcb1d4224ed6633679154b6d827', 'HEADER_ROW' => $header_row, 'ROWS' => $rows, 'NONRESPONSIVE' => false]));
        }

        return do_template('FORM_SCREEN', [
            '_GUID' => 'f91185ee725f47ffa652d5fef8d85c0b',
            'TITLE' => $this->title,
            'HIDDEN' => '',
            'TEXT' => $text,
            'FIELDS' => $fields,
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => $submit_name,
            'URL' => $post_url,
        ]);
    }

    /**
     * The actualiser to charge a customer.
     *
     * @return Tempcode The UI
     */
    public function _charge() : object
    {
        $username = post_param_string('member_username', false, INPUT_FILTER_POST_IDENTIFIER);
        $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
        if (($member_id === null) || (is_guest($member_id))) {
            warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($username)), false, false, 404);
        }
        $amount = post_param_integer('amount');

        require_code('customers');
        $cpf_id = get_credits_profile_field_id();
        if ($cpf_id === null) {
            $msg_tpl = warn_screen($this->title, do_lang_tempcode('INVALID_FIELD_ID'));
            return $msg_tpl;
        }

        // Increment the number of credits this customer has
        require_code('cns_members_action2');
        $fields = cns_get_custom_field_mappings($member_id);

        // Work out new total credits
        $new_amount = $fields['field_' . strval($cpf_id)] - $amount;
        if (post_param_integer('allow_overdraft', 0) == 0) {
            if ($new_amount < 0) {
                $new_amount = 0;
                $amount = $fields['field_' . strval($cpf_id)] - $new_amount;
            }
        }

        cns_set_custom_field($member_id, $cpf_id, strval($new_amount));

        $GLOBALS['SITE_DB']->query_insert('credit_charge_log', [
            'member_id' => $member_id,
            'charging_member_id' => get_member(),
            'num_credits' => $amount,
            'date_and_time' => time(),
            'reason' => post_param_string('reason', ''),
        ]);

        log_it('CHARGE_CUSTOMER', strval($member_id), strval($amount));

        // Show it worked / Refresh
        $url = build_url(['page' => '_SELF', 'type' => 'browse', 'username' => $username], '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }
}
