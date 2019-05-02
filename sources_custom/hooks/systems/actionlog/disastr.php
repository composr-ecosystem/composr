<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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
    public function get_handlers()
    {
        if (!addon_installed('disastr')) {
            return array();
        }

        require_lang('disastr');

        return array(
            'ADD_DISEASE' => array(
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => array(
                    'EDIT_THIS_DISEASE' => '_SEARCH:admin_disastr:_edit:{ID}',
                    'ADD_DISEASE' => '_SEARCH:admin_disastr:add',
                ),
            ),
            'EDIT_DISEASE' => array(
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => array(
                    'EDIT_THIS_DISEASE' => '_SEARCH:admin_disastr:_edit:{ID}',
                    'ADD_DISEASE' => '_SEARCH:admin_disastr:add',
                ),
            ),
            'DELETE_DISEASE' => array(
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => array(
                    'ADD_DISEASE' => '_SEARCH:admin_disastr:add',
                ),
            ),
        );
    }
}
