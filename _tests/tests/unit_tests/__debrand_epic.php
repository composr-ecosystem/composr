<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/*
 * The results of this test should be carefully reviewed. This test is intentionally very aggressive.
 * We do not test txt files as we expect branding to be acceptable in Comcode pages (e.g. tutorials).
 * We only test core software code as it is acceptable for a non-bundled addon to be branded in a hard-coded way.
 */

/**
 * Composr test case class (unit testing).
 */
class __debrand_epic_test_set extends cms_test_case
{
    protected $regex_to_check = [
        '/composr/i' => 'Detected hard-coded use of branded term \'Composr\'; consider using brand_name() or a generic term such as \'the software\'.',
        '/compo\.sr/i' => 'Detected hard-coded use of branded website \'compo.sr\'; consider using get_brand_base_url().', // LEGACY
        '/composr\.app/i' => 'Detected hard-coded use of branded website \'composr.app\'; consider using get_brand_base_url().',
    ];
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('files');
        require_code('files2');
    }

    public function testScripts()
    {
        require_code('third_party_code');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_CUSTOM_DIRS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);

        $dir_exceptions = array_merge(list_untouchable_third_party_directories(), [
        ]);
        $file_exceptions = array_merge(list_untouchable_third_party_files(), [
        ]);

        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $dir_exceptions) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $file_exceptions)) {
                continue;
            }

            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

            foreach ($this->regex_to_check as $regex => $message) {
                // Filename
                $counts = preg_match_all($regex, basename($path));
                $this->assertTrue(($counts == 0), $path . ' (file name): ' . $message);

                // File contents
                $counts = preg_match_all($regex, $c);
                if ($regex == '/composr/i') { // Subtract branding used in copyright comments from count; these are fine
                    $counts -= preg_match_all('/\s*composr[\r\n]\s*copyright \(c\)/i', $c);
                }
                $this->assertTrue(($counts == 0), $path . ' (file contents): ' . $message . ' (Found ' . integer_format($counts) . ')');
            }

            unset($c);
        }
    }

    public function testLang()
    {
        require_code('third_party_code');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_CUSTOM_DIRS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['ini']);

        $dir_exceptions = array_merge(list_untouchable_third_party_directories(), [
        ]);
        $file_exceptions = array_merge(list_untouchable_third_party_files(), [
        ]);

        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $dir_exceptions) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $file_exceptions)) {
                continue;
            }

            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

            foreach ($this->regex_to_check as $regex => $message) {
                // Filename
                $counts = preg_match_all($regex, basename($path));
                $this->assertTrue(($counts == 0), $path . ' (file name): ' . $message);

                // File contents
                $counts = preg_match_all($regex, $c);
                $this->assertTrue(($counts == 0), $path . ' (file contents): ' . $message . ' (Found ' . integer_format($counts) . ')');
            }

            unset($c);
        }
    }

    public function testJsAndCss()
    {
        require_code('third_party_code');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_CUSTOM_DIRS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['js', 'css']);

        $dir_exceptions = array_merge(list_untouchable_third_party_directories(), [
        ]);
        $file_exceptions = array_merge(list_untouchable_third_party_files(), [
        ]);

        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $dir_exceptions) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $file_exceptions)) {
                continue;
            }

            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

            foreach ($this->regex_to_check as $regex => $message) {
                // Filename
                $counts = preg_match_all($regex, basename($path));
                $this->assertTrue(($counts == 0), $path . ' (file name): ' . $message);

                // File contents
                $counts = preg_match_all($regex, $c);
                $this->assertTrue(($counts == 0), $path . ' (file contents): ' . $message . ' (Found ' . integer_format($counts) . ')');
            }

            unset($c);
        }
    }

    public function testTemplates()
    {
        require_code('third_party_code');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_CUSTOM_DIRS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['tpl']);

        $dir_exceptions = array_merge(list_untouchable_third_party_directories(), [
        ]);
        $file_exceptions = array_merge(list_untouchable_third_party_files(), [
        ]);

        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $dir_exceptions) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $file_exceptions)) {
                continue;
            }

            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

            foreach ($this->regex_to_check as $regex => $message) {
                // Filename
                $counts = preg_match_all($regex, basename($path));
                $this->assertTrue(($counts == 0), $path . ' (file name): ' . $message);

                // File contents
                $counts = preg_match_all($regex, $c);
                $this->assertTrue(($counts == 0), $path . ' (file contents): ' . $message . ' (Found ' . integer_format($counts) . ')');
            }

            unset($c);
        }
    }
}
