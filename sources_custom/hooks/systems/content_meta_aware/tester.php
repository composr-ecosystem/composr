<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    tester
 */

/**
 * Hook class.
 */
class Hook_content_meta_aware_tester
{
    /**
     * Get content type details. Provides information to allow task reporting, randomisation, and add-screen linking, to function.
     *
     * @param  ?ID_TEXT $zone The zone to link through to (null: autodetect).
     * @param  boolean $get_extended_data Populate additional data that is somewhat costly to compute (add_url, archive_url).
     * @return ?array Map of award content-type info (null: disabled).
     */
    public function info($zone = null, $get_extended_data = false)
    {
        return array(
            'support_custom_fields' => false,

            'content_type_label' => 'tester:TEST_SECTION',
            'content_type_universal_label' => 'Test section',

            'connection' => $GLOBALS['SITE_DB'],
            'table' => 'tests',
            'id_field' => 'id',
            'id_field_numeric' => true,
            'parent_category_field' => 't_section',
            'parent_category_meta_aware_type' => null,
            'is_category' => false,
            'is_entry' => true,
            'category_field' => null, // For category permissions
            'category_type' => null, // For category permissions
            'parent_spec__table_name' => null,
            'parent_spec__parent_name' => null,
            'parent_spec__field_name' => null,
            'category_is_string' => false,

            'title_field' => null,
            'title_field_dereference' => false,
            'description_field' => null,
            'thumb_field' => null,
            'thumb_field_is_theme_image' => false,
            'alternate_icon_theme_image' => null,

            'view_page_link_pattern' => '_SEARCH:tester:report:_WILD',
            'edit_page_link_pattern' => '_SEARCH:tester:_edit:_WILD',
            'view_category_page_link_pattern' => null,
            'add_url' => ($get_extended_data && function_exists('has_submit_permission') && has_submit_permission('mid', get_member(), get_ip_address(), 'tester')) ? (get_module_zone('tester') . ':tester:add') : null,
            'archive_url' => $get_extended_data ? (((!is_null($zone)) ? $zone : get_module_zone('tester')) . ':tester') : null,

            'support_url_monikers' => false,

            'views_field' => null,
            'order_field' => null,
            'submitter_field' => 't_assigned_to',
            'author_field' => null,
            'add_time_field' => null,
            'edit_time_field' => null,
            'date_field' => null,
            'validated_field' => null,

            'seo_type_code' => null,

            'feedback_type_code' => 'bug_report',

            'permissions_type_code' => null, // null if has no permissions

            'search_hook' => null,
            'rss_hook' => null,
            'attachment_hook' => null,
            'unvalidated_hook' => null,
            'notification_hook' => null,
            'sitemap_hook' => null,

            'addon_name' => 'tester',

            'cms_page' => 'cms_chat',
            'module' => 'tester',

            'commandr_filesystem_hook' => null,
            'commandr_filesystem__is_folder' => false,

            'support_revisions' => false,

            'support_privacy' => false,

            'support_content_reviews' => false,

            'actionlog_regexp' => '\w+_TEST',
        );
    }
}
