<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    photo_verification
 */

/**
 * Hook class.
 */
class Hook_actionlog_photo_verification extends Hook_actionlog
{
    /**
     * Get details of action log entry types handled by this hook.
     *
     * @return array Map of handler data in standard format
     */
    public function get_handlers() : array
    {
        if (!addon_installed('photo_verification')) {
            return [];
        }
        if (!addon_installed('tickets')) {
            return [];
        }

        require_lang('tickets');
        require_lang('photo_verification');

        return [
            'LOG_VERIFICATION_REQUEST' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'VIEW_SUPPORT_TICKET' => '_SEARCH:tickets:ticket:{ID}',
                ],
            ],
        ];
    }
}
