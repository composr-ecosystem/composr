<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: CMS.**/

/**
 * Composr API helper class.
 */
class CMSAttachmentWrite
{
    /**
     * Handle upload of an attachment.
     *
     * @return array Details of the upload, with the 'result' key marking success status
     */
    public function handle_upload_attach()
    {
        $member_id = get_member();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_code('uploads');

        $_f = array_keys($_FILES);
        $filekey = array_shift($_f);
        $filekey_orig = $filekey;
        if (is_array($_FILES[$filekey]['name'])) {
            $filekey .= '1';
        }

        $urls = get_url('', $filekey, file_exists(get_custom_file_base() . '/uploads/avatars') ? 'uploads/avatars' : 'uploads/cns_avatars', 0, CMS_UPLOAD_IMAGE, false, '', '', false, true);
        if ($urls[0] == '') {
            warn_exit(do_lang_tempcode('IMPROPERLY_FILLED_IN_UPLOAD'));
        }

        if (url_is_local($urls[0])) {
            $filepath = get_file_base() . '/' . rawurldecode($urls[0]);
            $filesize = filesize($filepath);

            require_once(COMMON_CLASS_PATH_READ . '/user_read.php');
            $user_read_object = new CMSUserRead();
            if ($filesize > $user_read_object->get_posting_setting(get_member(), 'max_attachment_size')) {
                unlink($filepath);
                warn_exit(do_lang_tempcode('ERROR_UPLOADING_1'));
            }
        } else {
            $filesize = $_FILES[$filekey_orig]['size'];
        }

        $attachment_id = $GLOBALS['FORUM_DB']->query_insert('attachments', array(
            'a_member_id' => $member_id,
            'a_file_size' => $filesize,
            'a_url' => $urls[0],
            'a_thumb_url' => '',
            'a_original_filename' => basename($urls[2]),
            'a_num_downloads' => 0,
            'a_description' => '',
            'a_add_time' => time(),
        ), true);

        return array(
            'attachment_id' => $attachment_id,
            'filters_size' => $filesize,
        );
    }

    /**
     * Handle upload of an avatar.
     *
     * @return array Details of the upload, with the 'result' key marking success status
     */
    public function handle_upload_avatar()
    {
        $member_id = get_member();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        require_code('uploads');

        $_f = array_keys($_FILES);
        $filekey = array_shift($_f);
        $filekey_orig = $filekey;
        if (is_array($_FILES[$filekey]['name'])) {
            $filekey .= '1';
        }

        $urls = get_url('', $filekey, file_exists(get_custom_file_base() . '/uploads/avatars') ? 'uploads/avatars' : 'uploads/cns_avatars', 0, CMS_UPLOAD_IMAGE, false, '', '', false, true);
        if ($urls[0] == '') {
            warn_exit(do_lang_tempcode('IMPROPERLY_FILLED_IN_UPLOAD'));
        }

        if (url_is_local($urls[0])) {
            $filepath = get_file_base() . '/' . rawurldecode($urls[0]);
            $filesize = filesize($filepath);

            require_once(COMMON_CLASS_PATH_READ . '/user_read.php');
            $user_read_object = new CMSUserRead();
            if ($filesize > $user_read_object->get_posting_setting(get_member(), 'max_attachment_size')) {
                unlink($filepath);
                warn_exit(do_lang_tempcode('ERROR_UPLOADING_1'));
            }
        } else {
            $filesize = $_FILES[$filekey_orig]['size'];
        }

        require_code('cns_members_action');
        require_code('cns_members_action2');
        cns_member_choose_avatar($urls[0], $member_id);

        return array(
            'filters_size' => $filesize,
        );
    }

    /**
     * Remove an attachment.
     *
     * @param  AUTO_LINK $attachment_id Attachment ID
     * @param  ?AUTO_LINK $forum_id Forum ID (null: private topic)
     * @param  ?AUTO_LINK $post_id Post ID (null: no specific post)
     */
    public function remove_attachment($attachment_id, $forum_id, $post_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $type = 'cns_post';

        if (is_null($post_id)) {
            $_post_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('attachment_refs', 'r_referer_id', array('r_referer_type' => $type, 'a_id' => $attachment_id));
            if (!is_null($_post_id)) {
                $post_id = intval($_post_id);
            }
        }

        if (is_null($post_id)) {
            warn_exit('Cannot currently delete a standalone attachment, as no standardised permission mechanism for it');
        } else {
            if (!can_moderate_post($post_id)) {
                access_denied('I_ERROR');
            }
        }

        $_post_comcode = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_posts', 'p_post', array('id' => $post_id));
        if (is_null($_post_comcode)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }
        $post_comcode = get_translated_text($_post_comcode, $GLOBALS['FORUM_DB']);
        $post_comcode = preg_replace('#\n*\[attachment(_safe)?( [^\[\]]*)?\]' . strval($attachment_id) . '\[/attachment(_safe)?\]#U', '', $post_comcode);
        $GLOBALS['FORUM_DB']->query_update('f_posts', lang_remap_comcode('p_post', $_post_comcode, $post_comcode, $GLOBALS['FORUM_DB']), array('id' => $post_id), '', 1);

        require_code('attachments3');

        $_attachment_info = $GLOBALS['SITE_DB']->query_select('attachments', array('a_url', 'a_thumb_url', 'a_member_id'), array('id' => $attachment_id), '', 1);
        if (!array_key_exists(0, $_attachment_info)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', do_lang_tempcode('_ATTACHMENT')));
        }

        $ref_where = array('a_id' => $attachment_id, 'r_referer_type' => $type);
        if (!is_null($post_id)) {
            $ref_where['r_referer_id'] = strval($post_id);
        }
        $GLOBALS['FORUM_DB']->query_delete('attachment_refs', $ref_where);

        // Was that the last reference to this attachment? (if so -- delete attachment)
        $test = $GLOBALS['FORUM_DB']->query_select_value_if_there('attachment_refs', 'id', array('a_id' => $attachment_id));
        if (is_null($test)) {
            _delete_attachment($attachment_id, $GLOBALS['FORUM_DB']);
        }
    }
}
