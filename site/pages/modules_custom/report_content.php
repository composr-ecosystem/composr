<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    reported_content
 */

/**
 * Module page class.
 */
class Module_report_content
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
        $info['version'] = 3;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('reported_content');
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
            $GLOBALS['SITE_DB']->create_table('reported_content', array(
                'r_session_id' => '*ID_TEXT',
                'r_content_type' => '*ID_TEXT',
                'r_content_id' => '*ID_TEXT',
                'r_counts' => 'BINARY', // If the content is marked unvalidated, r_counts is set to 0 for each row for it, so if it's revalidated the counts apply elsewhere
            ));
            $GLOBALS['SITE_DB']->create_index('reported_content', 'reported_already', array('r_content_type', 'r_content_id'));
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 3)) {
            $GLOBALS['SITE_DB']->alter_table_field('reported_content', 'r_session_id', 'ID_TEXT');
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
        return array();
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

        $type = get_param_string('type', 'browse');

        require_lang('report_content');

        attach_to_screen_header('<meta name="robots" content="noindex" />'); // XHTMLXHTML

        if ($type == 'browse') {
            $this->title = get_screen_title('REPORT_CONTENT');
        }

        if ($type == 'actual') {
            $this->title = get_screen_title('REPORT_CONTENT');
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
        require_lang('cns');

        // Decide what we're doing
        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            return $this->form();
        }
        if ($type == 'actual') {
            return $this->actualiser();
        }

        return new Tempcode();
    }

    public function form()
    {
        require_code('form_templates');

        require_code('cns_forums');

        $forum_id = $GLOBALS['FORUM_DRIVER']->forum_id_from_name(get_option('reported_posts_forum'));
        if (is_null($forum_id)) {
            warn_exit(do_lang_tempcode('cns:NO_REPORTED_POST_FORUM'));
        }

        $url = get_param_string('url', false, true);
        $content_type = get_param_string('content_type'); // Equates to a content_meta_aware hook
        $content_id = get_param_string('content_id');

        require_code('content');

        if (!is_null($GLOBALS['SITE_DB']->query_select_value_if_there('reported_content', 'r_counts', array(
            'r_session_id' => get_session_id(),
            'r_content_type' => $content_type,
            'r_content_id' => $content_id,
        )))
        ) {
            warn_exit(do_lang_tempcode('ALREADY_REPORTED_CONTENT'));
        }

        list($content_title, $poster_id,) = content_get_details($content_type, $content_id);
        if ($content_title == '') {
            $content_title = $content_type . ' #' . $content_id;
        }

        // Show form with input field and CAPTCHA, like forum's report post...

        $poster = do_lang('UNKNOWN');
        if ((!is_null($poster_id)) && (!is_guest($poster_id))) {
            $poster = $GLOBALS['FORUM_DRIVER']->get_username($poster_id);
            if (!is_null($poster)) {
                $member = '{{' . $poster . '}}';
            }
        }

        $hidden_fields = build_keep_form_fields('', true);

        $text = paragraph(do_lang_tempcode('DESCRIPTION_REPORT_CONTENT', escape_html($content_title), escape_html(integer_format(intval(get_option('reported_times'))))));

        url_default_parameters__enable();

        $specialisation = new Tempcode();
        if (!is_guest()) {
            $options = array();
            if (cns_forum_allows_anonymous_posts($forum_id)) {
                $options[] = array(do_lang_tempcode('_MAKE_ANONYMOUS_POST'), 'anonymous', false, do_lang_tempcode('MAKE_ANONYMOUS_POST_DESCRIPTION'));
            }
            $specialisation = form_input_various_ticks($options, '');
        } else {
            $specialisation = new Tempcode();
        }
        if (addon_installed('captcha')) {
            require_code('captcha');
            if (use_captcha()) {
                $specialisation->attach(form_input_captcha());
                $text->attach(paragraph(do_lang_tempcode('FORM_TIME_SECURITY')));
            }
        }

        if (addon_installed('points')) {
            $login_url = build_url(array('page' => 'login', 'type' => 'browse', 'redirect' => get_self_url(true, true)), get_module_zone('login'));
            $_login_url = escape_html($login_url->evaluate());
            if ((is_guest()) && ((get_forum_type() != 'cns') || (has_actual_page_access(get_member(), 'join')))) {
                $text->attach(paragraph(do_lang_tempcode('NOT_LOGGED_IN_NO_CREDIT', $_login_url)));
            }
        }

        $post_url = build_url(array('page' => '_SELF', 'type' => 'actual'), '_SELF');

        $post = do_template('REPORTED_CONTENT_FCOMCODE', array('_GUID' => 'cb40aa1900eefcd24a0786b9d980fef6', 'URL' => $url, 'CONTENT_ID' => $content_id, 'MEMBER' => $member, 'CONTENT_TITLE' => $content_title, 'POSTER' => $poster));
        $posting_form = get_posting_form(do_lang('REPORT_CONTENT'), 'buttons__send', $post->evaluate(), $post_url, $hidden_fields, $specialisation, '', '', null, null, null, null, true, false, true);

        url_default_parameters__disable();

        return do_template('POSTING_SCREEN', array('_GUID' => '92a0a35a7c07edd0d3f8a960710de608', 'TITLE' => $this->title, 'JAVASCRIPT' => function_exists('captcha_ajax_check') ? captcha_ajax_check() : '', 'TEXT' => $text, 'POSTING_FORM' => $posting_form));
    }

    public function actualiser()
    {
        // Test CAPTCHA
        if (addon_installed('captcha')) {
            require_code('captcha');
            enforce_captcha();
        }

        require_code('content');

        $content_type = post_param_string('content_type'); // Equates to a content_meta_aware hook
        $content_id = post_param_string('content_id');

        if (!is_null($GLOBALS['SITE_DB']->query_select_value_if_there('reported_content', 'r_counts', array(
            'r_session_id' => get_session_id(),
            'r_content_type' => $content_type,
            'r_content_id' => $content_id,
        )))
        ) {
            warn_exit(do_lang_tempcode('ALREADY_REPORTED_CONTENT'));
        }
        list($content_title, , $cma_info, , $content_url) = content_get_details($content_type, $content_id);

        // Create reported post...
        $forum_id = $GLOBALS['FORUM_DRIVER']->forum_id_from_name(get_option('reported_posts_forum'));
        // See if post already reported...
        $post = post_param_string('post');
        $anonymous = post_param_integer('anonymous', 0);
        $topic_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_topics t LEFT JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts p ON p.id=t.t_cache_first_post_id', 't.id', array('p.p_title' => $content_title, 't.t_forum_id' => $forum_id));
        require_code('cns_topics_action');
        require_code('cns_topics_action2');
        require_code('cns_posts_action');
        require_code('cns_posts_action2');
        if (!is_null($topic_id)) {
            // Already a topic
            $new_topic = false;
        } else { // New topic
            $topic_id = cns_make_topic($forum_id, '', '', 1, 1, 0, 0, 0, null, null, false);
            $new_topic = true;
        }
        $topic_title = do_lang('REPORTED_CONTENT_TITLE', $content_title);
        $post_id = cns_make_post($topic_id, $content_title, $post, 0, $new_topic, 1, 0, null, null, null, null, null, null, null, false, true, $forum_id, true, $topic_title, 0, null, $anonymous == 1);

        decache('main_staff_checklist');

        // Add to reported_content table
        $GLOBALS['SITE_DB']->query_insert('reported_content', array(
            'r_session_id' => get_session_id(),
            'r_content_type' => $content_type,
            'r_content_id' => $content_id,
            'r_counts' => 1,
        ));

        // If hit threshold, mark down r_counts and unvalidate the content
        $count = $GLOBALS['SITE_DB']->query_select_value('reported_content', 'COUNT(*)', array(
            'r_content_type' => $content_type,
            'r_content_id' => $content_id,
            'r_counts' => 1,
        ));
        if ($count >= intval(get_option('reported_times'))) {
            // Mark as unvalidated
            if ((!is_null($cma_info['validated_field'])) && (strpos($cma_info['table'], '(') === false)) {
                $db = $GLOBALS[((substr($cma_info['table'], 0, 2) == 'f_') && (get_forum_type() == 'cns')) ? 'FORUM_DB' : 'SITE_DB'];
                $db->query_update($cma_info['table'], array($cma_info['validated_field'] => 0), get_content_where_for_str_id($content_id, $cma_info));
            }

            $GLOBALS['SITE_DB']->query_update('reported_content', array('r_counts' => 0), array(
                'r_content_type' => $content_type,
                'r_content_id' => $content_id,
            ));
        }

        // Done
        $_url = post_param_string('url', '', true);
        if ($_url != '') {
            $content_url = make_string_tempcode($_url);
        }
        return redirect_screen($this->title, $content_url, do_lang_tempcode('SUCCESS'));
    }
}


/*FUDGE...

Before this can be an official Composr feature new content_meta_aware hooks are needed. Currently for instance there's no 'post' one.
*/
