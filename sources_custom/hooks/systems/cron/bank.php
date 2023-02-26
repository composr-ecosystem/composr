<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    bankr
 */

/**
 * Hook class.
 */
class Hook_cron_bank
{
    protected $to_be_restored;

    /**
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  ?boolean $calculate_num_queued Calculate the number of items queued, if possible (null: the hook may decide / low priority)
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, ?bool $calculate_num_queued) : ?array
    {
        if (!addon_installed('bankr')) {
            return null;
        }

        if (!addon_installed('points')) {
            return null;
        }
        if (!addon_installed('ecommerce')) {
            return null;
        }

        // Calculate on low priority
        if ($calculate_num_queued === null) {
            $calculate_num_queued = true;
        }

        if ($calculate_num_queued) {
            $this->to_be_restored = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'bank WHERE add_time<' . strval(time() - (30 * 24 * 60 * 60)));
            $num_queued = count($this->to_be_restored);
        } else {
            $num_queued = null;
        }

        return [
            'label' => 'Bankr restored deposits / dividends',
            'num_queued' => $num_queued,
            'minutes_between_runs' => 60,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        $bank_dividend = intval(get_option('bank_dividend'));

        foreach ($this->to_be_restored as $deposit) {
            if ($deposit['amount'] > 0) {
                require_code('points2');
                require_lang('bank');

                $restore_amount = round(floatval($deposit['amount']) * (1.0 + floatval($bank_dividend) / 100.0));
                points_credit_member($deposit['member_id'], do_lang('RESTORED_DEPOSIT'), intval($restore_amount), 0, true, 0, 'bank', 'deposit', strval($deposit['id']));

                $GLOBALS['SITE_DB']->query_delete('bank', ['id' => $deposit['id']], '', 1);
            }
        }
    }
}
