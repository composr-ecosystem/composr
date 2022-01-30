<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sugarcrm
 */

/**
 * Hook class.
 */
class Hook_privacy_sugarcrm extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('sugarcrm')) {
            return null;
        }

        require_lang('sugarcrm');

        return [
            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
                [
                    'heading' => do_lang('INFORMATION_STORAGE'),
                    'action' => do_lang_tempcode('PRIVACY_ACTION_sugarcrm'),
                    'reason' => do_lang_tempcode('PRIVACY_REASON_sugarcrm'),
                ],
            ],

            'database_records' => [
            ],
        ];
    }
}
