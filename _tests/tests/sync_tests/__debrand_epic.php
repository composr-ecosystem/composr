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
 */

/**
 * Composr test case class (unit testing).
 */
class __debrand_epic_test_set extends cms_test_case
{
    protected $regex_to_check = [
        // TODO: This is not actually effective as it will not match a "composr" at the very end of a string; find a better way to do this.
        '/composr[^\.]/i' => 'Detected hard-coded use of branded term \'Composr\'; consider using brand_name(), cms, or a generic term such as \'the software\'.',

        '/compo\.sr/i' => 'Detected hard-coded use of branded website \'compo.sr\'; consider using get_brand_base_url().', // LEGACY
        '/composr\.app/i' => 'Detected hard-coded use of branded website \'composr.app\'; consider using get_brand_base_url().',
    ];
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);

        require_code('files');
        require_code('files2');
    }

    public function testScripts()
    {
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_CUSTOM_DIRS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);

        $dir_exceptions = array_merge(list_untouchable_third_party_directories(), [
            'mobiquo', // Ignore Tapatalk because if we use a generic context for Composr here, developers will get it confused with Tapatalk (plus it's non-bundled)
            '_tests', // The test suite should not be subject to debranding
        ]);
        $file_exceptions = array_merge(list_untouchable_third_party_files(), [
            'adminzone/pages/modules/admin_debrand.php', // Hard-coded values specified in replacement calls
            'adminzone/pages/modules/admin_version.php', // Can specifically reference active Composr developers
            'sources/blocks/main_staff_checklist.php', // Contains software-specific checklist items when brand name is Composr
            'sources/upgrade_db_upgrade.php', // Contains legacy upgrade code

            // TODO: temporary exclusions
            'code_editor.php',
            'sources/critical_errors.php',
        ]);
        $regex_exceptions = [
            '/composr[^\.]/i' => [
                // Composr is allowed as an organisation for addons
                '/\$info\[\'organisation\'\] = \'Composr\';/i',
                '/\$organisation = \$is_orig \? \'Composr\' : \$defaults\[\'organisation\'\];/i',
                '/\$organisation = \'Composr\';/i',

                // Composr XML entity; too risky to rename as this deals with the XML db
                '/' . preg_quote('$bits[$i] != \'composr\'', '/') . '/i',
                '/\<composr\>/',
                '/\<\/composr\>/',
                '/' . preg_quote('// Skip past "composr"', '/') . '/i',

                '/define\(\'DEFAULT_BRAND_NAME\'\, \'Composr\'\);/i', // Defining default brand
                '/\s*composr[\r\n]\s*copyright \(c\)/i', // Acceptable to have Composr in the copyright comments
                // '/data\/ace\/ace_composr\.js/i', // Ignore references to Ace Composr (actually already ignored by the no-dot assertion)
                '/aceComposrLoader/i', // Ignore references to Ace Composr
                '/composr_failover_test/', // User agent; we allow this
                '/brand_name\(\) [=|!]= \'Composr\'/i', // Checking if the brand is set to Composr
                // '/servers\/composr\.info\//i', // LEGACY: Usually used as a condition against demonstratr (actually already ignored by the no-dot assertion)
                '/' . preg_quote('PRODID:-//Christopher Graham/Composr//NONSGML v1.0//EN', '/') . '/i', // ical
                '/' . preg_quote('\'previously_in_addon\' => [', '/') . '[^\]*]composr[^\]*]\]/i', // Renamed addons

                // TODO: temporary exclusions
                '/cms_homesite/', // Would be too complicated to rename / debrand at this time (same for other homesite addons)
                '/composr_mobile_sdk/', // Would be too complicated to rename / debrand at this time
                '/X\-Powered\-By: Composr/i', // TODO: Should we explicitly leave this as Composr to indicate what even rebranded installs are running or were based off?
                '/You may not distribute a modified version of this file\, unless it is solely as a Composr modification/', // homesite copyright (should this be modified?)

                // GitLab; must be defined twice since it contains two instances of composr
                '/' . preg_quote('https://gitlab.com/composr-foundation/composr', '/') . '/i',
                '/' . preg_quote('https://gitlab.com/composr-foundation/composr', '/') . '/i',
            ],

            '/composr\.app/i' => [
                '/' . preg_quote('define(\'DEFAULT_BRAND_URL\', \'https://composr.app\');', '/') . '/i', // Defining default brand URL
                '/website_specific\/composr\.app\//', // TODO: #5720
                '/' . preg_quote('https://composr.app/tracker/view.php?id=3470', '/') . '/', // Tracker issue in a comment
            ],
        ];

        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $dir_exceptions) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $file_exceptions)) {
                continue;
            }

            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

            // TODO: temporary ignore; up for discussion if we want these entry point files rebranded as well
            if (preg_match_all('/composr startup error/i', $c)) {
                continue;
            }

            foreach ($this->regex_to_check as $regex => $message) {
                // Filename
                $counts = preg_match_all($regex, basename($path));
                $this->assertTrue(($counts == 0), $path . ' (file name): ' . $message);

                // File contents
                $counts = preg_match_all($regex, $c);
                if (isset($regex_exceptions[$regex])) {
                    foreach ($regex_exceptions[$regex] as $regex_exception) {
                        $counts -= preg_match_all($regex_exception, $c);
                    }
                }
                $this->assertTrue(($counts == 0), $path . ' (file contents): ' . $message . ' (Found ' . integer_format($counts) . ' excluding exceptions)');
            }

            unset($c);
        }
    }

    public function testLangAndPages()
    {
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_CUSTOM_DIRS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['ini', 'txt']);

        $dir_exceptions = array_merge(list_untouchable_third_party_directories(), [
            'docs' // It is acceptable for tutorial pages to contain Composr branding
        ]);
        $file_exceptions = array_merge(list_untouchable_third_party_files(), [
        ]);
        $regex_exceptions = [
            '/composr[^\.]/i' => [
                // TODO: temporary exclusions
                '/cms_homesite/', // Would be too complicated to rename / debrand at this time (same for other homesite addons)
            ],

            '/composr\.app/i' => [
            ],
        ];

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
                if (isset($regex_exceptions[$regex])) {
                    foreach ($regex_exceptions[$regex] as $regex_exception) {
                        $counts -= preg_match_all($regex_exception, $c);
                    }
                }
                $this->assertTrue(($counts == 0), $path . ' (file contents): ' . $message . ' (Found ' . integer_format($counts) . ')');
            }

            unset($c);
        }
    }

    public function testJsAndCss()
    {
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_CUSTOM_DIRS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['js', 'css']);

        $dir_exceptions = array_merge(list_untouchable_third_party_directories(), [
        ]);
        $file_exceptions = array_merge(list_untouchable_third_party_files(), [
            'themes/default/javascript/installer.js', // TODO: Google App Engine code
            'themes/default/javascript/_cms_views.js', // Contains IRC info
        ]);
        $regex_exceptions = [
            '/composr[^\.]/i' => [
                // TODO: temporary exclusions
                '/cms_homesite/', // Would be too complicated to rename / debrand at this time (same for other homesite addons)
                '/aceComposrLoader/',
            ],

            '/composr\.app/i' => [
            ],
        ];

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
                if (isset($regex_exceptions[$regex])) {
                    foreach ($regex_exceptions[$regex] as $regex_exception) {
                        $counts -= preg_match_all($regex_exception, $c);
                    }
                }
                $this->assertTrue(($counts == 0), $path . ' (file contents): ' . $message . ' (Found ' . integer_format($counts) . ')');
            }

            unset($c);
        }
    }

    public function testTemplates()
    {
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_CUSTOM_DIRS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['tpl']);

        $dir_exceptions = array_merge(list_untouchable_third_party_directories(), [
        ]);
        $file_exceptions = array_merge(list_untouchable_third_party_files(), [
        ]);
        $regex_exceptions = [
            '/composr[^\.]/i' => [
                // TODO: temporary exclusions
                '/cms_homesite/', // Would be too complicated to rename / debrand at this time (same for other homesite addons)
            ],

            '/composr\.app/i' => [
            ],
        ];

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
                if (isset($regex_exceptions[$regex])) {
                    foreach ($regex_exceptions[$regex] as $regex_exception) {
                        $counts -= preg_match_all($regex_exception, $c);
                    }
                }
                $this->assertTrue(($counts == 0), $path . ' (file contents): ' . $message . ' (Found ' . integer_format($counts) . ')');
            }

            unset($c);
        }
    }
}
