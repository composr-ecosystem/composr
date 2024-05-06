<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_endpoint_cms_homesite_forum_topics
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Info about the hook
     */
    public function info(?string $type, ?string $id) : array
    {
        return [
            'authorization' => ($type !== 'view') ? ['super_admin', 'maintenance_password'] : false,
        ];
    }

    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        if (!addon_installed('cms_homesite')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite')));
        }
        if (!addon_installed('downloads')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('downloads')));
        }
        if (!addon_installed('news')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('news')));
        }

        require_code('cms_homesite');

        switch ($type) {
            case 'add':
                $forum_id = post_param_string('forum_id');
                $topic_title = post_param_string('topic_title');
                $post = post_param_string('post');

                require_code('cns_topics_action');
                require_code('cns_posts_action');
                $topic_id = cns_make_topic(intval($forum_id), '', '', 1, 1, 0, 0, null, null, false);
                $post_id = cns_make_post(intval($topic_id), $topic_title, $post, 0, true, 1, 0, null, null, null, get_member(), null, null, null, false);

                return ['id' => $post_id];

            default:
                return []; // GET not implemented
        }
    }
}
