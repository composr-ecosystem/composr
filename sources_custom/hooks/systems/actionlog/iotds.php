<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

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
            return array();
        }

        require_lang('iotds');

        return array(
            'ADD_IOTD' => array(
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => 'iotd',
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => array(
                    'VIEW' => '_SEARCH:iotds:view:{ID}',
                    'EDIT_THIS_IOTD' => '_SEARCH:cms_iotds:_edit:{ID}',
                    'ADD_IOTD' => '_SEARCH:cms_iotds:add',
                ),
            ),
            'EDIT_IOTD' => array(
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => 'iotd',
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => array(
                    'VIEW' => '_SEARCH:iotds:view:{ID}',
                    'EDIT_THIS_IOTD' => '_SEARCH:cms_iotds:_edit:{ID}',
                    'ADD_IOTD' => '_SEARCH:cms_iotds:add',
                ),
            ),
            'CHOOSE_IOTD' => array(
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => 'iotd',
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => array(
                    'VIEW' => '_SEARCH:iotds:view:{ID}',
                    'EDIT_THIS_IOTD' => '_SEARCH:cms_iotds:_edit:{ID}',
                    'ADD_IOTD' => '_SEARCH:cms_iotds:add',
                ),
            ),
            'DELETE_IOTD' => array(
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => 'iotd',
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => array(
                    'ADD_IOTD' => '_SEARCH:cms_iotds:add',
                ),
            ),
        );
    }
}
