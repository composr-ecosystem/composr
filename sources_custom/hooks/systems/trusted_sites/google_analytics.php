<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    google_analytics
 */

/*FORCE_ORIGINAL_LOAD_FIRST*/

/**
 * Hook class.
 */
class Hx_trusted_sites_google_analytics extends Hook_trusted_sites_google_analytics
{
    /**
     * Detect what needs to be 'added' to the trusted_sites_2 option.
     *
     * @param  array $sites List of trusted sites (written by reference)
     */
    public function find_trusted_sites_2(array &$sites)
    {
        parent::find_trusted_sites_2($sites);

        if (!addon_installed('google_analytics')) {
            return;
        }

        if ((get_option('ga_property_view_id') != '') && (get_option('google_apis_client_id') != '') && (get_option('google_apis_client_secret') != '')) {
            $sites[] = 'apis.google.com';
            $sites[] = 'stats.g.doubleclick.net';
            $sites[] = 'google-analytics.com';
        }
    }
}
