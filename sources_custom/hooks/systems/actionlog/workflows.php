<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    workflows
 */

/**
 * Hook class.
 */
class Hook_actionlog_workflows extends Hook_actionlog
{
    /**
     * Get details of action log entry types handled by this hook.
     *
     * @return array Map of handler data in standard format
     */
    public function get_handlers() : array
    {
        if (!addon_installed('workflows')) {
            return [];
        }

        require_lang('workflows');

        return [
            'ADD_WORKFLOW' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'EDIT_WORKFLOW' => '_SEARCH:admin_workflows:_edit:{ID}',
                    'ADD_WORKFLOW' => '_SEARCH:admin_workflows:add',
                ],
            ],
            'EDIT_WORKFLOW' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'EDIT_WORKFLOW' => '_SEARCH:admin_workflows:_edit:{ID}',
                    'ADD_WORKFLOW' => '_SEARCH:admin_workflows:add',
                ],
            ],
            'DELETE_WORKFLOW' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'ADD_WORKFLOW' => '_SEARCH:admin_workflows:add',
                ],
            ],
        ];
    }
}
