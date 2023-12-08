<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composer
 */

/**
 * Hook class.
 */
class Hook_startup_composer
{
    public function run()
    {
        if (is_file(get_file_base() . '/vendor/autoload.php')) {
            require(get_file_base() . '/vendor/autoload.php');
        }
    }
}
