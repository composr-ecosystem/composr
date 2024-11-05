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
class Hook_endpoint_cms_homesite_forum_posts
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return ?array Info about the hook (null: endpoint is disabled)
     */
    public function info(?string $type, ?string $id) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }
        if (!addon_installed('downloads')) {
            return null;
        }
        if (!addon_installed('news')) {
            return null;
        }

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
        require_code('cms_homesite');

        switch ($type) {
            case 'add':
                $replying_to_post = post_param_integer('replying_to_post');
                $post_important = post_param_integer('post_important');
                $post_reply_title = post_param_string('post_reply_title');
                $post_reply_message = post_param_string('post_reply_message');

                $topic_id = $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_topic_id', ['id' => $replying_to_post]);

                require_code('cns_posts_action');
                $post_id = cns_make_post($topic_id, $post_reply_title, $post_reply_message, 0, false, 1, $post_important, null, null, null, get_member(), null, null, null, false, true, null, false, '', null, false, false, false, false, $replying_to_post);

                return ['id' => $post_id];

            default:
                return []; // GET not implemented
        }
    }
}
