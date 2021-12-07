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
class xml_sitemaps_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('sitemap_xml');
        require_code('news2');
    }

    public function testSitemapAdd()
    {
        $id = add_news_category('Test' . uniqid('', true));
        $page_link = get_module_zone('news') . ':news:browse:' . strval($id);
        $last_updated = $GLOBALS['SITE_DB']->query_select_value('sitemap_cache', 'last_updated', array('page_link' => $page_link));
        $this->assertTrue($last_updated >= time() - 3);
    }

    public function testSitemapBuild()
    {
        sitemap_xml_build();

        $this->assertTrue(is_file(get_file_base() . '/data_custom/sitemaps/index.xml'));
    }

    public function testSitemapValidate()
    {
        $files = array('index.xml', 'set_0.xml');
        foreach ($files as $file) {
            $c = file_get_contents(get_file_base() . '/data_custom/sitemaps/' . $file);

            // Simple XML validation
            require_code('xml');
            $ob = new CMS_simple_xml_reader($c);

            /* Bots apparently being blocked on here now
            $url = 'https://ipullrank.com/tools/map-broker/index.php';
            $post_params = array(
                'option' => '1',
                'page' => 'go',
            );

            $tmp_file = get_file_base() . '/temp.xml';
            $cleaned_c = preg_replace('#https?://[^<>"]*#', 'http://example.com/', $c); // It checks URLs, which we don't want
            $cleaned_c = preg_replace('#(?U)(</url>.*</url>)(?-U).*</url>#s', '$1', $cleaned_c); // Strip down to speed up. Have 2 URLs else the validator is buggy
            file_put_contents($tmp_file, $cleaned_c);

            $files = array(
                'xmlfile' => $tmp_file,
            );
            $result = http_download_file($url, null, true, false, 'Composr', $post_params, null, null, null, null, null, null, null, 6.0, false, $files);

            unlink($tmp_file);

            $this->assertTrue(strpos($result, '<strong>100%</strong> valid sitemap'), $result);
            */
        }
    }
}
