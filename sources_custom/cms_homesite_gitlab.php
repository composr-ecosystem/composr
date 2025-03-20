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
 * Given a GitLab webhook payload, try to figure out the homesite member who performed this action.
 * This will first try Hybridauth, then matching e-mail address, then matching username.
 *
 * @param  array $data The associative array of payload data from GitLab
 * @return ?MEMBER The site member who performed this action (null: not found)
 */
function gitlab_webhook_payload_to_member(array $data) : ?int
{
    $member_id = null;

    // Maximum authenticity: Hybridauth linked GitLab account
    if (($member_id === null) && (isset($data['user']['id']))) {
        $member_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', ['m_password_compat_scheme' => 'GitLab', 'm_pass_hash_salted' => strval($data['user']['id'])], 'ORDER BY m_join_time DESC,id DESC');
    }

    // Medium authenticity: matching e-mail address
    if (($member_id === null) && (isset($data['user']['email']))) {
        $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($data['user']['email']);
    }

    // Low authenticity: matching username
    if (($member_id === null) && (isset($data['user']['username']))) {
        $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($data['user']['username']);
    }

    return $member_id;
}
