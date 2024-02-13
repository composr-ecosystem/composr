<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_actionlog_karma extends Hook_actionlog
{
    /**
     * Get details of action log entry types handled by this hook.
     *
     * @return array Map of handler data in standard format
     */
    public function get_handlers() : array
    {
        if (!addon_installed('karma')) {
            return [];
        }

        require_lang('karma');

        return [
            'AMEND_KARMA' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => null,
                'followup_page_links' => [
                    'VIEW_KARMA_RECORD' => '_SEARCH:admin_karma:view:{ID}',
                ],
            ],
            'REVERSED_KARMA' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => null,
                'followup_page_links' => [
                    'VIEW_KARMA_RECORD' => '_SEARCH:admin_karma:view:{ID}',
                ],
            ],
        ];
    }
}
