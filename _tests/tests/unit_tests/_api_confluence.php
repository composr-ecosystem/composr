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

/**
 * Composr test case class (unit testing).
 */
class _api_confluence_test_set extends cms_test_case
{
    protected $backup_url_scheme;

    public function setUp()
    {
        parent::setUp();

        $this->backup_url_scheme = get_option('url_scheme');

        set_option('confluence_subdomain', 'confluence.atlassian.com');
        set_option('confluence_space', 'Cloud');
        set_option('url_scheme', 'HTM');

        $this->load_key_options('confluence');
    }

    public function testCloudinaryTransfer()
    {
        if (!addon_installed('confluence')) {
            $this->assertTrue(false, 'The confluence addon must be installed for this test to run');
            return;
        }

        $this->run_health_check('API connections', 'Confluence');
    }

    public function tearDown()
    {
        set_option('url_scheme', $this->backup_url_scheme);
    }
}
