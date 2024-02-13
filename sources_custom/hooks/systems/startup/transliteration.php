<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    transliteration
 */

/**
 * Hook class.
 */
class Hook_startup_transliteration
{
    public function run()
    {
        if (!addon_installed('transliteration')) {
            return;
        }

        require_code('transliteration');
    }
}
