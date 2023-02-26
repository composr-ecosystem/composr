<?php /*

 Composr
 Copyright (c) ocProducts/Tapatalk, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    transliteration
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__transliteration()
{
    if (!function_exists('transliterator_transliterate')) {
        require_code('Transliterator/Transliterator');

        class BehatTransliterator extends \Behat\Transliterator\Transliterator
        {
        }

        function transliterator_transliterate($whatever, $text)
        {
            $transliterator = new BehatTransliterator();
            return $transliterator->transliterate($text);
        }
    }
}
