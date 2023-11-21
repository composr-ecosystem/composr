<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    better_mail
 */

/**
 * Attempt to send an e-mail to the specified recipient. The mail will be forwarding to the CC address specified in the options (if there is one, and if not specified not to cc).
 * The mail will be sent in dual HTML/text format, where the text is the unconverted Comcode source: if a member does not read HTML mail, they may wish to fallback to reading that.
 *
 * @param  string $subject_line The subject of the mail in plain text
 * @param  LONG_TEXT $message_raw The message, as Comcode
 * @param  ?array $to_email The destination (recipient) e-mail addresses [array of strings] (null: site staff address)
 * @param  ?mixed $to_name The recipient name. Array or string. (null: site name)
 * @param  EMAIL $from_email The from address (blank: site staff address)
 * @param  string $from_name The from name (blank: site name)
 * @param  integer $priority The message priority (1=urgent, 3=normal, 5=low)
 * @range  1 5
 * @param  ?array $attachments A list of attachments (each attachment being a map, absolute path=>filename) (null: none)
 * @param  boolean $no_cc Whether to NOT CC to the CC address
 * @param  ?MEMBER $as Convert Comcode->tempcode as this member (a privilege thing: we don't want people being able to use admin rights by default!) (null: guest)
 * @param  boolean $as_admin Replace above with arbitrary admin
 * @param  boolean $in_html HTML-only
 * @param  boolean $coming_out_of_queue Whether to bypass queueing, because this code is running as a part of the queue management tools
 * @param  ID_TEXT $mail_template The template used to show the email
 * @param  ?boolean $bypass_queue Whether to bypass queueing (null: auto-decide)
 * @param  ?array $extra_cc_addresses Extra CC addresses to use (null: none)
 * @param  ?array $extra_bcc_addresses Extra BCC addresses to use (null: none)
 * @param  ?TIME $require_recipient_valid_since Implement the Require-Recipient-Valid-Since header (null: no restriction)
 * @return boolean Success status
 */
