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
class sitemap_submit_test_set extends cms_test_case
{
    public function testSitemapSubmit()
    {
        require_code('config2');
        set_option('site_closed', '0');
        set_option('auto_submit_sitemap', '1');
        require_code('sitemap_xml');
        ping_sitemap_xml('https://compo.sr/data_custom/sitemaps/index.xml', true);
    }
}
