<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    patreon
 */

/**
 * Hook class.
 */
class Hook_cron_patreon
{
    protected $adapters;

    /**
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  boolean $calculate_num_queued Calculate the number of items queued, if possible
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, bool $calculate_num_queued) : ?array
    {
        if (!addon_installed('patreon')) {
            return null;
        }

        if (!addon_installed('hybridauth')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        require_code('hybridauth_admin');

        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        list($hybridauth, , $providers) = initiate_hybridauth_admin(0, 'admin', 'Patreon');

        if (!isset($providers['Patreon'])) {
            return null;
        }

        $this->adapters = [];

        if ($providers['Patreon']['enabled']) {
            $adapter = $hybridauth->getAdapter('Patreon');
            if ($adapter->isConnected()) {
                $this->adapters[] = $adapter;
            }
        }

        foreach ($providers['Patreon']['alternate_configs'] as $alternate_config) {
            list($_hybridauth, , $_providers) = initiate_hybridauth_admin(0, $alternate_config, 'Patreon');
            if ($_providers['Patreon']['enabled']) {
                $adapter = $_hybridauth->getAdapter('Patreon');
                if ($adapter->isConnected()) {
                    $this->adapters[] = $adapter;
                }
            }
        }

        cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        cms_ini_set('ocproducts.xss_detect', $before_xss_detect);

        if (empty($this->adapters)) {
            return null;
        }

        return [
            'label' => 'Patreon patron sync',
            'num_queued' => null,
            'minutes_between_runs' => 24 * 60,
        ];
    }

    /**
     * Run function for system scheduler scripts. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        require_code('patreon');
        patreon_sync($this->adapters);
    }
}
