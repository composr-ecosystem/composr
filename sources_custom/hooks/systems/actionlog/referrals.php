<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    referrals
 */

/**
 * Hook class.
 */
class Hook_actionlog_referrals extends Hook_actionlog
{
    /**
     * Get details of action log entry types handled by this hook.
     *
     * @return array Map of handler data in standard format
     */
    public function get_handlers() : array
    {
        if (!addon_installed('referrals')) {
            return [];
        }

        if (get_forum_type() != 'cns') {
            return [];
        }

        require_lang('referrals');

        return [
            '_MANUALLY_ADJUST_SCHEME_SETTINGS' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 0,
                'followup_page_links' => [
                    'REFERRALS' => '_SEARCH:admin_referrals',
                ],
            ],
        ];
    }
}
