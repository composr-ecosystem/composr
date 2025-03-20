<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    gitlab_shared
 */

function init__gitlab()
{
    if (!defined('COMPOSR_GITLAB_PROJECT_ID')) {
        define('COMPOSR_GITLAB_PROJECT_ID', '14182874');
    }

    require_lang('gitlab_shared');
}

/**
 * Add a comment to the specified commit.
 *
 * @param  ID_TEXT $commit_id The commit
 * @param  LONG_TEXT $comment The comment to add
 * @throws \Exception
 */
function gitlab_add_commit_comment(string $commit_id, string $comment)
{
    $token = get_option('gitlab_access_token');
    if ($token == '') {
        throw new Exception('GitLab access token must be defined in configuration.');
    }

    $url = 'https://gitlab.com/api/v4/projects/' . COMPOSR_GITLAB_PROJECT_ID . '/repository/commits/' . $commit_id . '/comments';

    $result = cms_http_request($url, ['timeout' => 10.0, 'trigger_errors' => false, 'extra_headers' => ['Private-Token' => $token], 'post_params' => ['note' => $comment]]);
    if (substr($result->message, 0, 1) != '2') {
        throw new Exception($result->data);
    }
}

/**
 * Add a note to the specified issue.
 *
 * @param  integer $issue_id The issue ID
 * @param  LONG_TEXT $note The note to add
 * @throws \Exception
 */
function gitlab_add_issue_note(int $issue_id, string $note)
{
    $token = get_option('gitlab_access_token');
    if ($token == '') {
        throw new Exception('GitLab access token must be defined in configuration.');
    }

    $url = 'https://gitlab.com/api/v4/projects/' . COMPOSR_GITLAB_PROJECT_ID . '/issues/' . strval($issue_id) . '/notes';

    $result = cms_http_request($url, ['timeout' => 10.0, 'trigger_errors' => false, 'extra_headers' => ['Private-Token' => $token], 'post_params' => ['body' => $note]]);
    if (substr($result->message, 0, 1) != '2') {
        throw new Exception($result->data);
    }
}
