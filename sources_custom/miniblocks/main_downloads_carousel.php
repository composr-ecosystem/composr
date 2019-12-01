<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    downloads_carousel
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('downloads_carousel')) {
    return do_template('RED_ALERT', ['_GUID' => 'hu3oqghv6805h85kfm5tvmwnohi7c6m6', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('downloads_carousel'))]);
}

if (!isset($map['id'])) {
    $map['id'] = strval(db_get_first_id());
}
$id = $map['id'];

require_code('images');
require_code('downloads');
require_lang('downloads');
require_javascript('core_rich_media');
require_css('carousels');

$subdownloads = new Tempcode();
require_code('selectcode');
$filter_where = selectcode_to_sqlfragment($id . '*', 'id', 'download_categories', 'parent_id', 'category_id', 'id');
$all_rows = $GLOBALS['SITE_DB']->query('SELECT d.* FROM ' . get_table_prefix() . 'download_downloads d WHERE ' . $filter_where . ' ORDER BY ' . $GLOBALS['SITE_DB']->translate_field_ref('name'), 20, 0, false, true, ['name' => 'SHORT_TRANS', 'the_description' => 'LONG_TRANS__COMCODE']);
shuffle($all_rows);
foreach ($all_rows as $d_row) {
    $d_url = build_url(['page' => 'downloads', 'type' => 'entry', 'id' => $d_row['id']], get_module_zone('downloads'));
    if (addon_installed('galleries')) {
        $i_rows = $GLOBALS['SITE_DB']->query_select('images', ['url', 'thumb_url', 'id'], ['cat' => 'download_' . strval($d_row['id'])], '', 1, $d_row['default_pic'] - 1);
        if (array_key_exists(0, $i_rows)) {
            $thumb_url = ensure_thumbnail($i_rows[0]['url'], $i_rows[0]['thumb_url'], 'galleries', 'images', $i_rows[0]['id']);
            $subdownloads->attach(hyperlink($d_url, do_image_thumb($thumb_url, render_download_box($d_row, false, false/*breadcrumbs?*/, '_SEARCH', null, false/*context?*/)), false, false));
        }
    }
}

if ($subdownloads->is_empty()) {
    $content = paragraph(do_lang('NO_ENTRIES'), '', 'nothing-here');
} else {
    $carousel_id = strval(mt_rand(0, mt_getrandmax()));

    $icon_button_left = do_template('ICON', ['_GUID' => 'dbce29b0f4acba4a91bacdb3e91467e9', 'NAME' => 'carousel/button_left']);
    $icon_button_right = do_template('ICON', ['_GUID' => '299320806e10587593d26119286bd95a', 'NAME' => 'carousel/button_right']);
    $content = make_string_tempcode(/** @lang HTML */'
        <div id="carousel-' . $carousel_id . '" class="carousel" style="display: none" data-view="Carousel" data-view-params=\'{"carouselId":"' . $carousel_id . '"}\'>
            <div class="move-left js-btn-car-move" data-move-amount="-100">' . $icon_button_left->evaluate() . '</div>
            <div class="main"></div>
            <div class="move-right js-btn-car-move" data-move-amount="+100">' . $icon_button_right->evaluate() . '</div>
        </div>

        <div class="carousel-temp" id="carousel-ns-' . $carousel_id . '">
            ' . $subdownloads->evaluate() . '
        </div>
    ');
}

$tpl = put_in_standard_box($content, do_lang('RANDOM_20_DOWNLOADS'));
$tpl->evaluate_echo();