function mail_wrap($subject_line, $message_raw, $to_email = null, $to_name = null, $from_email = '', $from_name = '', $priority = 3, $attachments = null, $no_cc = false, $as = null, $as_admin = false, $in_html = false, $coming_out_of_queue = false, $mail_template = 'MAIL', $bypass_queue = null, $extra_cc_addresses = null, $extra_bcc_addresses = null, $require_recipient_valid_since = null)
{
    if (get_option('smtp_sockets_use') == '0') {
        return non_overridden__mail_wrap($subject_line, $message_raw, $to_email, $to_name, $from_email, $from_name, $priority, $attachments, $no_cc, $as, $as_admin, $in_html, $coming_out_of_queue, $mail_template, $bypass_queue, $extra_cc_addresses, $extra_bcc_addresses, $require_recipient_valid_since);
    }

    if (running_script('stress_test_loader')) {
        require_code('files2');
        clean_temporary_mail_attachments($attachments);

        return false;
    }

    global $SITE_INFO;

    if (!empty($SITE_INFO['redirect_email_output'])) {
        $to_email = array($SITE_INFO['redirect_email_output']);
    }

    if (@$SITE_INFO['no_email_output'] === '1') {
        require_code('files2');
        clean_temporary_mail_attachments($attachments);

        return false;
    }

    if (method_exists($GLOBALS['FORUM_DRIVER'], 'get_member_from_email_address')) {
        if ($priority != 1 && $to_email !== null) {
            foreach ($to_email as $key => $email) {
                if ($GLOBALS['FORUM_DRIVER']->is_banned($GLOBALS['FORUM_DRIVER']->get_member_from_email_address($email))) {
                    unset($to_email[$key]);
                }

                if (count($to_email) == 0) {
                    require_code('files2');
                    clean_temporary_mail_attachments($attachments);

                    return true;
                }
            }
        }
    }

    if (is_null($bypass_queue)) {
        $bypass_queue = (($priority < 3) || (strpos(serialize($attachments), 'tmpfile') !== false));
    }

    global $EMAIL_ATTACHMENTS;
    $EMAIL_ATTACHMENTS = array();

    require_code('site');
    require_code('mime_types');
    require_code('type_sanitisation');

    if (is_null($as)) {
        $as = $GLOBALS['FORUM_DRIVER']->get_guest_id();
    }

    if (empty($attachments)) {
        $attachments = null;
    }
    if (is_null($extra_cc_addresses)) {
        $extra_cc_addresses = array();
    }
    if (is_null($extra_bcc_addresses)) {
        $extra_bcc_addresses = array();
    }

    if (!$coming_out_of_queue) {
        if ((mt_rand(0, 100) == 1) && (!$GLOBALS['SITE_DB']->table_is_locked('logged_mail_messages'))) {
            $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'logged_mail_messages WHERE m_date_and_time<' . strval(time() - 60 * 60 * 24 * intval(get_option('email_log_days'))) . ' AND m_queued=0', 500/*to reduce lock times*/); // Log it all for 2 weeks, then delete
        }

        $through_queue = (!$bypass_queue) && (((cron_installed()) && (get_option('mail_queue') === '1'))) || (get_option('mail_queue_debug') === '1');
        if ((!empty($attachments)) && (get_option('mail_queue_debug') === '0')) {
            foreach (array_keys($attachments) as $path) {
                if ((substr($path, 0, strlen(get_custom_file_base() . '/')) != get_custom_file_base() . '/') && (substr($path, 0, strlen(get_file_base() . '/')) != get_file_base() . '/')) {
                    $through_queue = false;
                }
            }
        }

        if (!$in_html) {
            inject_web_resources_context_to_comcode($message_raw);
        }

        $GLOBALS['SITE_DB']->query_insert('logged_mail_messages', array(
            'm_subject' => cms_mb_substr($subject_line, 0, 255),
            'm_message' => $message_raw,
            'm_to_email' => serialize($to_email),
            'm_to_name' => serialize($to_name),
            'm_extra_cc_addresses' => serialize($extra_cc_addresses),
            'm_extra_bcc_addresses' => serialize($extra_bcc_addresses),
            'm_join_time' => $require_recipient_valid_since,
            'm_from_email' => $from_email,
            'm_from_name' => $from_name,
            'm_priority' => $priority,
            'm_attachments' => serialize($attachments),
            'm_no_cc' => $no_cc ? 1 : 0,
            'm_as' => $as,
            'm_as_admin' => $as_admin ? 1 : 0,
            'm_in_html' => $in_html ? 1 : 0,
            'm_date_and_time' => time(),
            'm_member_id' => get_member(),
            'm_url' => get_self_url(true),
            'm_queued' => $through_queue ? 1 : 0,
            'm_template' => $mail_template,
        ), false, !$through_queue); // No errors if we don't NEED this to work

        if ($through_queue) {
            return true;
        }
    }

    global $SENDING_MAIL;
    if ($SENDING_MAIL) {
        require_code('files2');
        clean_temporary_mail_attachments($attachments);

        return false;
    }
    $SENDING_MAIL = true;

    // To and from, and language
    $staff_address = get_option('staff_address');
    if (!is_email_address($staff_address)) { // Required for security
        $staff_address = '';
    }
    if (is_null($to_email)) {
        $to_email = array($staff_address);
    }
    $to_email_new = array();
    foreach ($to_email as $test_address) {
        if ($test_address != '') {
            $to_email_new[] = $test_address;
        }
    }
    $to_email = $to_email_new;
    if ($to_email == array()) {
        $SENDING_MAIL = false;

        require_code('files2');
        clean_temporary_mail_attachments($attachments);

        return true;
    }

    $member_id = null;
    if ((method_exists($GLOBALS['FORUM_DRIVER'], 'get_member_from_email_address')) && (count($to_email) == 1)) {
        $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($to_email[0]);
    }
    if (($member_id === null) && (count($to_email) > 1)) {
        $member_id = $GLOBALS['FORUM_DRIVER']->get_guest_id();
    }

    if ($member_id === null) {
        $lang = user_lang();
    } else {
        $lang = get_lang($member_id);
    }

    if (is_null($to_name)) {
        if ($to_email[0] == $staff_address) {
            $to_name = get_site_name();
        } else {
            $to_name = '';
        }
    }
    if ($from_email == '') {
        $from_email = get_option('staff_address');
    }
    if (!is_email_address($from_email)) { // Required for security
        $from_email = '';
    }
    if ($from_name == '') {
        $from_name = get_site_name();
    }
    $from_email = str_replace(array("\r", "\n"), array('', ''), $from_email);
    $from_name = str_replace(array("\r", "\n"), array('', ''), $from_name);

    if (!$coming_out_of_queue) {
        cms_profile_start_for('mail_wrap');
    }

    $_theme = get_value('mail_theme');
    if ($_theme !== null) {
        $theme = $_theme;
    } else {
        if ($member_id === null) {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_guest_id(); // As there's no browser-settings involved (as for user_lang()) we just want to set for what a guest would see
        }

        $theme = method_exists($GLOBALS['FORUM_DRIVER'], 'get_theme') ? $GLOBALS['FORUM_DRIVER']->get_theme(null, $member_id) : 'default';
        if ($theme == 'default' || $theme == 'admin') { // Sucks, probably due to sending from Admin Zone...
            $theme = $GLOBALS['FORUM_DRIVER']->get_theme('', $member_id); // ... So get theme of welcome zone
        }
    }

    // Our subject
    $subject = do_template('MAIL_SUBJECT', array('_GUID' => 'be0817ac7a954ade56ff1a3426218de2', 'SUBJECT_LINE' => $subject_line), $lang, false, null, '.txt', 'text', $theme);
    $tightened_subject = $subject->evaluate($lang); // Note that this is slightly against spec, because characters aren't forced to be printable us-ascii. But it's better we allow this (which works in practice) than risk incompatibility via charset-base64 encoding.
    $tightened_subject = str_replace(array("\r", "\n"), array('', ''), $tightened_subject);

    // Evaluate message. Needs doing early so we know if we have any headers

    // Misc settings
    $website_email = get_option('website_email');
    if (!is_email_address($website_email)) { // Required for security
        $website_email = '';
    }
    if ($website_email == '') {
        $website_email = $from_email;
    }
    $cc_address = $no_cc ? '' : get_option("cc_address");

    global $CID_IMG_ATTACHMENT;
    $CID_IMG_ATTACHMENT = array();

    // Evaluate message. Needs doing early so we know if we have any headers
    if (!$in_html) {
        $cache_sig = serialize(array(
            $lang,
            $mail_template,
            $subject,
            $theme,
            crc32($message_raw),
        ));

        static $html_content_cache = array();
        if (isset($html_content_cache[$cache_sig])) {
            list($html_evaluated, $message_plain, $EMAIL_ATTACHMENTS) = $html_content_cache[$cache_sig];
        } else {
            require_code('media_renderer');
            push_media_mode(peek_media_mode() | MEDIA_LOWFI);

            $GLOBALS['NO_LINK_TITLES'] = true;
            global $LAX_COMCODE;
            $temp = $LAX_COMCODE;
            $LAX_COMCODE = true;
            $html_content = comcode_to_tempcode($message_raw, $as, $as_admin);
            $LAX_COMCODE = $temp;
            $GLOBALS['NO_LINK_TITLES'] = false;

            $_html_content = $html_content->evaluate($lang);
            $_html_content = preg_replace('#(keep|for)_session=\w*#', 'filtered=1', $_html_content);
            if (strpos($_html_content, '<html') !== false) {
                $message_html = make_string_tempcode($_html_content);
            } else {
                $message_html = do_template($mail_template, array(
                    '_GUID' => 'b23069c20202aa59b7450ebf8d49cde1',
                    'CSS' => '{CSS}',
                    'LOGOURL' => get_logo_url(''),
                    'LANG' => $lang,
                    'TITLE' => $subject,
                    'CONTENT' => $_html_content,
                ), $lang, false, 'MAIL', '.tpl', 'templates', $theme);
            }
            require_css('email');
            $css = css_tempcode(true, false, $message_html->evaluate($lang), $theme);
            $_css = $css->evaluate($lang);
            if (!GOOGLE_APPENGINE) {
                if (get_option('allow_ext_images') != '1') {
                    $_css = preg_replace_callback('#url\(["\']?(https?://[^"]*)["\']?\)#U', '_mail_css_rep_callback', $_css);
                }
            }
            $html_evaluated = $message_html->evaluate($lang);
            $html_evaluated = str_replace('{CSS}', $_css, $html_evaluated);

            // Cleanup the Comcode a bit
            $message_plain = comcode_to_clean_text($message_raw);
            $message_plain = static_evaluate_tempcode(do_template($mail_template, array(
                '_GUID' => 'a23069c20202aa59b7450ebf8d49cde1',
                'CSS' => '{CSS}',
                'LOGOURL' => get_logo_url(''),
                'LANG' => $lang,
                'TITLE' => $subject,
                'CONTENT' => $message_plain,
            ), $lang, false, 'MAIL', '.txt', 'text', $theme));

            $html_content_cache[$cache_sig] = array($html_evaluated, $message_plain, $EMAIL_ATTACHMENTS);

            pop_media_mode();
        }
        $attachments = array_merge(is_null($attachments) ? array() : $attachments, $EMAIL_ATTACHMENTS);
    } else {
        $html_evaluated = $message_raw;
    }

    // Character set
    $regexp = '#^[\x' . dechex(32) . '-\x' . dechex(126) . ']*$#';
    $charset = ((preg_match($regexp, $html_evaluated) == 0) ? do_lang('charset', null, null, null, $lang) : 'us-ascii');

    // CID attachments
    if (get_option('allow_ext_images') != '1') {
        $cid_before = array_keys($CID_IMG_ATTACHMENT);
        $html_evaluated = preg_replace_callback('#<img\s([^>]*)src="(https?://[^"]*)"#U', '_mail_img_rep_callback', $html_evaluated);
        $cid_just_html = array_diff(array_keys($CID_IMG_ATTACHMENT), $cid_before);
        $matches = array();
        foreach (array('#<([^"<>]*\s)style="([^"]*)"#', '#<style( [^<>]*)?' . '>(.*)</style>#Us') as $over) {
            $num_matches = preg_match_all($over, $html_evaluated, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $altered_inner = preg_replace_callback('#url\(["\']?(https?://[^"]*)["\']?\)#U', '_mail_css_rep_callback', $matches[2][$i]);
                if ($matches[2][$i] != $altered_inner) {
                    $altered_outer = str_replace($matches[2][$i], $altered_inner, $matches[0][$i]);
                    $html_evaluated = str_replace($matches[0][$i], $altered_outer, $html_evaluated);
                }
            }
        }

        // This is a hack to stop the images used by CSS showing as attachments in some mail clients
        $cid_just_css = array_diff(array_keys($CID_IMG_ATTACHMENT), $cid_just_html);
        foreach ($cid_just_css as $cid) {
            $html_evaluated .= '<img width="0" height="0" src="cid:' . $cid . '" />';
        }
    }
    $cid_attachments = array();
    foreach ($CID_IMG_ATTACHMENT as $id => $img) {
        $file_path_stub = convert_url_to_path($img);
        $mime_type = get_mime_type(get_file_extension($img), has_privilege($as, 'comcode_dangerous'));
        $filename = basename($img);

        if (!is_null($file_path_stub)) {
            $cid_attachment = array(
                'mime' => $mime_type,
                'filename' => $filename,
                'path' => $file_path_stub,
                'temp' => false,
                'cid' => $id,
            );
        } else {
            $myfile = mixed();
            $matches = array();
            if ((preg_match('#^' . preg_quote(find_script('attachment'), '#') . '\?id=(\d+)&amp;thumb=(0|1)#', $img, $matches) != 0) && (strpos($img, 'forum_db=1') === false)) {
                $rows = $GLOBALS['SITE_DB']->query_select('attachments', array('*'), array('id' => intval($matches[1])), 'ORDER BY a_add_time DESC');
                require_code('attachments');
                if ((array_key_exists(0, $rows)) && (has_attachment_access($as, intval($matches[1])))) {
                    $myrow = $rows[0];

                    if ($matches[2] == '1') {
                        $full = $myrow['a_thumb_url'];
                    } else {
                        $full = $myrow['a_url'];
                    }

                    if (url_is_local($full)) {
                        $_full = get_custom_file_base() . '/' . rawurldecode($full);
                        if (file_exists($_full)) {
                            $filename = $myrow['a_original_filename'];
                            require_code('mime_types');
                            $myfile = $_full;
                            $mime_type = get_mime_type(get_file_extension($filename), has_privilege($as, 'comcode_dangerous'));
                        } else {
                            continue;
                        }
                    }
                }
            }
            if ($myfile === null) {
                $myfile = cms_tempnam();
                http_download_file($img, null, false, false, 'Composr', null, null, null, null, null, $myfile);
                if (filesize($myfile) == 0) {
                    continue;
                }
                if (!is_null($GLOBALS['HTTP_DOWNLOAD_MIME_TYPE'])) {
                    $mime_type = $GLOBALS['HTTP_DOWNLOAD_MIME_TYPE'];
                }
                if (!is_null($GLOBALS['HTTP_FILENAME'])) {
                    $filename = $GLOBALS['HTTP_FILENAME'];
                }
            }

            $cid_attachment = array(
                'mime' => $mime_type,
                'filename' => $filename,
                'path' => $myfile,
                'temp' => true,
                'cid' => $id,
            );
        }

        $cid_attachments[] = $cid_attachment;
    }

    // Attachments
    $real_attachments = array();
    $attachments = array_merge(is_null($attachments) ? array() : $attachments, $EMAIL_ATTACHMENTS);
    if (!is_null($attachments)) {
        foreach ($attachments as $path => $filename) {
            $mime_type = get_mime_type(get_file_extension($filename), has_privilege($as, 'comcode_dangerous'));

            if ((strpos($path, '://') === false) && (substr($path, 0, 5) != 'gs://')) {
                if (!is_file($path)) {
                    continue;
                }

                require_code('files2');
                $real_attachment = array(
                    'mime' => $mime_type,
                    'filename' => $filename,
                    'path' => $path,
                    'temp' => is_temp_file($path),
                );
            } else {
                $myfile = cms_tempnam();
                http_download_file($path, null, false, false, 'Composr', null, null, null, null, null, $myfile);
                if (filesize($myfile) == 0) {
                    continue;
                }
                if (!is_null($GLOBALS['HTTP_DOWNLOAD_MIME_TYPE'])) {
                    $mime_type = $GLOBALS['HTTP_DOWNLOAD_MIME_TYPE'];
                }
                if (!is_null($GLOBALS['HTTP_FILENAME'])) {
                    $filename = $GLOBALS['HTTP_FILENAME'];
                }

                $real_attachment = array(
                    'mime' => $mime_type,
                    'filename' => $filename,
                    'path' => $myfile,
                    'temp' => true,
                );
            }

            $real_attachments[] = $real_attachment;
        }
    }

    // ==========================
    // Interface with SwiftMailer
    // ==========================

    require_code('swift_mailer/lib/swift_required');

    // Read in SMTP settings
    $host = get_option('smtp_sockets_host');
    $port = intval(get_option('smtp_sockets_port'));
    $username = get_option('smtp_sockets_username');
    $password = get_option('smtp_sockets_password');
    $smtp_from_address = get_option('smtp_from_address');
    if ($smtp_from_address != '') {
        $from_email = $smtp_from_address;
    }

    // Create the Transport
    $transport = (new Swift_SmtpTransport($host, $port))
        ->setUsername($username)
        ->setPassword($password);
    if (($port == 419) || ($port == 465) || ($port == 587)) {
        $disabled_ssl_verify = ((function_exists('get_value')) && (get_value('disable_ssl_for__' . $host) === '1'));

        $transport->setEncryption('tls');

        $crt_path = get_file_base() . '/data/curl-ca-bundle.crt';
        $ssl_options = array(
            'verify_peer' => !$disabled_ssl_verify,
            'verify_peer_name' => !$disabled_ssl_verify,
            'cafile' => $crt_path,
            'SNI_enabled' => true,
        );
        $transport->setStreamOptions(array('ssl' => $ssl_options));


        // Try to connect the transport 3 times, in case there are any stalls across the round-trips / TLS auth
        for ($i = 0; $i < 3; $i++) {
            try {
                $transport->start();
                break;
            }
            catch (Swift_SwiftException $e) {
            }
        }
    }

    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

    // Create a message
    $to_array = array();
    if ($to_name === '') {
        foreach ($to_email as $_to_email) {
            $to_array[] = $_to_email;
        }
    } else {
        foreach ($to_email as $i => $_to_email) {
            $to_array[$_to_email] = is_array($to_name) ? $to_name[$i] : $to_name;
        }
    }
    if (
        (get_option('use_true_from') == '1') ||
        ((get_option('use_true_from') == '0') && (preg_replace('#^.*@#', '', $from_email) == preg_replace('#^.*@#', '', get_option('website_email')))) ||
        ((get_option('use_true_from') == '0') && (preg_replace('#^.*@#', '', $from_email) == preg_replace('#^.*@#', '', get_option('staff_address')))) ||
        ((addon_installed('tickets')) && (get_option('use_true_from') == '0') && (preg_replace('#^.*@#', '', $from_email) == preg_replace('#^.*@#', '', get_option('ticket_email_from')))) ||
        ((addon_installed('tickets')) && (get_option('use_true_from') == '0') && (preg_replace('#^.*@#', '', $from_email) == get_domain()))
    ) {
        $website_email = $from_email;
    }

    $message = new Swift_Message($subject);
    $message
        ->setFrom(array($website_email => $from_name))
        ->setReplyTo(array($from_email => $from_name))
        ->setTo($to_array)
        ->setDate(time())
        ->setPriority($priority)
        ->setCharset($charset)
        ->setBody($html_evaluated, 'text/html', $charset);
    if (!$in_html) {
        $message->addPart($message_plain, 'text/plain', $charset);
    }

    if ($cc_address != '') {
        if (get_option('bcc') == '0') {
            $extra_cc_addresses[] = $cc_address;
        } else {
            $extra_bcc_addresses[] = $cc_address;
        }
    }
    $message->setCc(array_unique($extra_cc_addresses));
    $message->setBcc(array_unique($extra_bcc_addresses));

    if ((count($to_email) == 1) && (!is_null($require_recipient_valid_since))) {
        $headers = $message->getHeaders();
        $_require_recipient_valid_since = date('r', $require_recipient_valid_since);
        $headers->addTextHeader('Require-Recipient-Valid-Since', $to_email[0] . '; ' . $_require_recipient_valid_since);
    }

    // Attachments
    foreach ($real_attachments as $r) {
        $attachment = Swift_Attachment::fromPath($r['path'], $r['mime'])->setFilename($r['filename'])->setDisposition('attachment');
        $message->attach($attachment);
    }
    foreach ($cid_attachments as $r) {
        $attachment = Swift_Attachment::fromPath($r['path'], $r['mime'])->setFilename($r['filename'])->setDisposition('attachment')->setId($r['cid']);
        $message->attach($attachment);
    }

    // Send the message, and error collection
    $error = '';
    $worked = true;
    $failures = array();
    try {
        $result = $mailer->send($message, $failures);
    } catch (Exception $e) {
        $error = $e->getMessage();
        $worked = false;
    }
    if (($error == '') && (!$result)) {
        if (count($failures) == 0) {
            $error = 'Unknown error';
        } else {
            $error = 'Rejected addresses: ' . implode(', ', $failures);
        }
    }

    // Attachment cleanup
    foreach ($real_attachments as $r) {
        if ($r['temp']) {
            @unlink($r['path']);
        }
    }
    foreach ($cid_attachments as $r) {
        if ($r['temp']) {
            @unlink($r['path']);
        }
    }

    if (!$coming_out_of_queue) {
        cms_profile_end_for('mail_wrap', $subject_line);
    }

    if (!$worked) {
        $SENDING_MAIL = false;
        require_code('site');
        attach_message(!is_null($error) ? make_string_tempcode($error) : do_lang_tempcode('MAIL_FAIL', escape_html(get_option('staff_address'))), 'warn');
        return false;
    }

    $SENDING_MAIL = false;
    return true;
}
