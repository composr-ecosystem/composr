<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// php _tests/index.php __broken_links

/**
 * Composr test case class (unit testing).
 */
class __broken_links_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('files2');
        require_code('site');
        require_code('global4');

        disable_php_memory_limit();
    }

    public function testStaffLinks()
    {
        if ($this->only !== null) {
            return;
        }

        $urls = $GLOBALS['SITE_DB']->query_select('staff_links', ['link']);
        foreach ($urls as $url) {
            $this->record_link($url['link'], 'staff_links');
        }

        $this->check_recorded_links();
    }

    public function testStaffChecklist()
    {
        if ($this->only !== null) {
            return;
        }

        $tempcode = do_block('main_staff_checklist');
        $this->scan_html($tempcode->evaluate(), 'main_staff_checklist');

        $this->check_recorded_links();
    }

    public function testAddonDescriptions()
    {
        $addons = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($addons as $addon_name => $ob) {
            if (($this->only !== null) && ($this->only != $addon_name)) {
                continue;
            }

            if (method_exists($ob, 'get_description')) {
                $description = $ob->get_description();
                $tempcode = comcode_to_tempcode($description);

                $this->scan_html($tempcode->evaluate(), $addon_name);
            }
        }
    }

    public function testTutorials()
    {
        set_option('is_on_comcode_page_cache', '1');

        $path = get_file_base() . '/docs/pages/comcode_custom/' . fallback_lang();
        $files = get_directory_contents($path, $path, 0, true, true, ['txt']);
        foreach ($files as $file) {
            $tutorial = basename($file, '.txt');

            if ($tutorial == 'tut_addon_index') {
                continue; // May be outdated
            }

            if (($this->only !== null) && ($this->only != $tutorial)) {
                continue;
            }

            $tempcode = request_page($tutorial, true, 'docs');
            $this->scan_html($tempcode->evaluate(), $tutorial);
        }

        $this->check_recorded_links();
    }

    public function testTutorialDatabase()
    {
        if ($this->only !== null) {
            return;
        }

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        $urls = $GLOBALS['SITE_DB']->query_select('tutorials_external', ['t_url']);
        foreach ($urls as $url) {
            $this->record_link($url['t_url'], 'tutorials_external');
        }

        $this->check_recorded_links();
    }

    public function testFeatureTray()
    {
        if ($this->only !== null) {
            return;
        }

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        $tempcode = do_block('composr_homesite_featuretray');
        $this->scan_html($tempcode->evaluate(), 'composr_homesite_featuretray');

        $this->check_recorded_links();
    }

    public function testLangFiles()
    {
        if ($this->only !== null) {
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

        $this->check_recorded_links();
    }

    public function testTemplates()
    {
        if ($this->only !== null) {
            return;
        }

        foreach (['templates', 'templates_custom'] as $subdir) {
            $path = get_file_base() . '/themes/default/' . $subdir;
            $files = get_directory_contents($path, '', 0, true, true, ['tpl']);
            foreach ($files as $file) {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
                $this->scan_html($c, $file);
            }
        }

        $this->check_recorded_links();
    }

    protected function scan_html($html, $context)
    {
        $matches = [];
        $num_matches = preg_match_all('#\shref=["\']([^"\']+)["\']#', $html, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $this->record_link(html_entity_decode($matches[1][$i], ENT_QUOTES), $context);
        }
    }

    protected $recorded_links = [];

    protected function record_link($url, $context)
    {
        $this->recorded_links[] = [$url, $context];
    }

    protected function check_recorded_links()
    {
        shuffle($this->recorded_links); // This allows running multiple instances of the test in parallel

        foreach ($this->recorded_links as $recorded_link) {
            list($url, $context) = $recorded_link;
            $this->check_link($url, $context);
        }
        $this->recorded_links = [];
    }

    protected function check_link($url, $context)
    {
        cms_extend_time_limit(2);

        /*// This is just for temporary testing of a list of broken links
        if (!in_array($url, [
        ])) {
            return;
        }*/

        $docs_stub =  get_brand_base_url() . '/docs' . strval(cms_version()) . '/';
        if ((substr($url, 0, strlen($docs_stub)) == $docs_stub) && (cms_version_branch_status() == VERSION_ALPHA)) {
            return;
        }

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

        if (preg_match('#^http://sns.qzone.qq.com/#', $url) != 0) {
            return;
        }

        if (preg_match('#^http://december\.com/html/4/element/#', $url) != 0) {
            return;
        }
        if (preg_match('#^https?://shareddemo\.composr\.info/#', $url) != 0) {
            return;
        }
        if (preg_match('#^https://www\.linkedin\.com/shareArticle\?url=#', $url) != 0) {
            return;
        }
        if (preg_match('#^http://tumblr\.com/widgets/share/tool\?canonicalUrl=#', $url) != 0) {
            return;
        }
        if (preg_match('#^https://vk\.com/share\.php\?url=#', $url) != 0) {
            return;
        }
        if (preg_match('#^http://v\.t\.qq\.com/share/share\.php\?url=#', $url) != 0) {
            return;
        }
        if (preg_match('#^http://service\.weibo\.com/share/share\.php\?url=#', $url) != 0) {
            return;
        }
        if (preg_match('#^https://twitter\.com/intent/tweet\?text=#', $url) != 0) {
            return;
        }
        if (preg_match('#^https://compo\.sr/uploads/website_specific/compo\.sr/scripts/build_personal_upgrader\.php#', $url) != 0) {
            return;
        }
        if (in_array($url, [
            // These just won't check from a bot guest user
            'https://cloud.google.com/console',
            'https://www.google.com/webmasters/tools/home',
            'https://console.developers.google.com/project',
            'https://developer.twitter.com/en/apps',
            'http://dev.twitter.com/apps/new',
            'https://compo.sr/themeing-changes.htm',
            'https://pixabay.com/',
            'https://www.patreon.com/posts/18644315',
            'https://foundation.mozilla.org/en/insights/internet-health-report/',
            'https://business.adobe.com/products/magento/magento-commerce.html',
            'https://www.adobe.com/acrobat/pdf-reader.html',
            'https://www.adobe.com/sign.html',
            'https://www.upwork.com/',

            // Invalid but not actually used
            'http://qbnz.com/highlighter/',

            // In non-maintained functionality
            'http://php-minishell.appspot.com/',
        ])) {
            return;
        }
        if (in_array($url, [ // cURL doesn't like the SSL config / just times out connecting
            'https://www.hobo-web.co.uk/website-design-tips/',
            'https://www.microsoft.com/en-us/download/details.aspx?id=48264',
            'https://www.transifex.com/ocproducts/',
            'https://www.transifex.com/organization/ocproducts/dashboard',
            'https://www.paypal.com/us/webapps/helpcenter/helphub/article/?solutionId=FAQ2347',
            'http://stackoverflow.com/search?q=cms',
            'http://www.projecthoneypot.org/add_honey_pot.php',
            'http://www.projecthoneypot.org/httpbl_configure.php',
        ])) {
            return;
        }

        require_code('urls2');
        $message = '';
        $exists = check_url_exists($url, null, true, 3, $message);
        $this->assertTrue($exists, 'Broken URL: ' . $url . ' (' . $message . ') in ' . $context);
    }
}
