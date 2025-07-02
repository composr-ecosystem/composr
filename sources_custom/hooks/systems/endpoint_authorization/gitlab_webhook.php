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
class Hook_endpoint_authorization_gitlab_webhook
{
    /**
     * Run an endpoint authorisation.
     *
     * @param  array $authorizations Array of authorizations allowed by this call
     * @param  ?MEMBER $member The authorised member ID, passed by reference (null: we did not authorise to a specific member)
     * @param  ID_TEXT $hook_type The endpoint hook type called
     * @param  ID_TEXT $hook The endpoint hook called
     * @param  ?ID_TEXT $type The type of endpoint request made (null: none)
     * @param  ?ID_TEXT $id The resource ID requested in the endpoint call (null: none)
     * @return boolean Whether this call is authorised; false means check other authorisation hooks if applicable or throw access denied
     */
    public function run(array $authorizations, ?int &$member, string $hook_type, string $hook, ?string $type, ?string $id) : bool
    {
        if (!addon_installed('cms_homesite_gitlab')) {
            return false;
        }

        $handles = ['gitlab_webhook'];
        if (empty(array_intersect($handles, $authorizations))) {
            return false;
        }

        if (!isset($_SERVER['HTTP_X_GITLAB_TOKEN'])) {
            return false;
        }

        $expected_value = get_option('gitlab_webhook_secret');

        return ($_SERVER['HTTP_X_GITLAB_TOKEN'] === $expected_value);
    }
}
