<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
 */

/**
 * Hook class.
 */
class Hook_admin_import_types_mantis
{
    /**
     * Get a map of valid import types.
     *
     * @return array A map from codename to the language string that names them to the user
     */
    public function run() : array
    {
        if (!addon_installed('cms_homesite')) {
            return [];
        }
        if (!addon_installed('cms_homesite_tracker')) {
            return [];
        }

        require_lang('cms_homesite');

        return [
            'mantis' => 'TRACKER_ISSUES',
        ];
    }
}
