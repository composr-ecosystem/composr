<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

/**
 * Hook class.
 */
class Hook_resource_meta_aware_tutorials_external
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
        if (!addon_installed('composr_tutorials')) {
            return null;
        }

        return [
            'support_custom_fields' => false,

            'content_type_label' => 'tutorials:TUTORIAL',
            'content_type_universal_label' => 'Tutorial',

            'db' => get_db_for('tutorials_external'),
            'table' => 'tutorials_external',
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

            'title_field' => 't_title',
            'title_field_dereference' => false,
            'description_field' => 't_summary',
            'description_field_dereference' => false,
            'description_field_supports_comcode' => false,
            'image_field' => 't_icon',
            'image_field_is_theme_image' => true,
            'alternate_icon_theme_image' => 'icons/help',

            'view_page_link_pattern' => 'docs:_WILD',
            'edit_page_link_pattern' => '_SEARCH:cms_tutorials:_edit:_WILD',
            'view_category_page_link_pattern' => null,
            'add_url' => '_SEARCH:cms_tutorials:add',
            'archive_url' => '_SEARCH:tutorials',

            'support_url_monikers' => false,

            'views_field' => 't_views',
            'order_field' => null,
            'submitter_field' => 't_submitter',
            'author_field' => null,
            'add_time_field' => 't_add_date',
            'edit_time_field' => 't_edit_date',
            'date_field' => null,
            'validated_field' => null,

            'seo_type_code' => null,

            'feedback_type_code' => null,

            'search_hook' => null,
            'rss_hook' => null,
            'attachment_hook' => null,
            'notification_hook' => null,
            'sitemap_hook' => null,

            'addon_name' => 'composr_tutorials',

            'cms_page' => 'cms_tutorials',
            'module' => 'tutorials',

            'commandr_filesystem_hook' => null,
            'commandr_filesystem__is_folder' => false,

            'support_revisions' => false,

            'support_privacy' => false,

            'support_content_reviews' => false,

            'support_spam_heuristics' => null,

            'actionlog_regexp' => '\w+_TUTORIAL',

            'default_prominence_weight' => PROMINENCE_WEIGHT_HIGH,
            'default_prominence_flags' => 0,
        ];
    }
}
