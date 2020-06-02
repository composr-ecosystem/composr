<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    columns
 */

/**
 * Hook class.
 */
class Hook_startup_columns
{
    public function run()
    {
        if (!addon_installed('columns')) {
            return;
        }

        require_css('columns');
        require_javascript('jquery');
        require_javascript('columns');
    }
}
