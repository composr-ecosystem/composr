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
class template_no_unused_test_set extends cms_test_case
{
    public function testNothingUnused()
    {
        require_code('themes2');
        require_code('files2');

        disable_php_memory_limit();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $all_code = '';
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $all_code .= cms_file_get_contents_safe(get_file_base() . '/' . $path);
        }

        $exceptions = [
            'COMCODE_TABULAR_FAKE_TABLE',
            'COMCODE_TABULAR_TABLE',
            'COMCODE_TABULAR_FLEX',
            'COMCODE_TABULAR_INLINE_BLOCKS',
            'COMCODE_TABULAR_FLOATS',
            'FACEBOOK_FOOTER',
            'GALLERY_HOMEPAGE_HERO_SLIDE',
            'BLOCK_MAIN_NEWS_GRID',
            'BLOCK_MAIN_NEWS_GRID_ITEM',
            'BLOCK_MAIN_NEWS',
            'MAIL_RAW',
            'BLOCK_MAIN_MEMBERS',
            'BLOCK_MAIN_MEMBERS_COMPLEX',
            'CAPTCHA_LOOSE',
            'COMMENTS_POSTING_FORM_CAPTCHA',
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
            'CNS_MEMBER_PROFILE_FIELD',
            'CNS_MEMBER_PROFILE_FIELDS',
            'CHATCODE_EDITOR_MICRO_BUTTON',
            'CNS_MEMBER_DIRECTORY_SCREEN_FILTERS',
            'CNS_MEMBER_DIRECTORY_SCREEN_FILTER',
            'CNS_TOPIC_POLL_ANSWER_RADIO',
            'CNS_TOPIC_POLL_ANSWER_TICK',
            'CNS_TOPIC_POLL_VIEW_RESULTS',
            'CNS_VIEW_GROUP_MEMBER_SECONDARY',
            'COMCODE_CODE_SCROLL',
            'COMCODE_SUBTITLE',
            'COMMANDR_CNS_NOTIFICATION',
            'COMMANDR_PT_NOTIFICATION',
            'COMMUNITY_BILLBOARD_FOOTER',
            'EMOTICON_IMG_CODE_DIR',
            'EMOTICON_IMG_CODE_THEMED',
            'FILEDUMP_FOOTER',
            'FILEDUMP_SEARCH',
            'FILTER_BOX',
            'FORM_SCREEN_ARE_REQUIRED',
            'FORM_SCREEN_FIELD_DESCRIPTION',
            'FORM_SCREEN_INPUT_DATE',
            'FORM_SCREEN_INPUT_HIDDEN_2',
            'FORM_SCREEN_INPUT_TIME',
            'FORM_STANDARD_END',
            'GALLERY_POPULAR',
            'GLOBAL_HELPER_PANEL',
            'PERMISSIONS_CONTENT_ACCESS_LIST',
            'PERMISSIONS_CONTENT_ACCESS_TICK',
            'HANDLE_CONFLICT_RESOLUTION',
            'HTML_HEAD',
            'HTML_HEAD_POLYFILLS',
            'HYPERLINK',
            'HYPERLINK_BUTTON',
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
            'NOTIFICATION_BUTTONS',
            'NOTIFICATION_TYPES',
            'ECOM_PRODUCT_CUSTOM',
            'ECOM_PRODUCT_GAMBLING',
            'ECOM_PRODUCT_HIGHLIGHT_NAME',
            'ECOM_PRODUCT_PERMISSION',
            'ECOM_PRODUCT_TOPIC_PIN',
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
            'STANDARDBOX_accordion_wrap',
            'STANDARDBOX_default',
            'WEBSTANDARDS_CHECK_ERROR',
            'WEBSTANDARDS_CHECK_SCREEN',
            'WIKI_RATING_FORM',
            'ACTIVITY_FEED_ACTIVITY',
            'BLOCK_MAIN_CHOOSE_TO_BOOK',
            'BLOCK_SIDE_BOOK_DATE_RANGE',
            'BLOCK_TWITTER_FEED',
            'BLOCK_TWITTER_FEED_TWEET',
            'BLOCK_YOUTUBE_CHANNEL',
            'BLOCK_YOUTUBE_CHANNEL_VIDEO',
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
            'FORM_STANDARD_START',
            'MAIL',
            'ECOM_PRODUCT_BANK',
            'ECOM_PRODUCT_DISASTR',
            'ECOM_PRODUCT_GIFTR',
            'W_MESSAGE_ALL',
            'W_MESSAGE_TO',
            'RATING_INLINE_STATIC',
            'ADMIN_ZONE_SEARCH',
            'GOOGLE_TIME_PERIODS',
            'HEADER_CLASSIC',
            'HEADER_SIDE',
            'HEADER_MODERN',
            'STATS_GRAPH',
            'BLOCK_MAIN_MULTI_CONTENT_TABLE',
            'BLOCK_MAIN_MULTI_CONTENT__FOOTER',
            'BLOCK_MAIN_MULTI_CONTENT_LIST',
            'BLOCK_MAIN_MULTI_CONTENT_MOSAIC',
            'BLOCK_MAIN_MULTI_CONTENT_SLIDER',
            'BLOCK_MAIN_MULTI_CONTENT_GRID',
            'BLOCK_MAIN_MULTI_CONTENT_TILES',
            'BLOCK_MAIN_MULTI_CONTENT__HEADER',
            'BLOCK_MAIN_MULTI_CONTENT_CAROUSEL',
            'BLOCK_MAIN_MULTI_CONTENT_BOXES',
        ];

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            $paths = [
                get_file_base() . '/themes/' . $theme . '/templates',
                get_file_base() . '/themes/' . $theme . '/templates_custom',
            ];
            foreach ($paths as $path) {
                $dh = @opendir($path);
                if ($dh !== false) {
                    while (($file = readdir($dh)) !== false) {
                        if (cms_strtolower_ascii(substr($file, -4)) == '.tpl') {
                            $file = basename($file, '.tpl');

                            if (in_array($file, $exceptions)) {
                                continue;
                            }

                            $this->assertTrue(strpos($all_code, 'do_template(\'' . $file . '\'') !== false, 'Cannot find use of ' . $file . ' template');
                        }
                    }
                    closedir($dh);
                }
            }
        }
    }
}
