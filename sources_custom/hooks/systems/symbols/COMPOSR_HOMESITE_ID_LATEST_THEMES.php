<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite
 */

/**
 * Hook class.
 */
class Hook_symbol_COMPOSR_HOMESITE_ID_LATEST_THEMES
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run(array $param) : string
    {
        if (!addon_installed('composr_homesite')) {
            return '';
        }

        if (!addon_installed('downloads')) {
            return '';
        }

        require_code('composr_homesite');
        $version = get_latest_version_basis_number();
        if ($version === null) {
            return '1'; // No versions added yet
        }

        require_code('addon_publish');
        $cat_id = find_addon_category_download_category('Version ' . $version);
        $cat_id = find_addon_category_download_category('Themes', $cat_id);

        return strval($cat_id);
    }
}
