<?php /*

 Composr
 Copyright (c) Christopher Graham/Tapatalk, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
