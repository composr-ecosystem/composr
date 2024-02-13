<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composer
 */

/**
 * Hook class.
 */
class Hook_startup_composer
{
    public function run()
    {
        if (!addon_installed('composer')) {
            return;
        }

        if (is_file(get_file_base() . '/vendor/autoload.php')) {
            require(get_file_base() . '/vendor/autoload.php');
        } elseif (is_file(get_file_base() . '/sources_custom/vendor/autoload.php')) {
            require(get_file_base() . '/sources_custom/vendor/autoload.php');
        }
    }
}
