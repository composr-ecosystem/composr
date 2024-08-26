<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_health_check_gitlab extends Hook_Health_Check
{
    protected $category_label = 'API connections';

    /**
     * Standard hook run function to run this category of health checks.
     *
     * @param  ?array $sections_to_run Which check sections to run (null: all)
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @param  ?array $urls_or_page_links List of URLs and/or page-links to operate on, if applicable (null: those configured)
     * @param  ?array $comcode_segments Map of field names to Comcode segments to operate on, if applicable (null: N/A)
     * @param  boolean $show_unusable_categories Whether to include categories that might not be accessible for some reason
     * @return array A pair: category label, list of results
     */
    public function run(?array $sections_to_run, int $check_context, bool $manual_checks = false, bool $automatic_repair = false, ?bool $use_test_data_for_pass = null, ?array $urls_or_page_links = null, ?array $comcode_segments = null, bool $show_unusable_categories = false) : array
    {
        if (($show_unusable_categories) || (($check_context != CHECK_CONTEXT__INSTALL) && (addon_installed('cms_homesite')))) {
            if (($show_unusable_categories) || (file_exists(get_file_base() . '/data_custom/keys/gitlab.ini'))) {
                $this->process_checks_section('testGitlabConnection', 'GitLab', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass, $urls_or_page_links, $comcode_segments);
            }
        }

        return [$this->category_label, $this->results];
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @param  ?array $urls_or_page_links List of URLs and/or page-links to operate on, if applicable (null: those configured)
     * @param  ?array $comcode_segments Map of field names to Comcode segments to operate on, if applicable (null: N/A)
     */
    public function testGitlabConnection(int $check_context, bool $manual_checks = false, bool $automatic_repair = false, ?bool $use_test_data_for_pass = null, ?array $urls_or_page_links = null, ?array $comcode_segments = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            $this->log('Skipped; we are running from installer.');
            return;
        }
        if ($check_context == CHECK_CONTEXT__SPECIFIC_PAGE_LINKS) {
            $this->log('Skipped; running on specific page links.');
            return;
        }

        require_code('files');
        require_code('http');

        try {
            $gitlab_info = cms_parse_ini_file_fast(get_file_base() . '/data_custom/keys/gitlab.ini');
            list($gitlab_response) = cache_and_carry('cms_http_request', [
                'https://gitlab.com/api/v4/projects/' . $gitlab_info['project_id'] . '/repository/branches',
                [
                    'convert_to_internal_encoding' => true,
                    'timeout' => 5.0,
                    'extra_headers' => [
                        'Authorization' => 'Bearer ' . $gitlab_info['api_token']
                    ]
                ]
            ], 60);
            $_branches = @json_decode($gitlab_response, true);
            if (!is_array($_branches)) {
                $this->assertTrue(false, 'GitLab error: Could not retrieve repository branches. Are the credentials valid?');
                return;
            }
            $_branches = collapse_1d_complexity('name', $_branches);
            $this->assertTrue((count($_branches) > 0), 'GitLab error: No repository branches were returned.');
        } catch (Exception $e) {
            $this->assertTrue(false, 'GitLab error: Could not retrieve repository branches. Are the credentials valid?');
        }
    }
}
