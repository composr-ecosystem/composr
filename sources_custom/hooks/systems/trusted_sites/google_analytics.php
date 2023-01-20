<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    google_analytics
 */

/**
 * Hook class.
 */
class Hook_trusted_sites_google_analytics
{
    /**
     * Detect what needs to be 'added' to the trusted_sites_1 option.
     *
     * @param  array $sites List of trusted sites (written by reference)
     */
    public function find_trusted_sites_1(array &$sites)
    {
    }

    /**
     * Detect what needs to be 'added' to the trusted_sites_2 option.
     *
     * @param  array $sites List of trusted sites (written by reference)
     */
    public function find_trusted_sites_2(array &$sites)
    {
        if (!addon_installed('google_analytics')) {
            return;
        }

        if ((get_option('ga_property_view_id') != '') && (get_option('google_apis_client_id') != '') && (get_option('google_apis_client_secret') != '')) {
            $sites[] = 'apis.google.com';
            $sites[] = 'google-analytics.com';
            $sites[] = 'stats.g.doubleclick.net';
        }

    }
}
