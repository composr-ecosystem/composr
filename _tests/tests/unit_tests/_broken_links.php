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

// php _tests/index.php _broken_links

/**
 * Composr test case class (unit testing).
 */
class _broken_links_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('files2');
        require_code('site');
        require_code('global4');
    }

    public function testStaffLinks()
    {
        if (get_param_string('only_tutorial', '') != '') {
            return;
        }

        $urls = $GLOBALS['SITE_DB']->query_select('staff_links', array('link'));
        foreach ($urls as $url) {
            $this->check_link($url['link'], 'staff_links');
        }
    }

    public function testStaffChecklist()
    {
        if (get_param_string('only_tutorial', '') != '') {
            return;
        }

        $tempcode = do_block('main_staff_checklist');
        $this->scan_html($tempcode->evaluate(), 'main_staff_checklist');
    }

    public function testTutorials()
    {
        set_option('is_on_comcode_page_cache', '1');

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(10000);
        }
        disable_php_memory_limit();

        $only_tutorial = get_param_string('only_tutorial', '');

        $path = get_file_base() . '/docs/pages/comcode_custom/' . fallback_lang();
        $files = get_directory_contents($path, $path, 0, true, true, array('txt'));
        foreach ($files as $file) {
            $tutorial = basename($file, '.txt');

            if (($only_tutorial != '') && ($only_tutorial != $tutorial)) {
                continue;
            }

            $tempcode = request_page($tutorial, true, 'docs');
            $this->scan_html($tempcode->evaluate(), $tutorial);
        }
    }

    public function testTutorialDatabase()
    {
        if (get_param_string('only_tutorial', '') != '') {
            return;
        }

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        $urls = $GLOBALS['SITE_DB']->query_select('tutorials_external', array('t_url'));
        foreach ($urls as $url) {
            $this->check_link($url['t_url'], 'tutorials_external');
        }
    }

    public function testFeatureTray()
    {
        if (get_param_string('only_tutorial', '') != '') {
            return;
        }

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        $tempcode = do_block('composr_homesite_featuretray');
        $this->scan_html($tempcode->evaluate(), 'composr_homesite_featuretray');
    }

    public function testLangFiles()
    {
        if (get_param_string('only_tutorial', '') != '') {
            return;
        }

        require_code('lang2');
        require_code('lang_compile');

        $lang_files = get_lang_files(fallback_lang());
        foreach (array_keys($lang_files) as $lang_file) {
            $map = get_lang_file_map(fallback_lang(), $lang_file, false, false) + get_lang_file_map(fallback_lang(), $lang_file, true, false);
            foreach ($map as $key => $value) {
                if (strpos($value, '[url') !== false) {
                    $tempcode = comcode_to_tempcode($value);
                    $value = $tempcode->evaluate();
                }

                $this->scan_html($value, $lang_file);
            }
        }
    }

    public function testTemplates()
    {
        if (get_param_string('only_tutorial', '') != '') {
            return;
        }

        foreach (array('templates', 'templates_custom') as $subdir) {
            $path = get_file_base() . '/themes/default/' . $subdir;
            $files = get_directory_contents($path, '', 0, true, true, array('tpl'));
            foreach ($files as $file) {
                $c = file_get_contents($path . '/' . $file);
                $this->scan_html($c, $file);
            }
        }
    }

    protected function scan_html($html, $context)
    {
        $matches = array();
        $num_matches = preg_match_all('#\shref=["\']([^"\']+)["\']#', $html, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $this->check_link(html_entity_decode($matches[1][$i], ENT_QUOTES), $context);
        }
    }

    protected function check_link($url, $context)
    {
        if (strpos($url, '{') !== false) {
            return;
        }
        if (strpos($url, '://') === false) {
            return;
        }
        if (substr($url, 0, strlen(get_base_url())) == get_base_url()) {
            return;
        }
        if (empty($url)) {
            return;
        }

        if (preg_match('#^https://www\.linkedin\.com/shareArticle\?url=', $url) != 0) {
            return;
        }
        if (preg_match('#^http://tumblr\.com/widgets/share/tool\?canonicalUrl=', $url) != 0) {
            return;
        }
        if (preg_match('#^https://vk\.com/share\.php\?url=', $url) != 0) {
            return;
        }
        if (preg_match('#^http://v\.t\.qq\.com/share/share\.php\?url=', $url) != 0) {
            return;
        }

        if (preg_match('#^http://december\.com/html/4/element/#', $url) != 0) {
            return;
        }
        if (preg_match('#^http://shareddemo\.composr\.info/#', $url) != 0) {
            return;
        }
        if (preg_match('#^http://compo\.sr/docs\d+/#', $url) != 0) {
            return;
        }
        if (in_array($url, array( // These just won't check from a bot guest user
            'https://cloud.google.com/console',
            'https://www.google.com/webmasters/tools/home',
            'https://console.developers.google.com/project',
            'https://itouchmap.com/latlong.html',
            'https://www.techsmith.com/jing-tool.html',
        ))) {
            return;
        }

        $exists = check_url_exists($url, 60 * 60 * 24 * 100);
        if (!$exists) {
            $exists = check_url_exists($url, 0); // Re-try without caching, maybe we fixed a scanner bug or it's erratic
        }
        $this->assertTrue($exists, 'Broken URL: ' . str_replace('%', '%%', $url) . ' in ' . $context);
    }
}
