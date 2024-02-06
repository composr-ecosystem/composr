<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class Hook_resource_meta_aware_tutorials_internal
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

            'db' => get_db_for('tutorials_internal'),
            'table' => 'tutorials_internal',
            'id_field' => 't_page_name',
            'id_field_numeric' => false,
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

            'title_field' => 'CALL: generate_tutorials_entry_title',
            'title_field_dereference' => false,
            'description_field' => 'CALL: generate_tutorials_entry_description',
            'description_field_dereference' => false,
            'description_field_supports_comcode' => false,
            'image_field' => 'CALL: generate_tutorials_entry_image_url',
            'image_field_is_theme_image' => true,
            'alternate_icon_theme_image' => 'icons/help',

            'view_page_link_pattern' => '_SEARCH:tutorials:view:_SEARCH',
            'edit_page_link_pattern' => null,
            'view_category_page_link_pattern' => null,
            'add_url' => null,
            'archive_url' => '_SEARCH:tutorials',

            'support_url_monikers' => false,

            'views_field' => 't_views',
            'order_field' => null,
            'submitter_field' => null,
            'author_field' => null,
            'add_time_field' => null,
            'edit_time_field' => null,
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

            'cms_page' => null,
            'module' => null,

            'commandr_filesystem_hook' => null,
            'commandr_filesystem__is_folder' => false,

            'support_revisions' => false,

            'support_privacy' => false,

            'support_content_reviews' => false,

            'support_spam_heuristics' => null,

            'actionlog_regexp' => null,

            'default_prominence_weight' => PROMINENCE_WEIGHT_HIGH,
            'default_prominence_flags' => 0,
        ];
    }
}

/**
 * Find an entry title.
 *
 * @param  array $row Database row of entry
 * @param  integer $render_type A FIELD_RENDER_* constant
 * @param  boolean $resource_fs_style Whether to use the content API as resource-fs requires (may be slightly different)
 * @return ?mixed Content title (string or Tempcode, depending on $render_type) (null: could not generate)
 */
function generate_tutorials_entry_title(array $row, int $render_type = 1, bool $resource_fs_style = false)
{
    require_code('tutorials');
    $data = get_tutorial_metadata($row['t_page_name']);
    $ret = $data['title'];

    switch ($render_type) {
        case FIELD_RENDER_COMCODE:
            return escape_comcode($ret);

        case FIELD_RENDER_HTML:
            return make_string_tempcode(escape_html($ret));
    }

    return $ret;
}

/**
 * Find an entry description.
 *
 * @param  array $row Database row of entry
 * @param  integer $render_type A FIELD_RENDER_* constant
 * @param  boolean $resource_fs_style Whether to use the content API as resource-fs requires (may be slightly different)
 * @return ?mixed Content description (string or Tempcode, depending on $render_type) (null: could not generate)
 */
function generate_tutorials_entry_description(array $row, int $render_type = 1, bool $resource_fs_style = false)
{
    require_code('tutorials');
    $data = get_tutorial_metadata($row['t_page_name']);
    $ret = $data['summary'];

    switch ($render_type) {
        case FIELD_RENDER_COMCODE:
            return escape_comcode($ret);

        case FIELD_RENDER_HTML:
            return make_string_tempcode(escape_html($ret));
    }

    return $ret;
}

/**
 * Find an entry image.
 *
 * @param  array $row Database row of entry
 * @return string The image URL (blank: none)
 */
function generate_tutorials_entry_image_url(array $row) : string
{
    require_code('tutorials');
    $data = get_tutorial_metadata($row['t_page_name']);
    return find_theme_image($data['icon']);
}
