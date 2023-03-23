<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    buildr
 */

/**
 * Hook class.
 */
class Hook_admin_themewizard_buildr
{
    /**
     * Find details of images to include/exclude in the Theme Wizard.
     *
     * @return array A pair: List of theme image patterns to include, List of theme image patterns to exclude
     */
    public function run() : array
    {
        if (!addon_installed('buildr')) {
            return [[], []];
        }

        return [['logo/buildr-logo',], []];
    }
}
