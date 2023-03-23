<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
