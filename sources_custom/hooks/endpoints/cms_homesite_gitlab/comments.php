<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_gitlab
 */

/**
 * Hook class.
 */
class Hook_endpoint_cms_homesite_gitlab_comments
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
        if (!addon_installed('cms_homesite_gitlab')) {
            return null;
        }
        if (!addon_installed('points')) {
            return null;
        }

        return [
            'authorization' => ['gitlab_webhook'],
            'log_stats_event' => 'cms_homesite_gitlab/comments',
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
        // The JSON payload was coerced by the main endpoints script
        $data = json_decode($_POST['data'], true);

        // Integrity check
        if (!isset($data['object_kind']) || !isset($data['object_attributes']['action'])) {
            return ['success' => false, 'error_details' => 'Invalid webhook data.'];
        }

        // We only award points for new comments
        if (!in_array($data['object_attributes']['action'], ['create'])) {
            return ['success' => false, 'error_details' => 'Only actionable on creating comments.'];
        }

        require_code('cms_homesite_gitlab');

        $member_id = gitlab_webhook_payload_to_member($data);

        // Could not find any members? Exit out.
        if ($member_id === null) {
            return ['success' => false, 'error_details' => 'No member found matching the GitLab user responsible for the comment action.'];
        }

        $points = intval(get_option('gitlab_comment_points'));

        // Ignored if not awarding points for any issues
        if ($points < 0) {
            return ['success' => false, 'error_details' => 'Ignored; no points are to be awarded.'];
        }

        require_code('points2');
        require_code('templates');
        require_lang('cms_homesite_gitlab');

        $_reason = do_lang('UNKNOWN');
        if (isset($data['object_attributes']['noteable_type'])) {
            $data['object_attributes']['noteable_type'];
        }
        $reason = do_lang('POINTS_GITLAB_CREATED_COMMENT', comcode_escape(strval($data['object_attributes']['id'])), comcode_escape($_reason));

        points_credit_member($member_id, $reason, $points, 0, true, 0, 'gitlab', 'comment', strval($data['object_attributes']['id']));

        return ['success' => true];
    }
}
