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
class Hook_endpoint_cms_homesite_tracker_sponsorship
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
            'authorization' => ['session'],
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
        if (!addon_installed('points')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
        }

        $bug_id = get_param_integer('bug_id', 0);
        $amount = get_param_integer('amount', 0);
        $user_id = get_param_integer('user_id', 0);

        $reason = 'Sponsored issue #' . strval($bug_id);
        $agreement = 'This escrow shall be considered satisfied when tracker issue #' . strval($bug_id) . ' has been resolved. The resolving member will receive the points escrowed. Should the issue be closed / not implemented, the escrow shall be considered cancelled and all points refunded.';

        // Integrity check - if user_id does not match what is established by the session, then access denied
        if ($user_id != get_member()) {
            $_log_file = get_custom_file_base() . '/data_custom/endpoints.log';
            if (is_file($_log_file)) {
                require_code('files');
                $log_message = loggable_date() . ' ACCESS DENIED (failed user_id integrity check) to endpoint /cms_homesite/tracker_sponsorship/ by IP address ' . get_ip_address() . "\n";
                $log_file = cms_fopen_text_write($_log_file, true, 'ab');
                fwrite($log_file, $log_message);
                flock($log_file, LOCK_UN);
                fclose($log_file);
            }

            access_denied('ACCESS_DENIED', 'REST endpoint /cms_homesite/tracker_sponsorship/ (failed user_id integrity check)');
        }

        require_code('points_escrow');

        // Create the escrow for the sponsorship
        if ($type == 'add') {
            $id = escrow_points(get_member(), null, $amount, $reason, $agreement, null, 'tracker_issue', strval($bug_id));
            if ($id !== null) {
                return ['success' => true, 'id' => $id];
            }
            return ['success' => false, 'error_details' => 'Could not create an escrow for this sponsorship. Perhaps you do not have enough points?'];
        }

        // Editing a sponsorship requires cancelling the previous escrow and making a new one
        if ($type == 'edit') {
            $id = cancel_escrow($id, get_member(), 'Sponsorship amended');
            if ($id === null) {
                return ['success' => false, 'error_details' => 'Could not cancel the escrow / refund the points of the original sponsorship.'];
            }
            $id = escrow_points(get_member(), null, $amount, $reason, $agreement, null, 'tracker_issue', strval($bug_id));
            if ($id !== null) {
                return ['success' => true, 'id' => $id];
            }
            return ['success' => false, 'error_details' => 'We cancelled the old sponsorship and refunded the points, but could not create a new sponsorship. Perhaps you do not have enough points?'];
        }

        return ['success' => false, 'error_details' => 'Invalid Type'];
    }
}
