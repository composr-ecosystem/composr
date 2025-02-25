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
class Hook_resource_meta_aware_tracker_issue extends Hook_CMA
{
    /**
     * Get content type details.
     *
     * @param  ?ID_TEXT $zone The zone to link through to (null: autodetect)
     * @param  boolean $get_extended_data Populate additional data that is somewhat costly to compute (add_url, archive_url)
     * @return ?array Map of content-type info (null: disabled)
     */
    public function info(?string $zone = null, bool $get_extended_data = false) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }

        if (!addon_installed('cms_homesite_tracker')) {
            return null;
        }

        // We have to make a custom db connection since the issue tracker uses a different prefix
        require_code('database/' . get_db_type());
        $db_driver = object_factory('Database_Static_' . get_db_type(), false, ['mantis_']);
        $db = new DatabaseConnector(get_db_site(), get_db_site_host(), get_db_site_user(), get_db_site_password(), 'mantis_', false, $db_driver);

        return [
            'support_custom_fields' => false,

            'content_type_label' => 'cms_homesite:TRACKER_ISSUE',
            'content_type_universal_label' => 'Tracker Issue',

            'db' => $db,
            'table' => 'bug_table',
            'id_field' => 'id',
            'id_field_numeric' => true,
            'parent_category_field' => null,
            'parent_category_meta_aware_type' => null,
            'is_category' => false,
            'is_entry' => true,
            'category_field' => null, // For category permissions
            'permission_module' => null, // For category permissions
            'parent_spec__table_name' => null,
            'parent_spec__parent_name' => null,
            'parent_spec__field_name' => null,
            'category_is_string' => false,

            'title_field' => 'summary',
            'title_field_dereference' => false,
            'description_field' => null,
            'description_field_dereference' => false,
            'description_field_supports_comcode' => false,
            'image_field' => null,
            'image_field_is_theme_image' => false,
            'alternate_icon_theme_image' => 'icons/buttons/points',

            'view_page_link_pattern' => null,
            'edit_page_link_pattern' => null,
            'view_category_page_link_pattern' => null,
            'add_url' => null,
            'archive_url' => null,

            'support_url_monikers' => false,

            'views_field' => null,
            'order_field' => null,
            'submitter_field' => 'reporter_id',
            'author_field' => null,
            'add_time_field' => 'date_submitted',
            'edit_time_field' => 'last_updated',
            'date_field' => null,
            'validated_field' => null,
            'validation_time_field' => null,
            'additional_sort_fields' => [
                'popular' => null, // Abstract
                'added' => null, // Abstract
                'hours' => null, // Abstract
                'sponsorship_progress' => null, // Abstract
            ],

            'seo_type_code' => null,

            'feedback_type_code' => null,

            'search_hook' => null,
            'rss_hook' => null,
            'attachment_hook' => null,
            'notification_hook' => null,
            'sitemap_hook' => null,

            'addon_name' => 'cms_homesite_tracker',

            'cms_page' => 'tracker',
            'module' => 'tracker',

            'commandr_filesystem_hook' => null,
            'commandr_filesystem__is_folder' => false,

            'support_revisions' => false,

            'support_privacy' => false,

            'support_content_reviews' => false,

            'support_spam_heuristics' => null,

            'actionlog_regexp' => '\w+_TRACKER_ISSUE',

            'default_prominence_weight' => PROMINENCE_WEIGHT_HIGH,
            'default_prominence_flags' => 0,
        ];
    }
}
