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

/**
 * Composr API helper class.
 */
class CMSForumWrite
{
    /**
     * Mark a forum as read.
     *
     * @param  AUTO_LINK $forum_id Forum ID
     */
    public function mark_forum_as_read($forum_id)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            warn_exit(do_lang_tempcode('permissions:ACCESS_DENIED__NOT_AS_GUEST'));
        }

        require_code('config2');

        require_code('cns_forums_action2');
        $_max_forum_detail = get_option('max_forum_detail');
        set_option('max_forum_detail', '10000');
        cns_ping_forum_read_all($forum_id);
        set_option('max_forum_detail', $_max_forum_detail);
    }
}
