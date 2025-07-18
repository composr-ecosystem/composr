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
 * @package    staff_messaging
 */

/**
 * Block class.
 */
class Block_main_contact_simple
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array('param', 'title', 'private', 'email_optional', 'body_prefix', 'body_suffix', 'subject_prefix', 'subject_suffix', 'redirect', 'guid');
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        require_lang('messaging');
        require_code('feedback');

        $to = array_key_exists('param', $map) ? $map['param'] : get_option('staff_address');

        $body_prefix = array_key_exists('body_prefix', $map) ? $map['body_prefix'] : '';
        $body_suffix = array_key_exists('body_suffix', $map) ? $map['body_suffix'] : '';
        $subject_prefix = array_key_exists('subject_prefix', $map) ? $map['subject_prefix'] : '';
        $subject_suffix = array_key_exists('subject_suffix', $map) ? $map['subject_suffix'] : '';

        $block_id = md5(serialize($map));

        $post = post_param_string('post', '');
        if ((post_param_integer('_comment_form_post', 0) == 1) && (post_param_string('_block_id', '') == $block_id) && ($post != '')) {
            if (addon_installed('captcha')) {
                if (get_option('captcha_on_feedback') == '1') {
                    require_code('captcha');
                    enforce_captcha();
                }
            }

            $message = new Tempcode();/*Used to be written out here*/

            require_code('mail');

            $email_from = trim(post_param_string('email', $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member())));
            $from_name = substr(trim(post_param_string('poster_name_if_guest', post_param_string('name', $GLOBALS['FORUM_DRIVER']->get_username(get_member(), true)))), 0, 80);

            if ($email_from != '') {
                require_code('type_sanitisation');
                if (!is_email_address($email_from)) {
                    return paragraph(do_lang_tempcode('INVALID_EMAIL_ADDRESS'), '', 'red_alert');
                }
            }

            $title = post_param_string('title');

            mail_wrap($subject_prefix . $title . $subject_suffix, $body_prefix . $post . $body_suffix, array($to), null, $email_from, $from_name, 3, null, false, get_member());

            if ($email_from != '' && get_option('message_received_emails') == '1') {
                mail_wrap(do_lang('YOUR_MESSAGE_WAS_SENT_SUBJECT', $title), do_lang('YOUR_MESSAGE_WAS_SENT_BODY', $post), array($email_from), empty($from_name) ? null : $from_name, '', '', 3, null, false, get_member());
            }

            attach_message(do_lang_tempcode('MESSAGE_SENT'), 'inform');

            $redirect = array_key_exists('redirect', $map) ? $map['redirect'] : '';
            if ($redirect != '') {
                $redirect = page_link_to_url($redirect);
                require_code('site2');
                assign_refresh($redirect, 0.0);
            }
        } else {
            $message = new Tempcode();
        }

        $box_title = array_key_exists('title', $map) ? $map['title'] : do_lang('CONTACT_US');
        $private = (array_key_exists('private', $map)) && ($map['private'] == '1');

        $em = $GLOBALS['FORUM_DRIVER']->get_emoticon_chooser();

        require_javascript('editing');
        require_javascript('checking');

        $comment_url = get_self_url();
        $email_optional = array_key_exists('email_optional', $map) ? (intval($map['email_optional']) == 1) : true;

        if (addon_installed('captcha')) {
            require_code('captcha');
            $use_captcha = ((get_option('captcha_on_feedback') == '1') && (use_captcha()));
            if ($use_captcha) {
                generate_captcha();
            }
        } else {
            $use_captcha = false;
        }

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('_block_id', $block_id));

        $guid = isset($map['guid']) ? $map['guid'] : 'd35227903b5f786331f6532bce1765e4';

        $comment_details = do_template('COMMENTS_POSTING_FORM', array(
            '_GUID' => $guid,
            'JOIN_BITS' => '',
            'FIRST_POST_URL' => '',
            'FIRST_POST' => '',
            'USE_CAPTCHA' => $use_captcha,
            'EMAIL_OPTIONAL' => $email_optional,
            'POST_WARNING' => '',
            'COMMENT_TEXT' => '',
            'GET_EMAIL' => !$private,
            'GET_TITLE' => !$private,
            'EM' => $em,
            'DISPLAY' => 'block',
            'TITLE' => $box_title,
            'SUBMIT_NAME' => do_lang_tempcode('SEND'),
            'COMMENT_URL' => $comment_url,
            'HIDDEN' => $hidden,
        ));

        $out = do_template('BLOCK_MAIN_CONTACT_SIMPLE', array(
            '_GUID' => $guid,
            'EMAIL_OPTIONAL' => true,
            'COMMENT_DETAILS' => $comment_details,
            'MESSAGE' => $message,
        ));

        return $out;
    }
}
