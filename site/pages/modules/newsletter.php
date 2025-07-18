<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    newsletter
 */

/**
 * Module page class.
 */
class Module_newsletter
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
        $info['version'] = 12;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('newsletter_subscribers');
        $GLOBALS['SITE_DB']->drop_table_if_exists('newsletters');
        $GLOBALS['SITE_DB']->drop_table_if_exists('newsletter_archive');
        $GLOBALS['SITE_DB']->drop_table_if_exists('newsletter_subscribe');
        $GLOBALS['SITE_DB']->drop_table_if_exists('newsletter_drip_send');
        $GLOBALS['SITE_DB']->drop_table_if_exists('newsletter_periodic');

        delete_value('newsletter_whatsnew');
        delete_value('newsletter_send_time');

        delete_privilege('change_newsletter_subscriptions');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        if (is_null($upgrade_from)) {
            require_lang('newsletter');

            $GLOBALS['SITE_DB']->create_table('newsletter_subscribers', array(
                'id' => '*AUTO',
                'email' => 'SHORT_TEXT',
                'join_time' => 'TIME',
                'code_confirm' => 'INTEGER',
                'the_password' => 'SHORT_TEXT',
                'pass_salt' => 'ID_TEXT',
                'language' => 'ID_TEXT',
                'n_forename' => 'SHORT_TEXT',
                'n_surname' => 'SHORT_TEXT',
            ));
            $GLOBALS['SITE_DB']->create_index('newsletter_subscribers', 'welcomemails', array('join_time'));
            $GLOBALS['SITE_DB']->create_index('newsletter_subscribers', 'code_confirm', array('code_confirm'));

            $GLOBALS['SITE_DB']->create_table('newsletter_archive', array(
                'id' => '*AUTO',
                'date_and_time' => 'INTEGER',
                'subject' => 'SHORT_TEXT',
                'newsletter' => 'LONG_TEXT',
                'language' => 'ID_TEXT',
                'importance_level' => 'INTEGER'
            ));

            add_privilege('NEWSLETTER', 'change_newsletter_subscriptions', false);

            $GLOBALS['SITE_DB']->create_table('newsletters', array(
                'id' => '*AUTO',
                'title' => 'SHORT_TRANS',
                'description' => 'LONG_TRANS',
            ));

            $map = array();
            require_code('lang3');
            $map += lang_code_to_default_content('title', 'GENERAL');
            $map += lang_code_to_default_content('description', 'NEWSLETTER_GENERAL');
            $GLOBALS['SITE_DB']->query_insert('newsletters', $map);

            $GLOBALS['SITE_DB']->create_table('newsletter_subscribe', array(
                'newsletter_id' => '*AUTO_LINK',
                'the_level' => 'SHORT_INTEGER',
                'email' => '*SHORT_TEXT',
            ), false, false, true);
            $GLOBALS['SITE_DB']->create_index('newsletter_subscribe', 'peopletosendto', array('the_level'));

            $GLOBALS['SITE_DB']->create_table('newsletter_drip_send', array(
                'id' => '*AUTO',
                'd_inject_time' => 'TIME',
                'd_subject' => 'SHORT_TEXT',
                'd_message' => 'LONG_TEXT',
                'd_html_only' => 'BINARY',
                'd_to_email' => 'SHORT_TEXT',
                'd_to_name' => 'SHORT_TEXT',
                'd_from_email' => 'SHORT_TEXT',
                'd_from_name' => 'SHORT_TEXT',
                'd_priority' => 'SHORT_INTEGER',
                'd_template' => 'ID_TEXT',
            ));
            $GLOBALS['SITE_DB']->create_index('newsletter_drip_send', 'd_inject_time', array('d_inject_time'));
        }

        if ((is_null($upgrade_from)) || ($upgrade_from < 9)) {
            $GLOBALS['SITE_DB']->create_table('newsletter_periodic', array(
                'id' => '*AUTO',
                'np_message' => 'LONG_TEXT',
                'np_subject' => 'LONG_TEXT',
                'np_lang' => 'LANGUAGE_NAME',
                'np_send_details' => 'LONG_TEXT',
                'np_html_only' => 'BINARY',
                'np_from_email' => 'SHORT_TEXT',
                'np_from_name' => 'SHORT_TEXT',
                'np_priority' => 'SHORT_INTEGER',
                'np_csv_data' => 'LONG_TEXT',
                'np_frequency' => 'SHORT_TEXT',
                'np_day' => 'SHORT_INTEGER',
                'np_in_full' => 'BINARY',
                'np_template' => 'ID_TEXT',
                'np_last_sent' => 'TIME',
            ));
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 9)) {
            $GLOBALS['SITE_DB']->add_table_field('newsletter_drip_send', 'd_template', 'ID_TEXT');
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 11)) {
            $GLOBALS['SITE_DB']->rename_table('newsletter', 'newsletter_subscribers');

            $GLOBALS['SITE_DB']->alter_table_field('newsletter_subscribers', 'the_password', 'SHORT_TEXT');

            $GLOBALS['SITE_DB']->delete_index_if_exists('newsletter_drip_send', '#d_message');
            $GLOBALS['SITE_DB']->create_index('newsletter_drip_send', '#d_message', array('d_message'));
        }

        if ((is_null($upgrade_from)) || ($upgrade_from < 11)) {
            $GLOBALS['SITE_DB']->create_index('newsletter_drip_send', 'd_to_email', array('d_to_email'));
        }

        if ((is_null($upgrade_from)) || ($upgrade_from < 12)) {
            $GLOBALS['SITE_DB']->create_index('newsletter_subscribers', 'email', array('email'));
        }
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
        if ($check_perms) {
            if ($GLOBALS['SITE_DB']->query_select_value('newsletters', 'COUNT(*)') == 0) {
                return array();
            }
        }
        return array(
            'browse' => array('NEWSLETTER_JOIN', 'menu/site_meta/newsletters'),
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
        $type = get_param_string('type', 'browse');

        require_lang('newsletter');

        if ($type == 'browse') {
            $this->title = get_screen_title('_NEWSLETTER_JOIN', true, array(escape_html(get_option('newsletter_title'))));
            breadcrumb_set_self(do_lang_tempcode('NEWSLETTER'));
        }

        if ($type == 'unsub') {
            $this->title = get_screen_title('NEWSLETTER_UNSUBSCRIBED');
        }

        if ($type == 'reset') {
            breadcrumb_set_self(do_lang_tempcode('NEWSLETTER_PASSWORD_BEEN_RESET'));
            breadcrumb_set_parents(array(array('_SELF:_SELF:browse', do_lang_tempcode('NEWSLETTER'))));

            $this->title = get_screen_title(get_option('newsletter_title'), false);
        }

        if ($type == 'confirm') {
            breadcrumb_set_parents(array(array('_SELF:_SELF:browse', do_lang_tempcode('NEWSLETTER'))));

            $this->title = get_screen_title(get_option('newsletter_title'), false);
        }

        if ($type == 'do') {
            breadcrumb_set_parents(array(array('_SELF:_SELF:browse', do_lang_tempcode('NEWSLETTER'))));

            $this->title = get_screen_title('_NEWSLETTER_JOIN', true, array(escape_html(get_option('newsletter_title'))));
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
        require_code('newsletter');

        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            return $this->newsletter_form();
        }
        if ($type == 'confirm') {
            return $this->newsletter_confirm_joining();
        }
        if ($type == 'do') {
            return $this->newsletter_maintenance();
        }
        if ($type == 'reset') {
            return $this->newsletter_password_reset();
        }
        if ($type == 'unsub') {
            return $this->newsletter_unsubscribe();
        }

        return new Tempcode();
    }

    /**
     * The UI to sign up to the newsletter (actually, generally manage subscription).
     *
     * @return Tempcode The UI
     */
    public function newsletter_form()
    {
        $newsletters = $GLOBALS['SITE_DB']->query_select('newsletters', array('*'));
        if (count($newsletters) == 0) {
            inform_exit(do_lang_tempcode('NO_CATEGORIES'));
        }

        $post_url = build_url(array('page' => '_SELF', 'type' => 'do'), '_SELF');
        $submit_name = do_lang_tempcode('NEWSLETTER_JOIN');

        require_code('form_templates');

        url_default_parameters__enable();

        $forename = '';
        $surname = '';
        if (!is_guest()) {
            $their_email = get_param_string('email', $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member()));
            $username = $GLOBALS['FORUM_DRIVER']->get_username(get_member(), true);
            $parts = explode(' ', $username);
            if (count($parts) >= 2) {
                $surname = array_pop($parts);
                $forename = implode(' ', $parts);
            }

            /*
            Insecure, potential information leak
            $existing_record = $GLOBALS['SITE_DB']->query_select('newsletter_subscribers', array('n_forename', 'n_surname'), array('email' => $their_email), '', 1);
            if (array_key_exists(0, $existing_record)) {
                $forename = $existing_record[0]['n_forename'];
                $surname = $existing_record[0]['n_surname'];
            }*/
        } else {
            $their_email = get_param_string('email', '');
        }

        $message = get_option('newsletter_text');
        if (has_actual_page_access(get_member(), 'admin_config')) {
            if ($message != '') {
                $message .= ' [semihtml]<span class="associated_link"><a href="{$PAGE_LINK*,_SEARCH:admin_config:category:FEATURE#group_NEWSLETTER}">' . do_lang('EDIT') . '</a></span>[/semihtml]';
            }
        }
        $text = comcode_to_tempcode($message, null, true);

        // Build up the join form
        $fields = new Tempcode();
        $fields->attach(form_input_email(do_lang_tempcode('EMAIL_ADDRESS'), do_lang_tempcode('DESCRIPTION_SUBSCRIBE_ADDRESS'), 'email', $their_email, true));
        $fields->attach(form_input_line(do_lang_tempcode('FORENAME'), '', 'forename', $forename, false));
        $fields->attach(form_input_line(do_lang_tempcode('SURNAME'), '', 'surname', $surname, false));
        $fields->attach(form_input_password(do_lang_tempcode('YOUR_PASSWORD'), do_lang_tempcode('DESCRIPTION_MAINTENANCE_PASSWORD'), 'password', false));
        $fields->attach(form_input_password(do_lang_tempcode('CONFIRM_PASSWORD'), '', 'password_confirm', false));
        if (count(find_all_langs()) != 1) {
            $fields->attach(form_input_list(do_lang_tempcode('LANGUAGE'), '', 'lang', create_selection_list_langs(user_lang())));
        }
        $level = get_param_integer('level', null);
        if (is_null($level)) {
            $level = 3;
        }
        $l = form_input_list_entry('0', false, do_lang_tempcode('NEWSLETTER_0'));
        $l->attach(form_input_list_entry('1', $level == 1, do_lang_tempcode('NEWSLETTER_1')));
        $l->attach(form_input_list_entry('2', $level == 2, do_lang_tempcode('NEWSLETTER_2')));
        $l->attach(form_input_list_entry('3', $level == 3, do_lang_tempcode('NEWSLETTER_3')));
        $l->attach(form_input_list_entry('4', $level == 4, do_lang_tempcode('NEWSLETTER_4')));
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => 'a87e4be6cbc070e66e25ad4ece429cc4', 'TITLE' => do_lang_tempcode('NEWSLETTER_SUBSCRIPTIONS'))));
        foreach ($newsletters as $newsletter) {
            $newsletter_title = get_translated_text($newsletter['title']);
            $newsletter_description = get_translated_text($newsletter['description']);
            $GLOBALS['NO_DEV_MODE_FULLSTOP_CHECK'] = true;
            if (get_option('interest_levels') == '1') {
                $fields->attach(form_input_list(do_lang_tempcode('SUBSCRIPTION_LEVEL_FOR', make_string_tempcode(escape_html($newsletter_title))), do_lang_tempcode('DESCRIPTION_SUBSCRIPTION_LEVEL', escape_html($newsletter_description)), 'level' . strval($newsletter['id']), $l));
            } else {
                $fields->attach(form_input_tick(do_lang_tempcode('SUBSCRIBE_TO', make_string_tempcode(escape_html($newsletter_title))), make_string_tempcode(escape_html($newsletter_description)), 'level' . strval($newsletter['id']), $level != 0));
            }
        }

        if (addon_installed('captcha')) {
            require_code('captcha');
            if (use_captcha()) {
                $fields->attach(form_input_captcha());
                $text->attach(' ');
                $text->attach(do_lang_tempcode('FORM_TIME_SECURITY'));
            }
        }

        url_default_parameters__disable();

        $text->attach(paragraph(do_lang_tempcode('CHANGE_SETTINGS_BY_RESUBSCRIBING')));

        $javascript = "
            var form=document.getElementById('password').form;
            form.old_submit=form.onsubmit;
            form.onsubmit=function() {
                if ((form.elements['password_confirm']) && (form.elements['password_confirm'].value!=form.elements['password'].value))
                {
                    window.fauxmodal_alert('" . php_addslashes(do_lang('PASSWORD_MISMATCH')) . "');
                    return false;
                }
                if (typeof form.old_submit!='undefined' && form.old_submit) return form.old_submit();
                return true;
            };
        ";

        $javascript .= (function_exists('captcha_ajax_check') ? captcha_ajax_check() : '');

        return do_template('FORM_SCREEN', array('_GUID' => '24d7575465152f450c5a8e62650bf6c8', 'JAVASCRIPT' => $javascript, 'HIDDEN' => '', 'FIELDS' => $fields, 'SUBMIT_ICON' => 'buttons__proceed', 'SUBMIT_NAME' => $submit_name, 'URL' => $post_url, 'TITLE' => $this->title, 'TEXT' => $text));
    }

    /**
     * The actualiser for newsletter subscription maintenance (adding, updating, deleting).
     *
     * @return Tempcode The UI
     */
    public function newsletter_maintenance()
    {
        require_code('type_sanitisation');
        require_code('crypt');

        if (addon_installed('captcha')) {
            require_code('captcha');
            enforce_captcha();
        }

        // Add
        $email = trim(post_param_string('email'));
        $password = trim(post_param_string('password', ''));
        $forename = trim(post_param_string('forename'));
        $surname = trim(post_param_string('surname'));
        if ($password != trim(post_param_string('password_confirm', ''))) {
            warn_exit(make_string_tempcode(escape_html(do_lang('PASSWORD_MISMATCH'))));
        }
        $language = post_param_string('lang', user_lang());
        if (!is_email_address($email)) {
            return warn_screen($this->title, do_lang_tempcode('IMPROPERLY_FILLED_IN'));
        }

        $message = do_lang_tempcode('NEWSLETTER_UPDATE');
        $old_confirm = $GLOBALS['SITE_DB']->query_select_value_if_there('newsletter_subscribers', 'code_confirm', array('email' => $email));

        // New (or as new - replace old unconfirmed records)
        if ((is_null($old_confirm)) || ($old_confirm != 0)) {
            // As it is new we need to actually confirm you were setting some subscription settings
            $newsletters = $GLOBALS['SITE_DB']->query_select('newsletters', array('id'));
            $found_level = false;
            foreach ($newsletters as $newsletter) {
                if (get_option('interest_levels') == '1') {
                    $level = post_param_integer('level' . strval($newsletter['id']));
                } else {
                    $level = post_param_integer('level' . strval($newsletter['id']), 0);
                    if ($level == 1) {
                        $level = 4;
                    }
                }
                if ($level != 0) {
                    $found_level = true;
                }
            }
            if (!$found_level) {
                // No subscription settings
                warn_exit(do_lang_tempcode('NOT_NEWSLETTER_SUBSCRIBER'));
            }

            $code_confirm = is_null($old_confirm) ? mt_rand(1, mt_getrandmax()) : $old_confirm;
            if ($password == '') {
                $password = get_rand_password();
            }
            $salt = produce_salt();
            if (is_null($old_confirm)) {
                add_newsletter_subscriber($email, time(), $code_confirm, ratchet_hash($password, $salt, PASSWORD_SALT), $salt, $language, $forename, $surname);

                $this->_send_confirmation($email, $code_confirm, $password, $forename, $surname);
            } else {
                $id = $GLOBALS['SITE_DB']->query_select_value('newsletter_subscribers', 'id', array('email' => $email));
                edit_newsletter_subscriber($id, $email, time(), null, null, null, $language, $forename, $surname);

                $this->_send_confirmation($email, $code_confirm, null, $forename, $surname);
            }
            $message = do_lang_tempcode('NEWSLETTER_CONFIRM', escape_html($email));
        }

        // Existing, OR it is new and we are just proceeding to save the subscription settings...

        // Change/make settings
        $old_password = $GLOBALS['SITE_DB']->query_select_value('newsletter_subscribers', 'the_password', array('email' => $email));
        $old_salt = $GLOBALS['SITE_DB']->query_select_value('newsletter_subscribers', 'pass_salt', array('email' => $email));
        require_code('crypt');
        if ((!has_privilege(get_member(), 'change_newsletter_subscriptions')) && (!is_null($old_confirm)) && ($old_confirm == 0) && ($old_password != '') && (ratchet_hash_verify($password, $old_password, $old_salt))) { // Access denied. People who can change any subscriptions can't get denied.
            // Access denied to an existing record that was confirmed
            $_reset_url = build_url(array('page' => '_SELF', 'type' => 'reset', 'email' => $email), '_SELF');
            $reset_url = $_reset_url->evaluate();
            return warn_screen($this->title, do_lang_tempcode('NEWSLETTER_PASSWORD_RESET', escape_html($reset_url)));
        } else {
            // Access granted, make edit
            $newsletters = $GLOBALS['SITE_DB']->query_select('newsletters', array('id'));
            foreach ($newsletters as $newsletter) {
                if (get_option('interest_levels') == '1') {
                    $level = post_param_integer('level' . strval($newsletter['id']));
                } else {
                    $level = post_param_integer('level' . strval($newsletter['id']), 0);
                    if ($level == 1) {
                        $level = 4;
                    }
                }
                // First we delete
                $GLOBALS['SITE_DB']->query_delete('newsletter_subscribe', array('newsletter_id' => $newsletter['id'], 'email' => $email), '', 1);
                if ($level != 0) { // Then we put back if it's not a 0 level
                    $GLOBALS['SITE_DB']->query_insert('newsletter_subscribe', array('newsletter_id' => $newsletter['id'], 'email' => $email, 'the_level' => $level));
                }
            }

            // Update name etc if it's an edit
            if ((!is_null($old_confirm)) && ($old_confirm == 0)) {
                $id = $GLOBALS['SITE_DB']->query_select_value('newsletter_subscribers', 'id', array('email' => $email));
                edit_newsletter_subscriber($id, $email, null, null, null, null, null, $forename, $surname);
            }
        }

        // Done, show result
        return inform_screen($this->title, $message);
    }

    /**
     * The actualiser for resetting newsletter password.
     *
     * @return Tempcode The UI
     */
    public function newsletter_password_reset()
    {
        require_code('crypt');

        $email = trim(get_param_string('email'));
        $language = $GLOBALS['SITE_DB']->query_select_value('newsletter_subscribers', 'language', array('email' => $email));
        $salt = $GLOBALS['SITE_DB']->query_select_value('newsletter_subscribers', 'pass_salt', array('email' => $email));
        $new_password = produce_salt();
        $GLOBALS['SITE_DB']->query_update('newsletter_subscribers', array('the_password' => ratchet_hash($new_password, $salt, PASSWORD_SALT)), array('email' => $email), '', 1);

        $message = do_lang('NEWSLETTER_PASSWORD_CHANGE', comcode_escape(get_ip_address()), comcode_escape($new_password), null, $language);

        require_code('mail');
        mail_wrap(get_option('newsletter_title'), $message, array($email), $GLOBALS['FORUM_DRIVER']->get_username(get_member(), true));

        return inform_screen($this->title, protect_from_escaping(do_lang('NEWSLETTER_PASSWORD_BEEN_RESET', null, null, null, $language)));
    }

    /**
     * The actualiser for unsubscribing from the newsletter.
     *
     * @return Tempcode The UI
     */
    public function newsletter_unsubscribe()
    {
        $id = get_param_integer('id');
        $hash = get_param_string('hash');

        $_subscriber = $GLOBALS['SITE_DB']->query_select('newsletter_subscribers', array('*'), array('id' => $id), '', 1);
        if (!array_key_exists(0, $_subscriber)) {
            fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        $subscriber = $_subscriber[0];

        require_code('crypt');
        $needed_hash = ratchet_hash($subscriber['the_password'], 'xunsub');

        if ($hash != $needed_hash) {
            warn_exit(do_lang_tempcode('COULD_NOT_UNSUBSCRIBE'));
        }

        $GLOBALS['SITE_DB']->query_delete('newsletter_subscribe', array('email' => $subscriber['email']));

        return inform_screen($this->title, do_lang_tempcode('FULL_NEWSLETTER_UNSUBSCRIBED', escape_html(get_site_name())));
    }

    /**
     * Send a newsletter join confirmation.
     *
     * @param  SHORT_TEXT $email The e-mail address
     * @param  SHORT_TEXT $code_confirm The confirmation code
     * @param  ?SHORT_TEXT $password The newsletter password (null: password may not be viewed, because it's been permanently hashed already)
     * @param  string $forename Subscribers forename
     * @param  string $surname Subscribers surname
     */
    public function _send_confirmation($email, $code_confirm, $password, $forename, $surname)
    {
        if (is_null($password)) {
            $password = do_lang('NEWSLETTER_PASSWORD_ENCRYPTED');
        }

        $_url = build_url(array('page' => 'newsletter', 'type' => 'confirm', 'email' => $email, 'confirm' => $code_confirm), '_SELF', null, false, true);
        $url = $_url->evaluate();
        $newsletter_url = build_url(array('page' => 'newsletter'), get_module_zone('newsletter'));
        $message = do_lang('NEWSLETTER_SIGNUP_TEXT', comcode_escape($url), comcode_escape($password), array($forename, $surname, $email, get_site_name(), $newsletter_url->evaluate()));

        require_code('mail');
        mail_wrap(do_lang('NEWSLETTER_SIGNUP'), $message, array($email), $GLOBALS['FORUM_DRIVER']->get_username(get_member(), true));
    }

    /**
     * The UI for having confirmed an e-mail address onto the newsletter.
     *
     * @return Tempcode The UI
     */
    public function newsletter_confirm_joining()
    {
        $code_confirm = get_param_integer('confirm');
        $email = trim(get_param_string('email'));
        $correct_confirm = $GLOBALS['SITE_DB']->query_select_value_if_there('newsletter_subscribers', 'code_confirm', array('email' => $email));
        if ($correct_confirm === null) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        if ($correct_confirm == $code_confirm) {
            $GLOBALS['SITE_DB']->query_update('newsletter_subscribers', array('code_confirm' => 0), array('email' => $email), '', 1);
            return inform_screen($this->title, do_lang_tempcode('NEWSLETTER_CONFIRMED'));
        }

        return warn_screen($this->title, do_lang_tempcode(($correct_confirm == 0) ? 'ALREADY_CONFIRMED' : 'INCORRECT_CONFIRMATION'));
    }
}
