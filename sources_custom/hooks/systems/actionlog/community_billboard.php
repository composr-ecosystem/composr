<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_actionlog_community_billboard extends Hook_actionlog
{
    /**
     * Get details of action log entry types handled by this hook.
     *
     * @return array Map of handler data in standard format
     */
    public function get_handlers()
    {
        if (!addon_installed('community_billboard')) {
            return [];
        }

        require_lang('community_billboard');

        return [
            'ADD_COMMUNITY_BILLBOARD' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'EDIT_THIS_COMMUNITY_BILLBOARD' => '_SEARCH:admin_community_billboard:_edit:{ID}',
                    'ADD_COMMUNITY_BILLBOARD' => '_SEARCH:admin_community_billboard:add',
                ],
            ],
            'EDIT_COMMUNITY_BILLBOARD' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'EDIT_THIS_COMMUNITY_BILLBOARD' => '_SEARCH:admin_community_billboard:_edit:{ID}',
                    'ADD_COMMUNITY_BILLBOARD' => '_SEARCH:admin_community_billboard:add',
                ],
            ],
            'CHOOSE_COMMUNITY_BILLBOARD' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'EDIT_THIS_COMMUNITY_BILLBOARD' => '_SEARCH:admin_community_billboard:_edit:{ID}',
                    'ADD_COMMUNITY_BILLBOARD' => '_SEARCH:admin_community_billboard:add',
                ],
            ],
            'DELETE_COMMUNITY_BILLBOARD' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'ADD_COMMUNITY_BILLBOARD' => '_SEARCH:admin_community_billboard:add',
                ],
            ],
        ];
    }
}
