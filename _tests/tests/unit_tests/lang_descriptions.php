<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class lang_descriptions_test_set extends cms_test_case
{
    protected $lang_files;

    public function setUp()
    {
        parent::setUp();

        require_code('lang_compile');
        require_code('lang2');

        $this->lang_files = get_lang_files(fallback_lang());

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
    }

    public function testDescriptionsLoad()
    {
        $descriptions = get_lang_file_section(fallback_lang(), 'global', 'descriptions');
        $this->assertTrue(!empty($descriptions));
    }

    public function testDescriptionsMerge()
    {
        cms_file_put_contents_safe(get_file_base() . '/lang/' . fallback_lang() . '/footest.ini', "[descriptions]\nA=old\nB=old");
        cms_file_put_contents_safe(get_file_base() . '/lang_custom/' . fallback_lang() . '/footest.ini', "[descriptions]\nA=new\nC=new");

        $descriptions = get_lang_file_section(fallback_lang(), 'footest', 'descriptions');

        $this->assertTrue($descriptions['A'] == 'new');
        $this->assertTrue($descriptions['B'] == 'old');
        $this->assertTrue($descriptions['C'] == 'new');

        unlink(get_file_base() . '/lang/' . fallback_lang() . '/footest.ini');
        unlink(get_file_base() . '/lang_custom/' . fallback_lang() . '/footest.ini');
    }

    public function testNoMissingOrphanDescriptions()
    {
        foreach (array_keys($this->lang_files) as $file) {
            $descriptions = get_lang_file_section(fallback_lang(), $file, 'descriptions');
            $strings = get_lang_file_section(fallback_lang(), $file, 'strings');
            foreach ($descriptions as $key => $val) {
                $this->assertTrue(isset($strings[$key]), 'Description has no string to match: ' . $key);
            }
        }
    }

    public function testNoMissingDescriptions()
    {
        foreach (array_keys($this->lang_files) as $file) {
            $descriptions = get_lang_file_section(fallback_lang(), $file, 'descriptions');
            $strings = get_lang_file_section(fallback_lang(), $file, 'strings');
            foreach ($strings as $key => $val) {
                if (cms_strtolower_ascii($key) == $key) {
                    $this->assertTrue(isset($descriptions[$key]), 'Lower case string has no description to match: ' . $key);
                }
            }
        }
    }

    public function testNoNonsenseSections()
    {
        foreach (array_keys($this->lang_files) as $file) {
            $path_a = get_custom_file_base() . '/lang_custom/' . fallback_lang() . '/' . $file . '.ini';
            $path_b = get_file_base() . '/lang/' . fallback_lang() . '/' . $file . '.ini';
            foreach ([$path_a, $path_b] as $path) {
                if (is_file($path)) {
                    $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
                    $matches = [];
                    $num_matches = preg_match_all('#^\[(\w+)\]$#m', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $this->assertTrue(in_array($matches[1][$i], ['descriptions', 'strings', 'runtime_processing']), 'Unknown language section ' . $matches[1][$i] . ' in ' . $path);
                    }
                }
            }
        }
    }
}
