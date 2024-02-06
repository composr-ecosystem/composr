<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class __specsettings_documented_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);
        disable_php_memory_limit();

        require_code('files2');
    }

    public function testDirectives()
    {
        $directives_file = cms_file_get_contents_safe(get_file_base() . '/sources/symbols.php', FILE_READ_LOCK);

        $tempcode_tutorial = cms_file_get_contents_safe(get_file_base() . '/docs/pages/comcode_custom/EN/tut_tempcode.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

        $matches = [];
        $num_matches = preg_match_all('#^            case \'([A-Z_]+)\':#m', $directives_file, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $directive = $matches[1][$i];

            $this->assertTrue(strpos($tempcode_tutorial, $directive) !== false, 'Missing documented directive, ' . $directive);
        }
    }

    public function testSymbols()
    {
        $symbols_file = cms_file_get_contents_safe(get_file_base() . '/sources/symbols.php') . cms_file_get_contents_safe(get_file_base() . '/sources/symbols2.php', FILE_READ_LOCK);

        $tempcode_tutorial = cms_file_get_contents_safe(get_file_base() . '/docs/pages/comcode_custom/EN/tut_tempcode.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

        $matches = [];
        $num_matches = preg_match_all('#^function ecv2?_(\w+)\(\$lang, \$escaped, \$param\)#m', $symbols_file, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $symbol = $matches[1][$i];

            if (in_array($symbol, ['TERNARY'])) {
                continue;
            }

            $this->assertTrue(strpos($tempcode_tutorial, '{$' . $symbol) !== false, 'Missing documented symbol, {$' . $symbol . '}');
        }
    }

    public function testSymbolsReverse()
    {
        require_code('symbols2');

        $tempcode_tutorial = cms_file_get_contents_safe(get_file_base() . '/docs/pages/comcode_custom/EN/tut_tempcode.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

        $matches = [];
        $num_matches = preg_match_all('#\{\$(\w+)#', $tempcode_tutorial, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $symbol = $matches[1][$i];

            if (in_array($symbol, ['SYMBOL', 'F'])) {
                continue;
            }

            $this->assertTrue(function_exists('ecv_' . $symbol) || function_exists('ecv2_' . $symbol) || is_file(get_file_base() . '/sources/hooks/systems/symbols/' . $symbol . '.php'), 'Missing documented symbol, ' . $symbol);
        }
    }

    public function testInstallOptions()
    {
        $config_editor_code = cms_file_get_contents_safe(get_file_base() . '/config_editor.php', FILE_READ_LOCK);

        $found = [];

        $all_code = '';

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        foreach ($files as $path) {
            if (basename($path) == 'shared_installs.php') {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $all_code .= $c;
        }

        $matches = [];
        $num_matches = preg_match_all('#(\$SITE_INFO|\$GLOBALS\[\'SITE_INFO\'\])\[\'([^\'"]+)\'\]#', $all_code, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $var = $matches[2][$i];
            if (
                (/*Can't just flip so simply*/$var != 'multi_lang_content') &&
                (/*string replace array*/$var != 'reps') &&
                (/*AFM*/strpos($var, 'ftp_') === false) &&
                (/*Testing Platform*/!in_array($var, ['ci_password', 'gitlab_personal_token'])) &&
                (/*Demonstratr*/strpos($var, 'throttle_') === false) &&
                (/*Demonstratr*/strpos($var, 'custom_') === false) &&
                (/*Demonstratr*/$var != 'mysql_demonstratr_password') &&
                (/*Demonstratr*/$var != 'mysql_root_password') &&
                (/*Custom domains*/strpos($var, 'ZONE_MAPPING') === false) &&
                (/*Legacy password name*/$var != 'admin_password') &&
                (/*XML dev environment*/strpos($var, '_chain') === false) &&
                (/*LEGACY*/$var != 'board_prefix') &&
                (/*forum-driver-specific*/!in_array($var, ['aef_table_prefix', 'bb_forum_number', 'ipb_table_prefix', 'mybb_table_prefix', 'phpbb_table_prefix', 'smf_table_prefix', 'stronghold_cookies', 'vb_table_prefix', 'vb_unique_id', 'vb_version', 'wowbb_table_prefix']))
            ) {
                $found[$var] = true;
            }
        }

        $found = array_keys($found);
        sort($found);

        foreach ($found as $var) {
            $this->assertTrue(strpos($config_editor_code, '\'' . $var . '\' => \'') !== false, 'Missing config_editor UI for ' . $var);
        }

        // Test the reverse too...

        $matches = [];
        $num_matches = preg_match_all('#^        \'(\w+)\' => \'#m', $config_editor_code, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $install_option = $matches[1][$i];

            if (in_array($install_option, ['gae_application', 'gae_bucket_name'])) {
                continue;
            }

            $have_found = (strpos($all_code, '$GLOBALS[\'SITE_INFO\'][\'' . $install_option . '\']') !== false) || (strpos($all_code, '$SITE_INFO[\'' . $install_option . '\']') !== false);
            $this->assertTrue($have_found, 'Documented install option not used (' . $install_option . ')');
        }

        // Test blanking out won't cause a crash...

        $config = '<' . '?php' . "\n" . 'global $SITE_INFO;' . "\n";
        foreach ($found as $key) {
            if (in_array($key, ['dev_mode'])) { // Exceptions
                continue;
            }

            $config .= '$SITE_INFO[\'' . $key . '\'] = \'\';' . "\n";
        }
        $config .= '?' . '>';
        $old_config = cms_file_get_contents_safe(get_file_base() . '/_config.php', FILE_READ_LOCK);
        $config .= $old_config;
        file_put_contents(get_file_base() . '/_config.php', $config);
        $this->assertTrue(is_string(http_get_contents(get_base_url() . '/index.php', ['trigger_error' => false])));
        file_put_contents(get_file_base() . '/_config.php', $old_config);
        fix_permissions(get_file_base() . '/_config.php');
        sync_file(get_file_base() . '/_config.php');
    }

    public function testValueOptions()
    {
        $codebook_text = cms_file_get_contents_safe(get_file_base() . '/docs/pages/comcode_custom/EN/codebook_3.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

        $found = [];

        $all_code = '';

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_NONBUNDLED | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['php', 'tpl', 'js']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            if (($path == 'sources/upgrade.php') || ($path == 'sources/shared_installs.php') || ($path == 'sources_custom/phpstub.php')) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $all_code .= $c;
        }

        $regexps = [
            '#get\_value\(\'([^\']+)\'\)#',
            '#\{\$VALUE_OPTION[*;/\#]*,([^{},]+)\}#',
        ];
        foreach ($regexps as $regexp) {
            $matches = [];
            $num_matches = preg_match_all($regexp, $all_code, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $var = $matches[1][$i];

                // Exceptions
                if (in_array($var, [
                    /*LEGACY*/'ocf_version',
                    'user_peak',
                    'uses_ftp',
                    'version',
                    'cns_version',
                    'newsletter_whatsnew',
                    'newsletter_send_time',
                    'site_salt',
                    'sitemap_building_in_progress',
                    'setupwizard_completed',
                    'oracle_index_cleanup_last_time',
                    'timezone',
                    'users_online',
                    'user_peak_week',
                    'ran_once',
                    'commandr_watched_chatroom',
                    'trusted_sites_1',
                    'trusted_sites_2',
                    'smf_auth_secret',
                ])) {
                    continue;
                }
                if ((substr($var, 0, 5) == 'last_') || (substr($var, 0, 4) == 'ftp_') || (substr($var, 0, 8) == 'delurk__') || (substr($var, 0, 7) == 'backup_')) {
                    continue;
                }

                if (!file_exists(get_file_base() . '/sources/hooks/systems/disposable_values/' . $var . '.php')) {// Quite a few are set in code
                    $found[$var] = true;
                }
            }
        }

        $found = array_keys($found);
        sort($found);

        foreach ($found as $var) {
            $this->assertTrue(strpos($codebook_text, '[tt]' . $var . '[/tt]') !== false, 'Missing Code Book listing for hidden value, ' . $var);
        }

        // Test the reverse too...

        $matches = [];
        $num_matches = preg_match_all('#^  - \[tt\](\w+)\[/tt\] -- #m', $codebook_text, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $value_option = $matches[1][$i];

            if (in_array($value_option, ['webdav_root'])) {
                continue;
            }

            $have_found = (strpos($all_code, '\'' . $value_option . '\'') !== false) || (preg_match('#\{\$VALUE_OPTION[;\*]?,' . preg_quote($value_option, '#') . '#', $all_code) != 0);
            $this->assertTrue($have_found, 'Documented value option not used (' . $value_option . ')');
        }
    }

    public function testKeepSettings()
    {
        $codebook_text = cms_file_get_contents_safe(get_file_base() . '/docs/pages/comcode_custom/EN/codebook_3.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

        $found = [];

        $all_code = '';

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_NONBUNDLED | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['php', 'tpl', 'js']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            if ((basename($path) == 'shared_installs.php') || (strpos($path, 'sources/forum/') !== false) || (basename($path) == 'phpstub.php')) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $all_code .= $c;
        }

        $matches = [];
        $num_matches = preg_match_all('#get\_param(\_integer)?\(\'(keep_[^\']+)\'[,\)]#', $all_code, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $var = $matches[2][$i];
            $found[$var] = true;
        }
        $num_matches = preg_match_all('#\{\$_GET,(keep_\w+)]#', $all_code, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $var = $matches[2][$i];
            $found[$var] = true;
        }

        $found = array_keys($found);
        sort($found);

        foreach ($found as $var) {
            $this->assertTrue(strpos($codebook_text, '[tt]' . $var . '[/tt]') !== false, 'Missing Code Book listing for keep setting, ' . $var);
        }

        // Test the reverse too...

        $matches = [];
        $num_matches = preg_match_all('#^ - \[tt\](keep_\w+)\[/tt\]#m', $codebook_text, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $keep_setting = $matches[1][$i];

            $have_found = (strpos($all_code, '\'' . $keep_setting . '\'') !== false) || (strpos($all_code, '{$_GET,' . $keep_setting) !== false) || (strpos($all_code, 'searchParams.get(\'' . $keep_setting . '\')') !== false);
            $this->assertTrue($have_found, 'Documented keep setting not used (' . $keep_setting . ')');
        }
    }
}
