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
class __lang_no_unused_test_set extends cms_test_case
{
    public function testNothingUnused()
    {
        require_code('third_party_code');
        require_code('files2');
        require_code('lang_compile');

        disable_php_memory_limit();
        cms_set_time_limit(200);

        // Exceptions
        $exceptions_dirs = array_merge(list_untouchable_third_party_directories(), [
        ]);
        $exceptions_files = array_merge(list_untouchable_third_party_files(), [
        ]);

        $all_php_files = [];
        $all_template_files = [];
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $exceptions_dirs) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $exceptions_files)) {
                continue;
            }

            $all_php_files[] = $path;
        }
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['tpl']);
        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $exceptions_dirs) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $exceptions_files)) {
                continue;
            }

            $all_template_files[] = $path;
        }
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['js']);
        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $exceptions_dirs) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $exceptions_files)) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            if (strpos($c, '/*{$,parser hint: pure}*/') === false) {
                $all_template_files[] = $path;
            }
        }
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['txt']);
        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $exceptions_dirs) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $exceptions_files)) {
                continue;
            }

            $all_template_files[] = $path;
        }
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['xml']);
        foreach ($files as $path) {
            if (preg_match('#^(' . implode('|', $exceptions_dirs) . ')/#', $path) != 0) {
                continue;
            }
            if (in_array($path, $exceptions_files)) {
                continue;
            }

            $all_template_files[] = $path;
        }

        $_all_php_code = [];
        foreach ($all_php_files as $path) {
            $code = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            foreach (explode("\n", $code) as $line) {
                if (strpos($line, "'") !== false) {
                    $_all_php_code[] = $line;
                }
            }
        }
        $all_php_code = implode("\n", $_all_php_code);
        $_all_template_code = [];
        foreach ($all_template_files as $path) {
            $code = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            foreach (explode("\n", $code) as $line) {
                if (strpos($line, '{!') !== false) {
                    $_all_template_code[] = $line;
                }
            }
        }
        $all_template_code = implode("\n", $_all_template_code);

        $skip_prefixes = [
            'RANK_SET_',
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
            'ECOM_PURCHASE_STAGE_',
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
            'MISC_SYMBOL__',
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
            'COUNTRY_',
            'CONTINENT_',
            'ECOM_CAT_',
            'ECOM_CATD_',
            'ARITHMETICAL_SYMBOL__',
            '_PASSWORD_RESET_TEXT_',
            'MAIL_NONMATCH_POLICY_',
            'MAILING_LIST_SIMPLE_SUBJECT_',
            'MAILING_LIST_SIMPLE_MAIL_',
            'SECURITY_LEVEL_',
            'HEALTH_CHECK_SUBJECT_',
            'RECAPTCHA_ERROR_',
            'AUTOFILL_TYPE_DESCRIPTION_',
            'LEADER_BOARD_TYPE_SHORT_',
            'DEFAULT_SLIDE',
            'LEDGER_STATUS_',
        ];
        $_skip_prefixes = '#^(' . implode('|', $skip_prefixes) . ')#';

        $skip_prefixes_regexp = '#^(' . implode('|', $skip_prefixes) . ')#';

        $skip = array_flip([
            'ALLOWED_FILES',
            'PLUPLOAD_QUEUED',
            'PLUPLOAD_CANCEL',
            'PLUPLOAD_REMOVE',
            'PLUPLOAD_UPLOADING',
            'PLUPLOAD_COMPLETE',
            'PLUPLOAD_FAILED',
            'CLEAR_COLOR_SELECTION',
            'NO_COLOR_SELECTION',
            'SELECT2_REMOVE_ALL_ITEMS',
            'SELECT2_NO_RESULTS',
            'SELECT2_LOADING_FAILED',
            'SELECT2_ENTER',
            'SELECT2_DELETE',
            'SELECT2_SELECTION_LIMIT',
            'SELECT2_LOADING_MORE',
            'SELECT2_SEARCHING',
            'THEME_CAPABILITY_administrative',
            'FROM_COLOUR',
            'TO_COLOUR',
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
            'INSERT_MISC_SYMBOL',
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
            'GEOCODE_ZERO_RESULTS',
            'GEOCODE_REQUEST_DENIED',
            'GEOCODE_UNKNOWN_ERROR',
            'CONTENT_REVIEW_AUTO_ACTION_invalidate',
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
            'en_right',
            'LATITUDE',
            'LONGITUDE',
            'UPGRADER_UP_INFO_1',
            'UPGRADER_UP_INFO_2',
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
            '_VIEW_IMAGE',
            '_VIEW_VIDEO',
            'EDIT_WARNING',
            'NEWFORWARDING_DESCRIPTION',
            'NEWPOP3_DESCRIPTION',
            'TITLE_POP3',
            'takes_lots_of_space',
            'TAX_SALES_NUMBER',
            'TAX_VAT_NUMBER',
            'TAX_SALES',
            'TAX_VAT',
        ]);
        $_skip = array_flip($skip);

        $cli = is_cli();

        $files = get_directory_contents(get_file_base() . '/lang/EN', get_file_base() . '/lang/EN', null, false, true, ['ini']);
        foreach ($files as $path) {
            if (($this->only !== null) && ($this->only != basename($path))) {
                continue;
            }

            $input = [];
            _get_lang_file_map($path, $input, 'strings', false, true, 'EN');

            if ($cli) {
                echo 'Processing: ' . $path . "\n";
            }

            foreach ($input as $key => $val) {
                if (strpos($all_php_code, '\'' . $key . '\'') !== false) { // Most efficient check
                    $contains = true;
                } else { // Remaining check
                    if (preg_match($skip_prefixes_regexp, $key) != 0) {
                        continue;
                    }

                    if (isset($skip[$key])) {
                        continue;
                    }

                    $_key = preg_quote($key, '#');
                    $contains = (preg_match('#(\{!' . $key . '|:' . $key . ')#', $all_template_code) != 0) || (strpos($all_php_code, ':' . $key . "'") != 0);
                }
                $this->assertTrue($contains, $key . ': cannot find usage of language string (' . $val . ')');
            }
        }
    }
}
