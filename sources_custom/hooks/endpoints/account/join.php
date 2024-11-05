<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_endpoint_account_join
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
        if (!addon_installed('composr_mobile_sdk')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        return [
            'authorization' => false,
            'log_stats_event' => 'account/join',
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
        require_code('cns_join');
        cns_require_all_forum_stuff();
        list($message, $member_id) = cns_join_actual('', false); // TODO: GDPR Need a method to require declaration acceptance before an account is created

        $_message = $message->evaluate();

        return [
            'message' => ($_message == '') ? null : $_message,
        ];
    }
}
