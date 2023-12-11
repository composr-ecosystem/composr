<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

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
        $urls = $GLOBALS['SITE_DB']->query_select('staff_links', array('link'));
        foreach ($urls as $url) {
            $this->checkLink($url['link']);
        }
    }

    public function testStaffChecklist()
    {
        $tempcode = do_block('main_staff_checklist');
        $this->scanHTML($tempcode->evaluate());
    }

    public function testTutorials()
    {
        set_option('is_on_comcode_page_cache', '1');

        if (function_exists('set_time_limit')) {
            @set_time_limit(10000);
        }

        $path = get_file_base() . '/docs/pages/comcode_custom/' . fallback_lang();
        $files = get_directory_contents($path, $path);
        foreach ($files as $file) {
            if (substr($file, -4) == '.txt') {
                $tempcode = request_page(basename($file, '.txt'), true, 'docs');
                $this->scanHTML($tempcode->evaluate());
            }
        }
    }

    public function testTutorialDatabase()
    {
        $urls = $GLOBALS['SITE_DB']->query_select('tutorials_external', array('t_url'));
        foreach ($urls as $url) {
            $this->checkLink($url['t_url']);
        }
    }

    public function testFeatureTray()
    {
        $tempcode = do_block('composr_homesite_featuretray');
        $this->scanHTML($tempcode->evaluate());
    }

    public function testLangFiles()
    {
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

                $this->scanHTML($value);
            }
        }
    }

    public function testTemplates()
    {
        foreach (array('templates', 'templates_custom') as $subdir) {
            $path = get_file_base() . '/themes/default/' . $subdir;
            $files = get_directory_contents($path);
            foreach ($files as $file) {
                if (substr($file, -4) == '.tpl') {
                    $contents = file_get_contents($path . '/' . $file);
                    $this->scanHTML($contents);
                }
            }
        }
    }

    protected function scanHTML($html)
    {
        $matches = array();
        $num_matches = preg_match_all('#\shref=["\']([^"\']+)["\']#', $html, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $this->checkLink(html_entity_decode($matches[1][$i], ENT_QUOTES, get_charset()));
        }
    }

    protected function checkLink($url)
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
        if (preg_match('#^https://digg\.com/submit\?phase=2&url=#', $url) != 0) {
            return;
        }
        if (preg_match('#^https://december.com/html/4/element/#', $url) != 0) {
            return;
        }
        if (preg_match('#^https://shareddemo.composr.info/#', $url) != 0) {
            return;
        }
        if (preg_match('#^https://compo.sr/docs10/#', $url) != 0) {
            return;
        }
        if (in_array($url, array('https://cloud.google.com/console', 'https://console.cloud.google.com', 'https://console.developers.google.com/project', 'https://console.cloud.google.com/cloud-resource-manager', 'https://itouchmap.com/latlong.html', 'https://www.techsmith.com/jing-tool.html'))) {
            return;
        }

        // These URLs have a CAPTCHA which prevents check_url_exists from working
        if (in_array($url, array('https://stackoverflow.com/search?q=cms'))) {
            return;
        }

        $exists = check_url_exists($url, 60 * 60 * 24 * 100);
        if (!$exists) {
            $exists = check_url_exists($url, 0); // Re-try without caching, maybe we fixed a scanner bug or it's erratic
        }

        $this->assertTrue($exists, 'Broken URL: ' . str_replace('%', '%%', $url));
    }
}
