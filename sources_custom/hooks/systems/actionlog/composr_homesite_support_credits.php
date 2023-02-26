<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_actionlog_composr_homesite_support_credits extends Hook_actionlog
{
    /**
     * Get details of action log entry types handled by this hook.
     *
     * @return array Map of handler data in standard format
     */
    public function get_handlers() : array
    {
        if (!addon_installed('composr_homesite_support_credits')) {
            return [];
        }

        if (get_forum_type() != 'cns') {
            return [];
        }

        require_lang('customers');

        return [
            'CHARGE_CUSTOMER' => [
                'flags' => ACTIONLOG_FLAGS_NONE,
                'cma_hook' => 'member',
                'identifier_index' => 0,
                'written_context_index' => null,
                'followup_page_links' => [
                    'VIEW_PROFILE' => ['FORUM_DRIVER__PROFILE_URL', '{ID}'],
                    'CHARGE_CUSTOMER' => '_SEARCH:admin_customers:charge:member_id={ID}',
                ],
            ],
        ];
    }

    /**
     * Get written context for an action log entry handled by this hook.
     *
     * @param  array $actionlog_row Action log row
     * @param  array $handler_data Handler data
     * @param  ?string $identifier Identifier (null: none)
     * @return ?string Written context (null: none)
     */
    protected function get_written_context(array $actionlog_row, array $handler_data, ?string $identifier) : ?string
    {
        switch ($actionlog_row['the_type']) {
            case 'CHARGE_CUSTOMER':
                $username = $GLOBALS['FORUM_DRIVER']->get_username(intval($actionlog_row['param_a']));
                if ($username === null) {
                    $username = '#' . $actionlog_row['param_a'];
                }

                $written_context = do_lang('SOMETHING_FROM', integer_format(intval($actionlog_row['param_b']), 0), $username);
                return $written_context;
        }

        return parent::get_written_context($actionlog_row, $handler_data, $identifier);
    }
}
