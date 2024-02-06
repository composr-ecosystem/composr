<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

/**
 * Hook class.
 */
class Hook_actionlog_composr_tutorials extends Hook_actionlog
{
    /**
     * Get details of action log entry types handled by this hook.
     *
     * @return array Map of handler data in standard format
     */
    public function get_handlers() : array
    {
        if (!addon_installed('composr_tutorials')) {
            return [];
        }

        require_lang('tutorials');

        return [
            'ADD_TUTORIAL' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'VIEW' => '{URL}',
                    'EDIT_THIS_TUTORIAL' => '_SEARCH:cms_tutorials:_edit:{ID}',
                    'ADD_TUTORIAL' => '_SEARCH:cms_tutorials:add',
                ],
            ],
            'EDIT_TUTORIAL' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'VIEW' => '{URL}',
                    'EDIT_THIS_TUTORIAL' => '_SEARCH:cms_tutorials:_edit:{ID}',
                    'ADD_TUTORIAL' => '_SEARCH:cms_tutorials:add',
                ],
            ],
            'DELETE_TUTORIAL' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => null,
                'identifier_index' => 0,
                'written_context_index' => 1,
                'followup_page_links' => [
                    'ADD_TUTORIAL' => '_SEARCH:cms_tutorials:add',
                ],
            ],
        ];
    }

    /**
     * Get details of action log entry types handled by this hook.
     *
     * @param  array $actionlog_row Action log row
     * @param  ?string $identifier The identifier associated with this action log entry (null: unknown / none)
     * @param  ?string $written_context The written context associated with this action log entry (null: unknown / none)
     * @param  array $bindings Default bindings
     */
    protected function get_extended_actionlog_bindings(array $actionlog_row, ?string $identifier, ?string $written_context, array &$bindings)
    {
        switch ($actionlog_row['the_type']) {
            case 'ADD_TUTORIAL':
            case 'EDIT_TUTORIAL':
                if ($identifier !== null) {
                    $url = $GLOBALS['SITE_DB']->query_select_value_if_there('tutorials_external', 't_url', ['id' => intval($identifier)]);
                    if ($url !== null) {
                        $bindings += [
                            'URL' => $url,
                        ];
                    }
                }
                break;
        }
    }
}
