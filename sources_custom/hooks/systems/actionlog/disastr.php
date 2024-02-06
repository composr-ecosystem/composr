<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    disastr
 */

/**
 * Hook class.
 */
class Hook_actionlog_disastr extends Hook_actionlog
{
    /**
     * Get details of action log entry types handled by this hook.
     *
     * @return array Map of handler data in standard format
     */
    public function get_handlers() : array
    {
        if (!addon_installed('disastr')) {
            return [];
        }

        if (get_forum_type() != 'cns') {
            return [];
        }

        require_lang('disastr');

        return [
            'ADD_DISEASE' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'EDIT_THIS_DISEASE' => '_SEARCH:admin_disastr:_edit:{ID}',
                    'ADD_DISEASE' => '_SEARCH:admin_disastr:add',
                ],
            ],
            'EDIT_DISEASE' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'EDIT_THIS_DISEASE' => '_SEARCH:admin_disastr:_edit:{ID}',
                    'ADD_DISEASE' => '_SEARCH:admin_disastr:add',
                ],
            ],
            'DELETE_DISEASE' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'ADD_DISEASE' => '_SEARCH:admin_disastr:add',
                ],
            ],
        ];
    }
}
