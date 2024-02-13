<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_admin_import_types_community_billboard
{
    /**
     * Get a map of valid import types.
     *
     * @return array A map from codename to the language string that names them to the user
     */
    public function run() : array
    {
        if (!addon_installed('community_billboard')) {
            return [];
        }

        return [
            'community_billboard' => 'COMMUNITY_BILLBOARD_ARCHIVE',
        ];
    }
}
