<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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

        require_javascript('jquery');
    }
}
