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
class lang_no_unused_test_set extends cms_test_case
{
    public function testNothingUnused()
    {
        require_code('files');
        require_code('lang_compile');

        disable_php_memory_limit();

        @set_time_limit(0);

        $all_code = '';
        $files = $this->do_dir(get_file_base(), '', 'php');
        foreach ($files as $file) {
            $all_code .= file_get_contents($file);
        }
        $files = $this->do_dir(get_file_base(), '', 'tpl');
        foreach ($files as $file) {
            $all_code .= file_get_contents($file);
        }
        $files = $this->do_dir(get_file_base() . '/themes', 'themes', 'js');
        foreach ($files as $file) {
            $c = file_get_contents($file);
            if (strpos($c, '/*{$,Parser hint: pure}*/') === false) {
                $all_code .= $c;
            }
        }
        $files = $this->do_dir(get_file_base(), '', 'txt');
        foreach ($files as $file) {
            $all_code .= file_get_contents($file);
        }
        $files = $this->do_dir(get_file_base(), '', 'xml');
        foreach ($files as $file) {
            $all_code .= file_get_contents($file);
        }
        $all_code .= file_get_contents(get_file_base() . '/install.php');

        $skip_prefixes = array(
            'BLOCK_',
            'MODULE_',
            'PRIVILEGE_',
            'ACCESS_DENIED__',
            'TIP_',
            'CMD_',
            'DEFAULT_CALENDAR_TYPE__',
            'MODIFIER_',
            'SPECIAL_CPF__',
            'DEFAULT_CPF_',
            'INPUT_COMCODE_',
            'COMCODE_GROUP_',
            'COMCODE_TAG_',
            'MEDIA_TYPE_',
            'CONFIG_OPTION_',
            'CONFIG_CATEGORY_',
            'ERROR_UPLOADING_',
            'NEXT_ITEM_',
            'DYNAMIC_NOTICE_',
            'PURCHASE_STAGE_',
            'LENGTH_UNIT_',
            '_LENGTH_UNIT_',
            'PAYMENT_STATE_',
            'FIELD_TYPE_',
            'INPUTSYSTEM_',
            'FORUM_CLASS_',
            'ENABLE_NOTIFICATIONS_',
            'DIGEST_EMAIL_SUBJECT_',
            'PERM_TYPE_',
            'DIRECTIVE__',
            'ABSTRACTION_SYMBOL_',
            'PROGRAMMATIC_SYMBOL__',
            'LOGICAL_SYMBOL__',
            'FORMATTING_SYMBOL__',
            'SYMBOL__',
            'CALENDAR_MONTHLY_RECURRENCE_',
            'CHAT_EFFECT_',
            'EMOTICON_RELEVANCE_LEVEL_',
            'POST_INDICATOR_',
            'PAYMENT_GATEWAY_',
            'X_PER_',
            'PER_',
            'NC_',
            'CONFIG_GROUP_',
            'faqs__',
            'links__',
            'ECOM_CAT_',
            'ECOM_CATD_',
            'ARITHMETICAL_SYMBOL__',
            '_PASSWORD_RESET_TEXT_',
        );
        $_skip_prefixes = '#^(' . implode('|', $skip_prefixes) . ')#';

        $skip = array(
            'CONTINUE_RESTORATION',
            'ADD_PRIVATE_CALENDAR_EVENT',
            'EDIT_PRIVATE_CALENDAR_EVENT',
            'EDIT_PRIVATE_THIS_CALENDAR_EVENT',
            'DELETE_PRIVATE_CALENDAR_EVENT',
            'ADD_PUBLIC_CALENDAR_EVENT',
            'EDIT_PUBLIC_CALENDAR_EVENT',
            'EDIT_PUBLIC_THIS_CALENDAR_EVENT',
            'DELETE_PUBLIC_CALENDAR_EVENT',
            'EVENT_TIME_RANGE_WITH_TIMEZONE',
            'EVENT_TIME_RANGE_WITHIN_DAY_WITH_TIMEZONE',
            'INSERT_SYMBOL',
            'INSERT_PROGRAMMATIC_SYMBOL',
            'INSERT_FORMATTING_SYMBOL',
            'INSERT_ABSTRACTION_SYMBOL',
            'INSERT_ARITHMETICAL_SYMBOL',
            'INSERT_LOGICAL_SYMBOL',
            'INSERT_DIRECTIVE',
            'CSS_RULE_UNMATCHED_css_replace',
            'CSS_RULE_UNMATCHED_css_prepend',
            'CSS_RULE_UNMATCHED_css_append',
            'CSS_RULE_OVERMATCHED_css_replace',
            'CSS_RULE_OVERMATCHED_css_prepend',
            'CSS_RULE_OVERMATCHED_css_append',
            'ADD_WIKI_POST_SUBJECT',
            'ADD_WIKI_POST_BODY',
            'ADD_WIKI_PAGE_SUBJECT',
            'ADD_WIKI_PAGE_BODY',
            'EDIT_WIKI_POST_SUBJECT',
            'EDIT_WIKI_POST_BODY',
            'EDIT_WIKI_PAGE_SUBJECT',
            'EDIT_WIKI_PAGE_BODY',
            'TICKET_SIMPLE_SUBJECT_new',
            'TICKET_SIMPLE_SUBJECT_reply',
            'TICKET_SIMPLE_MAIL_new',
            'TICKET_SIMPLE_MAIL_reply',
            'EDIT_FORWARDING_DOMAIN',
            'EDIT_POP3_DOMAIN',
            'EDIT_PERMISSION_PRODUCT',
            'EDIT_CUSTOM_PRODUCT',
            'GOOGLE_GEOCODE_ZERO_RESULTS',
            'GOOGLE_GEOCODE_REQUEST_DENIED',
            'GOOGLE_GEOCODE_UNKNOWN_ERROR',
            'CONTENT_REVIEW_AUTO_ACTION_unvalidate',
            'CONTENT_REVIEW_AUTO_ACTION_delete',
            'CONTENT_REVIEW_AUTO_ACTION_leave',
            'BLOCKS_TYPE_side',
            'BLOCKS_TYPE_main',
            'BLOCKS_TYPE_bottom',
            'ADDED_COMMENT_ON_UNTITLED',
            '_ADDED_COMMENT_ON_UNTITLED',
            'ADDED_COMMENT_ON_UNTYPED',
            '_ADDED_COMMENT_ON_UNTYPED',
            'ACTIVITY_LIKES_UNTITLED',
            '_ACTIVITY_LIKES_UNTITLED',
            '_ACTIVITY_LIKES_UNTYPED',
            '_SUBSCRIPTION_START_TIME',
            '_SUBSCRIPTION_TERM_START_TIME',
            '_SUBSCRIPTION_TERM_END_TIME',
            '_SUBSCRIPTION_EXPIRY_TIME',
            'MYSQL_QUERY_CHANGES_MAKE_1',
            'MYSQL_QUERY_CHANGES_MAKE_2',
            'LONGITUDE',
            'LATITUDE',
            'FU_UP_INFO_1',
            'FU_UP_INFO_2',
            'MAP_POSITION_FIELD',
            'PRIORITY_na',
            'DESCRIPTION_DELETE_PARENT_CONTENTS',
            '_CHOOSE_EDIT_LIST_EXTRA',
            '_DELETE_MEMBER_SUICIDAL',
            'DESCRIPTION_PASSWORD_EDIT',
            'NOTIFICATION_SUBJECT_CONTENT_REVIEWS_delete',
            'NOTIFICATION_BODY_CONTENT_REVIEWS_delete',
            'VIEW_AS_THREADED',
            'VIEW_AS_LINEAR',
            '_SUBMITTED_BY',
            'BAD_SECURITY_CODE',
            'BAD_CARD_DATE',
            'MISSING_ADDON',
            'NO_SANDBOX',
            'HOURS_AGO',
            '_VIEW_IMAGE',
            '_VIEW_VIDEO',
            'EDIT_WARNING',
            'takes_lots_of_space',
        );
        $_skip = array_flip($skip);

        $cli = (php_sapi_name() == 'cli');

        $dh = opendir(get_file_base() . '/lang/EN/');
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) != '.ini') {
                continue;
            }
            if ($file[0] == '.') {
                continue;
            }

            $input = array();
            _get_lang_file_map(get_file_base() . '/lang/EN/' . $file, $input, 'strings', false, true, 'EN');

            if ($cli) {
                echo 'Processing: ' . $file . "\n";
            }

            foreach ($input as $key => $val) {
                if (preg_match($_skip_prefixes, $key) != 0) {
                    continue;
                }

                if (isset($_skip[$key])) {
                    continue;
                }

                if (strpos($all_code, '\'' . $key . '\'') !== false) { // Most efficient check
                    $contains = true;
                } else { // Full check
                    $_key = preg_quote($key, '#');
                    $contains = (preg_match('#(\{!' . $key . '|:' . $key . '|\'' . $key . '\')#', $all_code) != 0);
                }
                $this->assertTrue($contains, $key . ': cannot find usage of language string (' . str_replace('%', '', $val) . ')');
            }
        }
        closedir($dh);
    }

    private function do_dir($dir, $dir_stub, $ext)
    {
        $exceptions = array(
            'tracker',
            '_tests/codechecker/netbeans',
        );

        $files = array();

        if (($dh = opendir($dir)) !== false) {
            while (($file = readdir($dh)) !== false) {
                if (($file[0] != '.') && (!should_ignore_file((($dir_stub == '') ? '' : ($dir_stub . '/')) . $file, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_SUPPLIED_CONTENTS | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                    if (in_array((($dir_stub == '') ? '' : ($dir_stub . '/')) . $file, $exceptions)) {
                        continue;
                    }

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
