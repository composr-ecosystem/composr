<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class _api_translation_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('translation');

        set_option('bing_translator_enabled', '1');
        set_option('google_translate_enabled', '1');
        $this->load_key_options('azure');
        $this->load_key_options('google');
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
