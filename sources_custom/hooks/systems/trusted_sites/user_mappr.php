<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_mappr
 */

/**
 * Hook class.
 */
class Hook_trusted_sites_user_mappr
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
        if (!addon_installed('user_mappr')) {
            return;
        }

        if (get_option('google_apis_api_key') != '') {
            $sites[] = 'maps.googleapis.com';
        }
    }
}
