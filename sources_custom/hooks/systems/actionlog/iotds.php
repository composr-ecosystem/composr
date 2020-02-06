<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Hook class.
 */
class Hook_actionlog_iotds extends Hook_actionlog
{
    /**
     * Get details of action log entry types handled by this hook.
     *
     * @return array Map of handler data in standard format
     */
    public function get_handlers()
    {
        if (!addon_installed('iotds')) {
            return [];
        }

        require_lang('iotds');

        return [
            'ADD_IOTD' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => 'iotd',
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'VIEW' => '_SEARCH:iotds:view:{ID}',
                    'EDIT_THIS_IOTD' => '_SEARCH:cms_iotds:_edit:{ID}',
                    'ADD_IOTD' => '_SEARCH:cms_iotds:add',
                ],
            ],
            'EDIT_IOTD' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => 'iotd',
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'VIEW' => '_SEARCH:iotds:view:{ID}',
                    'EDIT_THIS_IOTD' => '_SEARCH:cms_iotds:_edit:{ID}',
                    'ADD_IOTD' => '_SEARCH:cms_iotds:add',
                ],
            ],
            'CHOOSE_IOTD' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => 'iotd',
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'VIEW' => '_SEARCH:iotds:view:{ID}',
                    'EDIT_THIS_IOTD' => '_SEARCH:cms_iotds:_edit:{ID}',
                    'ADD_IOTD' => '_SEARCH:cms_iotds:add',
                ],
            ],
            'DELETE_IOTD' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => 'iotd',
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'ADD_IOTD' => '_SEARCH:cms_iotds:add',
                ],
            ],
        ];
    }
}
