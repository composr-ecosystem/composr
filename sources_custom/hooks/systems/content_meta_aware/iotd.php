<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Hook class.
 */
class Hook_content_meta_aware_iotd
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
            'support_custom_fields' => true,

            'content_type_label' => 'iotds:IOTD',
            'content_type_universal_label' => 'Image of the day',

            'connection' => $GLOBALS['SITE_DB'],
            'table' => 'iotd',
            'id_field' => 'id',
            'id_field_numeric' => true,
            'parent_category_field' => null,
            'parent_category_meta_aware_type' => null,
            'is_category' => false,
            'is_entry' => true,
            'category_field' => null, // For category permissions
            'category_type' => null, // For category permissions
            'parent_spec__table_name' => null,
            'parent_spec__parent_name' => null,
            'parent_spec__field_name' => null,
            'category_is_string' => true,

            'title_field' => 'i_title',
            'title_field_dereference' => true,
            'title_field_supports_comcode' => true,
            'description_field' => 'caption',
            'thumb_field' => 'thumb_url',
            'thumb_field_is_theme_image' => false,
            'alternate_icon_theme_image' => null,

            'view_page_link_pattern' => '_SEARCH:iotds:view:_WILD',
            'edit_page_link_pattern' => '_SEARCH:cms_iotds:_edit:_WILD',
            'view_category_page_link_pattern' => null,
            'add_url' => ($get_extended_data && function_exists('has_submit_permission') && has_submit_permission('mid', get_member(), get_ip_address(), 'cms_iotds')) ? (get_module_zone('cms_iotds') . ':cms_iotds:add') : null,
            'archive_url' => $get_extended_data ? (((!is_null($zone)) ? $zone : get_module_zone('iotds')) . ':iotds') : null,

            'support_url_monikers' => true,

            'views_field' => 'iotd_views',
            'order_field' => null,
            'submitter_field' => 'submitter',
            'author_field' => null,
            'add_time_field' => 'add_date',
            'edit_time_field' => 'edit_date',
            'date_field' => 'date_and_time', // add_date is the technical add date, but date_and_time is when it went live
            'validated_field' => null,

            'seo_type_code' => null,

            'feedback_type_code' => 'iotds',

            'permissions_type_code' => null, // null if has no permissions

            'search_hook' => 'iotd',
            'rss_hook' => 'iotds',
            'attachment_hook' => null,
            'unvalidated_hook' => null,
            'notification_hook' => 'iotd_chosen',
            'sitemap_hook' => 'iotd',

            'addon_name' => 'iotds',

            'cms_page' => 'cms_iotds',
            'module' => 'iotd',

            'commandr_filesystem_hook' => 'iotds',
            'commandr_filesystem__is_folder' => false,

            'support_revisions' => false,

            'support_privacy' => false,

            'support_content_reviews' => true,

            'actionlog_regexp' => '\w+_IOTD',
        );
    }

    /**
     * Run function for content hooks. Renders a content box for an award/randomisation.
     *
     * @param  array $row The database row for the content
     * @param  ID_TEXT $zone The zone to display in
     * @param  boolean $give_context Whether to include context (i.e. say WHAT this is, not just show the actual content)
     * @param  boolean $include_breadcrumbs Whether to include breadcrumbs (if there are any)
     * @param  ?ID_TEXT $root Virtual root to use (null: none)
     * @param  boolean $attach_to_url_filter Whether to copy through any filter parameters in the URL, under the basis that they are associated with what this box is browsing
     * @param  ID_TEXT $guid Overridden GUID to send to templates (blank: none)
     * @return Tempcode Results
     */
    public function run($row, $zone, $give_context = true, $include_breadcrumbs = true, $root = null, $attach_to_url_filter = false, $guid = '')
    {
        require_code('iotds');

        return render_iotd_box($row, $zone, false, $give_context, $guid);
    }
}
