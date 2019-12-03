<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class translation_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('translation');

        // Please don't use these on a live site, we just need these to test against
        set_option('azure_api_key', '70f9d62f90c6435189ca20ca17d06c54');
        set_option('google_apis_api_key', 'AIzaSyD-jqeO_HlD1bLmA68JhAJOBajZw96-UHE');

        set_option('bing_translator_enabled', '1');
        set_option('google_translate_enabled', '1');
    }

    public function testTranslation()
    {
        $this->assertTrue(has_translation());

        $hooks = [
            'bing_translator',
            'google_translate',
        ];

        foreach ($hooks as $hook) {
            $GLOBALS['SITE_DB']->query_delete('translation_cache');

            $translation_object = get_translation_object_for_hook($hook);
            $errormsg = null;

            $from = 'EN';
            $to = 'FR';

            $this->assertTrue(has_translation($from, $to, $translation_object, $errormsg));

            $from_text = 'Hello';
            $to_text = translate_text($from_text, TRANS_TEXT_CONTEXT__AUTODETECT, $from, $to, $hook, $errormsg);
            $this->assertTrue($to_text == 'Bonjour' || $to_text == 'Salut', 'Got ' . $to_text . ' (error message is ' . $errormsg . ')');

            if ($hook == 'google_translate') {
                $this->assertTrue(get_translation_credit($from, $to, $hook) != '');
            }
        }
    }
}
