<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cloudinary
 */

/**
 * Hook class.
 */
class Hook_health_check_cloudinary extends Hook_Health_Check
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
    public function run($sections_to_run, $check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null, $urls_or_page_links = null, $comcode_segments = null, $show_unusable_categories = false)
    {
        if (($show_unusable_categories) || (($check_context != CHECK_CONTEXT__INSTALL) && (addon_installed('cloudinary')))) {
            if (($show_unusable_categories) || (get_option('openweathermap_api_key') != '')) {
                $this->process_checks_section('testCloudinaryConnection', 'Cloudinary', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass, $urls_or_page_links, $comcode_segments);
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
    public function testCloudinaryConnection($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null, $urls_or_page_links = null, $comcode_segments = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }
        if ($check_context == CHECK_CONTEXT__SPECIFIC_PAGE_LINKS) {
            return;
        }

        $before = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');

        $cloud_name = get_option('cloudinary_cloud_name');
        $api_key = get_option('cloudinary_api_key');
        $api_secret = get_option('cloudinary_api_secret');

        require_code('Cloudinary/autoload');

        \Cloudinary::config([
            'cloud_name' => $cloud_name,
            'api_key' => $api_key,
            'api_secret' => $api_secret,
        ]);

        $ob = new \Cloudinary\Search();
        try {
            $result = $ob->expression('format:jpg')->execute();
            $error = '';
        } catch (Exception $e) {
            $result = null;
            $error = $e->getMessage();
        }

        $this->assertTrue(($result !== null) && (is_integer($result['total_count'])), 'Could not get Cloudinary result: ' . $error);

        cms_ini_set('ocproducts.type_strictness', $before);
    }
}
