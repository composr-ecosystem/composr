<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
 */

/**
 * Hook class.
 */
class Hook_endpoint_cms_homesite_tracker_posts
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
        if (!addon_installed('cms_homesite_tracker')) {
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
                $data = [];

                require_code('mantis');
                $tracker_id = post_param_integer('tracker_id');
                $tracker_comment_message = post_param_string('tracker_comment_message');

                // If these parameters were provided, then we are also updating the issue.
                $version_dotted = post_param_string('version_dotted', null);
                $tracker_severity = post_param_integer('tracker_severity', null);
                $tracker_category = post_param_integer('tracker_category', null);
                $tracker_project = post_param_integer('tracker_project', null);
                if (($version_dotted !== null) && ($tracker_severity !== null) && ($tracker_category !== null) && ($tracker_project !== null)) {
                    update_tracker_issue($tracker_id, $version_dotted, $tracker_severity, $tracker_category, $tracker_project);
                    $data['issue_updated'] = true;
                }

                $data['id'] = create_tracker_post($tracker_id, $tracker_comment_message);
                return $data;

            default:
                return []; // GET not implemented
        }
    }
}
