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
class Hook_endpoint_cms_homesite_gitlab_issues
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
            'log_stats_event' => 'cms_homesite_gitlab/issues',
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

        require_code('cms_homesite_gitlab');

        switch($data['object_attributes']['action']) {
            case 'open':
                $member_id = gitlab_webhook_payload_to_member($data);

                // Could not find any members? Exit out.
                if ($member_id === null) {
                    return ['success' => false, 'error_details' => 'No member found matching the GitLab user responsible for the issue action.'];
                }

                $points = intval(get_option('gitlab_issue_points'));

                // Ignored if not awarding points for any issues
                if ($points < 0) {
                    return ['success' => false, 'error_details' => 'Ignored; no points are to be awarded.'];
                }

                require_code('points_escrow');
                require_code('templates');
                require_code('gitlab');
                require_lang('cms_homesite_gitlab');

                $_reason = '';
                if (isset($data['object_attributes']['title'])) {
                    $_reason = generate_truncation($data['object_attributes']['title'], 'right', 128);
                }
                $reason = do_lang('POINTS_OPENED_ISSUE', comcode_escape(strval($data['object_attributes']['id'])), comcode_escape($_reason));

                // Escrow for the issue creator
                escrow_points($GLOBALS['FORUM_DRIVER']->get_guest_id(), $member_id, $points, $reason, do_lang('GITLAB_ISSUE_AGREEMENT_CREATOR'), (time() * 60 * 60 * 24 * 90), 'gitlab-issue', strval($data['object_attributes']['id']), true, null);

                $resolve_points = intval(get_option('gitlab_issue_resolve_points'));
                if ($resolve_points > 0) {
                    // Escrow for the issue resolver
                    escrow_points($GLOBALS['FORUM_DRIVER']->get_guest_id(), null, $resolve_points, $reason, do_lang('GITLAB_ISSUE_AGREEMENT_RESOLVER'), (time() * 60 * 60 * 24 * 90), 'gitlab-issue', strval($data['object_attributes']['id']), true, null);
                }

                // Add a specialised note
                gitlab_add_issue_note($data['object_attributes']['id'], do_lang('GITLAB_ISSUE_RESOLVE_NOTE', comcode_escape(build_url(['page' => 'sponsor_issue'], get_page_zone('sponsor_issue'))->evaluate())));

                return ['success' => true];

            case 'update':
            case 'close':
                if (!isset($data['changes']['labels'])) {
                    return ['success' => false, 'error_details' => 'No labels set.'];
                }

                // Nothing to do if the Resolved label was already applied previously
                $had_previous = false;
                foreach ($data['changes']['labels']['previous'] as $label) {
                    if ($label['title'] == 'Resolved') {
                        $had_previous = true;
                        break;
                    }
                }

                foreach ($data['changes']['labels']['current'] as $label) {
                    if ($label['title'] == 'Resolved') {
                        require_code('points_escrow');

                        $member_id = gitlab_webhook_payload_to_member($data);

                        // Could not find any members?
                        if ($member_id === null) {
                            // The issue creator should still get the points
                            $rows = $GLOBALS['SITE_DB']->query_select('escrow', ['*'], ['sending_member' => $GLOBALS['FORUM_DRIVER']->get_guest_id(), 'content_type' => 'gitlab-issue', 'content_id' => strval($data['object_attributes']['id'])], ' AND receiving_member IS NOT NULL');
                            foreach ($rows as $row) {
                                _complete_escrow($row);
                            }

                            // But cancel / refund all other escrows
                            cancel_all_escrows_by_content('gitlab-issue', strval($data['object_attributes']['id']), do_lang('GITLAB_ISSUE_RESOLVED_NO_MEMBER'));
                            return ['success' => false, 'error_details' => 'No member found matching the GitLab user responsible for adding the resolved flag on the issue.'];
                        }

                        // Award all sponsored points on this issue to the resolving member. This also completes the escrow for the issue creator.
                        complete_all_escrows_by_content($member_id, 'gitlab-issue', strval($data['object_attributes']['id']));

                        // Let everyone know on the issue we officially resolved it on the homesite
                        if (!$had_previous) {
                            gitlab_add_issue_note($data['object_attributes']['id'], do_lang('GITLAB_ISSUE_RESOLVED'));
                        }
                    }
                }

                return ['success' => true];
        }
    }
}
