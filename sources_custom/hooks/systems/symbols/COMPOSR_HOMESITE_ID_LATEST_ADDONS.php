<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/**
 * Hook class.
 */
class Hook_symbol_COMPOSR_HOMESITE_ID_LATEST_ADDONS
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run($param)
    {
        require_code('composr_homesite');
        $version = get_latest_version_pretty();
        if ($version === null && $GLOBALS['DEV_MODE']) { // Not uploaded any releases to dev site?
            $version = float_to_raw_string(cms_version_number(), 2, true);
        }
        if ($version === null) {
            return '1'; // No versions added yet
        }

        require_code('addon_publish');
        $cat_id = find_addon_category_download_category('Version ' . $version);

        return strval($cat_id);
    }
}
