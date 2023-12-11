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
class template_no_unused_test_set extends cms_test_case
{
    public function testNothingUnused()
    {
        require_code('files');
        require_code('themes2');

        disable_php_memory_limit();

        $all_code = '';
        $files = $this->do_dir(get_file_base(), '', 'php');
        $files[] = 'install.php';
        foreach ($files as $file) {
            $all_code .= file_get_contents($file);
        }

        $exceptions = array(
            'BLOCK_MAIN_MEMBERS',
            'BLOCK_MAIN_MEMBERS_COMPLEX',
            'PASSWORD_CHECK_JS',
            'AJAX_PAGINATION',
            'BLOCK_SIDE_GALLERIES_LINE',
            'BLOCK_SIDE_GALLERIES_LINE_DEPTH',
            'CALENDAR_DAY_ENTRY',
            'CATALOGUE_DEFAULT_CATEGORY_EMBED',
            'CATALOGUE_DEFAULT_CATEGORY_SCREEN',
            'CATALOGUE_DEFAULT_ENTRY_SCREEN',
            'CATALOGUE_DEFAULT_FIELD_MULTILIST',
            'CATALOGUE_DEFAULT_FIELD_PICTURE',
            'CATALOGUE_DEFAULT_FIELDMAP_ENTRY_FIELD',
            'CATALOGUE_DEFAULT_FIELDMAP_ENTRY_WRAP',
            'CATALOGUE_DEFAULT_GRID_ENTRY_FIELD',
            'CATALOGUE_DEFAULT_GRID_ENTRY_WRAP',
            'CATALOGUE_DEFAULT_TABULAR_ENTRY_FIELD',
            'CATALOGUE_DEFAULT_TABULAR_ENTRY_WRAP',
            'CATALOGUE_DEFAULT_TABULAR_HEADCELL',
            'CATALOGUE_DEFAULT_TABULAR_WRAP',
            'CATALOGUE_DEFAULT_TITLELIST_ENTRY',
            'CATALOGUE_DEFAULT_TITLELIST_WRAP',
            'CATALOGUE_links_TABULAR_ENTRY_FIELD',
            'CATALOGUE_links_TABULAR_ENTRY_WRAP',
            'CATALOGUE_links_TABULAR_HEADCELL',
            'CATALOGUE_links_TABULAR_WRAP',
            'CATALOGUE_products_CATEGORY_EMBED',
            'CATALOGUE_products_CATEGORY_SCREEN',
            'CATALOGUE_products_ENTRY_SCREEN',
            'CATALOGUE_products_FIELDMAP_ENTRY_FIELD',
            'CATALOGUE_products_GRID_ENTRY_FIELD',
            'CATALOGUE_products_GRID_ENTRY_WRAP',
            'CHATCODE_EDITOR_MICRO_BUTTON',
            'CNS_MEMBER_DIRECTORY_SCREEN_FILTERS',
            'CNS_MEMBER_DIRECTORY_SCREEN_FILTER',
            'CNS_TOPIC_POLL_ANSWER_RADIO',
            'CNS_TOPIC_POLL_VIEW_RESULTS',
            'CNS_VIEW_GROUP_MEMBER_SECONDARY',
            'COLUMNED_TABLE_ACTION_DELETE_CATEGORY',
            'COLUMNED_TABLE_ACTION_DOWNLOAD',
            'COLUMNED_TABLE_ACTION_TRANSLATE',
            'COMCODE_CODE_SCROLL',
            'COMCODE_SUBTITLE',
            'COMMANDR_CNS_NOTIFICATION',
            'COMMANDR_PT_NOTIFICATION',
            'CROP_TEXT_MOUSE_OVER_INLINE',
            'ECOM_SHOPPING_CART_STAGE_PAY',
            'EMOTICON_IMG_CODE_DIR',
            'EMOTICON_IMG_CODE_THEMED',
            'FILEDUMP_FOOTER',
            'FILEDUMP_SEARCH',
            'FONT_SIZER',
            'FORM_SCREEN_ARE_REQUIRED',
            'FORM_SCREEN_FIELD_DESCRIPTION',
            'FORM_SCREEN_INPUT_DATE',
            'FORM_SCREEN_INPUT_HIDDEN_2',
            'FORM_SCREEN_INPUT_TIME',
            'FORM_STANDARD_END',
            'GALLERY_POPULAR',
            'GLOBAL_HELPER_PANEL',
            'GLOBAL_HTML_WRAP_mobile',
            'HANDLE_CONFLICT_RESOLUTION',
            'HTML_HEAD',
            'HYPERLINK',
            'HYPERLINK_BUTTON',
            'HYPERLINK_TOOLTIP',
            'LOOKUP_SCREEN',
            'MAIL',
            'MASS_SELECT_DELETE_FORM',
            'MASS_SELECT_MARKER',
            'MEDIA__DOWNLOAD_LINK',
            'MEDIA_WEBPAGE_OEMBED_RICH',
            'MEDIA_WEBPAGE_OEMBED_VIDEO',
            'MENU_BRANCH_dropdown',
            'MENU_BRANCH_embossed',
            'MENU_BRANCH_mobile',
            'MENU_BRANCH_popup',
            'MENU_BRANCH_select',
            'MENU_BRANCH_sitemap',
            'MENU_BRANCH_tree',
            'MENU_dropdown',
            'MENU_embossed',
            'MENU_LINK_PROPERTIES',
            'MENU_mobile',
            'MENU_popup',
            'MENU_select',
            'MENU_sitemap',
            'MENU_SPACER_dropdown',
            'MENU_SPACER_embossed',
            'MENU_SPACER_mobile',
            'MENU_SPACER_popup',
            'MENU_SPACER_select',
            'MENU_SPACER_sitemap',
            'MENU_SPACER_tree',
            'MENU_tree',
            'NEWS_CHICKLETS',
            'NOTIFICATION_BUTTONS',
            'NOTIFICATION_TYPES',
            'POINTSTORE_CUSTOM',
            'POINTSTORE_GAMBLING',
            'POINTSTORE_HIGHLIGHT_NAME',
            'POINTSTORE_PERMISSION',
            'POINTSTORE_TOPIC_PIN',
            'PURCHASE_WIZARD_STAGE_PAY',
            'PURCHASE_WIZARD_STAGE_SUBSCRIBE',
            'QUIZ_RESULTS',
            'RATING_BOX',
            'RATING_DISPLAY_SHARED',
            'RATING_FORM',
            'RATING_INLINE_DYNAMIC',
            'RESTORE_HTML_WRAP',
            'RESULTS_cart_TABLE',
            'RESULTS_products_TABLE',
            'RESULTS_TABLE',
            'RESULTS_TABLE_cart_FIELD',
            'RESULTS_TABLE_ENTRY',
            'RESULTS_TABLE_FIELD',
            'SEARCH_FOR_SEARCH_DOMAIN_OPTION_DATE',
            'SEARCH_FOR_SEARCH_DOMAIN_OPTION_FLOAT',
            'SEARCH_FOR_SEARCH_DOMAIN_OPTION_INTEGER',
            'SEARCH_FOR_SEARCH_DOMAIN_OPTION_JUST_DATE',
            'SEARCH_FOR_SEARCH_DOMAIN_OPTION_JUST_TIME',
            'SEARCH_FOR_SEARCH_DOMAIN_OPTION_LIST',
            'SEARCH_FOR_SEARCH_DOMAIN_OPTION_MULTI_LIST',
            'SEARCH_FOR_SEARCH_DOMAIN_OPTION_TEXT',
            'SEARCH_FOR_SEARCH_DOMAIN_OPTION_TICK',
            'SEARCH_RESULT_CATALOGUE_ENTRIES',
            'STAFF_ACTIONS',
            'STANDARDBOX_accordion',
            'STANDARDBOX_default',
            'WEBSTANDARDS_CHECK_ERROR',
            'WEBSTANDARDS_CHECK_SCREEN',
            'WIKI_RATING_FORM',
            'ACTIVITY',
            'BLOCK_MAIN_CHOOSE_TO_BOOK',
            'BLOCK_SIDE_BOOK_DATE_RANGE',
            'BLOCK_TWITTER_FEED',
            'BLOCK_TWITTER_FEED_TWEET',
            'BLOCK_YOUTUBE_CHANNEL',
            'BLOCK_YOUTUBE_CHANNEL_STYLE',
            'BOOK_DATE_CHOOSE',
            'BOOKABLE_NOTES',
            'BOOKING_CONFIRM_FCOMCODE',
            'BOOKING_DISPLAY',
            'BOOKING_NOTICE_FCOMCODE',
            'COMCODE_ENCRYPT',
            'COMCODE_FLIP',
            'COMCODE_SELF_DESTRUCT',
            'CUSTOMER_CREDIT_INFO',
            'DOWNLOADS_FOLLOWUP_EMAIL',
            'DOWNLOADS_FOLLOWUP_EMAIL_DOWNLOAD_LIST',
            'EMOTICON_IMG_CODE_THEMED',
            'MAIL',
            'POINTSTORE_BANK',
            'POINTSTORE_DISASTR',
            'POINTSTORE_GIFTR',
            'W_MESSAGE_ALL',
            'W_MESSAGE_TO',
            'RATING_INLINE_STATIC',
            'ADMIN_ZONE_SEARCH',
        );

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            $paths = array(
                get_file_base() . '/themes/' . $theme . '/templates',
                get_file_base() . '/themes/' . $theme . '/templates_custom',
            );
            foreach ($paths as $path) {
                $dh = opendir($path);
                while (($f = readdir($dh)) !== false) {
                    if (strtolower(substr($f, -4)) == '.tpl') {
                        $f = basename($f, '.tpl');

                        if (in_array($f, $exceptions)) {
                            continue;
                        }

                        $this->assertTrue(strpos($all_code, 'do_template(\'' . $f . '\'') !== false, 'Cannot find use of ' . $f . ' template');
                    }
                }
            }
        }
    }

    private function do_dir($dir, $dir_stub, $ext)
    {
        $files = array();

        if (($dh = opendir($dir)) !== false) {
            while (($file = readdir($dh)) !== false) {
                if (($file[0] != '.') && (!should_ignore_file((($dir_stub == '') ? '' : ($dir_stub . '/')) . $file, IGNORE_BUNDLED_VOLATILE))) {
                    if (is_file($dir . '/' . $file)) {
                        if (substr($file, -strlen($ext) - 1, strlen($ext) + 1) == '.' . $ext) {
                            $files[] = $dir . '/' . $file;
                        }
                    } elseif (is_dir($dir . '/' . $file)) {
                        $_files = $this->do_dir($dir . '/' . $file, (($dir_stub == '') ? '' : ($dir_stub . '/')) . $file, $ext);
                        $files = array_merge($_files, $files);
                    }
                }
            }
            closedir($dh);
        }

        return $files;
    }
}
