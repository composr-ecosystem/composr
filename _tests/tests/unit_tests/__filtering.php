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
class __filtering_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testFiltercode()
    {
        if (($this->only !== null) && ($this->only !== 'testFiltercode')) {
            return;
        }

        $this->cleanup_db();

        $guest_id = $GLOBALS['FORUM_DRIVER']->get_guest_id();
        $admin_id = $GLOBALS['FORUM_DRIVER']->get_guest_id() + 1;
        $test_id = $GLOBALS['FORUM_DRIVER']->get_guest_id() + 2;
        $guest_username = $GLOBALS['FORUM_DRIVER']->get_username($guest_id);

        $hook_contents = "
            <" . "?php

            class Hook_content_meta_aware_temp_test
            {
                public function info()
                {
                    return [
                        'support_custom_fields' => true,

                        'content_type_label' => 'TITLE',
                        'content_type_universal_label' => 'Title',

                        'db' => \$GLOBALS['SITE_DB'],
                        'table' => 'temp_test',
                        'id_field' => 'id',
                        'id_field_numeric' => true,
                        'parent_category_field' => null,
                        'parent_category_meta_aware_type' => null,
                        'is_category' => false,
                        'is_entry' => true,
                        'category_field' => null,
                        'permission_module' => null,
                        'parent_spec__table_name' => null,
                        'parent_spec__parent_name' => null,
                        'parent_spec__field_name' => null,
                        'category_is_string' => true,

                        'title_field' => 't_short_text',
                        'title_field_dereference' => false,
                        'title_field_supports_comcode' => false,
                        'description_field' => null,
                        'description_field_dereference' => false,
                        'description_field_supports_comcode' => false,
                        'image_field' => null,
                        'image_field_is_theme_image' => false,
                        'alternate_icon_theme_image' => null,

                        'view_page_link_pattern' => null,
                        'edit_page_link_pattern' => null,
                        'view_category_page_link_pattern' => null,
                        'add_url' => null,
                        'archive_url' => null,

                        'support_url_monikers' => false,

                        'views_field' => null,
                        'order_field' => null,
                        'submitter_field' => null,
                        'author_field' => null,
                        'add_time_field' => null,
                        'edit_time_field' => null,
                        'date_field' => null,
                        'validated_field' => null,

                        'seo_type_code' => 'temp_test',

                        'feedback_type_code' => 'temp_test',

                        'search_hook' => null,
                        'rss_hook' => null,
                        'attachment_hook' => null,
                        'notification_hook' => null,
                        'sitemap_hook' => null,

                        'addon_name' => null,

                        'cms_page' => null,
                        'module' => null,

                        'commandr_filesystem_hook' => null,
                        'commandr_filesystem__is_folder' => false,

                        'support_spam_heuristics' => false,

                        'support_revisions' => false,

                        'support_privacy' => false,

                        'support_content_reviews' => false,

                        'actionlog_regexp' => null,

                        'filtercode_protected_fields' => ['t_secret'],

                        'default_prominence_weight' => PROMINENCE_WEIGHT_NONE,
                    ];
                }
            }
        ";
        file_put_contents(get_file_base() . '/sources_custom/hooks/systems/content_meta_aware/temp_test.php', trim(preg_replace('#^            #m', '', $hook_contents)) . "\n");

        $GLOBALS['SITE_DB']->create_table('temp_test', [
            'id' => '*INTEGER',
            't_short_text' => 'SHORT_TEXT',
            't_short_trans' => 'SHORT_TRANS',
            't_member' => 'MEMBER',
            't_real' => 'REAL',
            't_time' => 'TIME',
            't_language_name' => 'LANGUAGE_NAME',
            't_id_text' => 'ID_TEXT',
            't_binary' => 'BINARY',
            't_secret' => 'SHORT_TEXT',
            't_linker' => 'INTEGER',
        ]);
        $GLOBALS['SITE_DB']->create_index('temp_test', '#t_short_text', ['t_short_text']);
        $GLOBALS['SITE_DB']->create_index('temp_test', '#t_language_name', ['t_language_name']);
        $GLOBALS['SITE_DB']->create_index('temp_test', '#t_id_text', ['t_id_text']);

        $GLOBALS['SITE_DB']->query_insert('temp_test', [
            'id' => 1,
            't_short_text' => 'axxxxx',
            't_member' => $guest_id,
            't_real' => 1.0,
            't_time' => 1000000,
            't_language_name' => 'EN',
            't_id_text' => 'axxxxx',
            't_binary' => 0,
            't_secret' => 'x',
            't_linker' => 1,
        ] + insert_lang('t_short_trans', 'axxxxx', 1));

        $GLOBALS['SITE_DB']->query_insert('temp_test', [
            'id' => 2,
            't_short_text' => 'bxxxxx',
            't_member' => $admin_id,
            't_real' => 2.0,
            't_time' => 2000000,
            't_language_name' => 'ES',
            't_id_text' => 'bxxxxx',
            't_binary' => 1,
            't_secret' => 'x',
            't_linker' => 2,
        ] + insert_lang('t_short_trans', 'bxxxxx', 1));

        $GLOBALS['SITE_DB']->query_insert('temp_test', [
            'id' => 3,
            't_short_text' => 'cxxxxx',
            't_member' => $test_id,
            't_real' => 3.0,
            't_time' => 3000000,
            't_language_name' => 'TA',
            't_id_text' => 'cxxxxx',
            't_binary' => 1,
            't_secret' => 'x',
            't_linker' => 3,
        ] + insert_lang('t_short_trans', 'cxxxxx', 1));

        $GLOBALS['SITE_DB']->query_delete('rating', ['rating_for_type' => 'temp_test']);
        $GLOBALS['SITE_DB']->query_insert('rating', ['rating_for_type' => 'temp_test', 'rating_for_id' => '1', 'rating_member' => get_member(), 'rating_ip' => get_ip_address(), 'rating_time' => time(), 'rating' => 4]);
        $GLOBALS['SITE_DB']->query_insert('rating', ['rating_for_type' => 'temp_test', 'rating_for_id' => '1', 'rating_member' => get_member(), 'rating_ip' => get_ip_address(), 'rating_time' => time(), 'rating' => 3]);

        require_code('content2');
        seo_meta_set_for_explicit('temp_test', '1', 'abc,def', 'abc');

        $GLOBALS['SITE_DB']->create_table('temp_test_linked', [
            'id' => '*INTEGER',
            'l_something' => 'INTEGER',
        ]);

        $GLOBALS['SITE_DB']->query_insert('temp_test_linked', [
            'id' => 1,
            'l_something' => 123,
        ]);

        $GLOBALS['SITE_DB']->query_insert('temp_test_linked', [
            'id' => 2,
            'l_something' => 124,
        ]);

        $GLOBALS['SITE_DB']->query_insert('temp_test_linked', [
            'id' => 3,
            'l_something' => 125,
        ]);

        if (function_exists('sleep')) {
            sleep(4); // Some DBs require some time to generate full-text index
        }

        require_code('filtercode');

        $filter_tests = [];

        $filter_tests = array_merge([
            // Filtering on 'id'
            'id<2' => [1],
            'id<=2' => [1, 2],
            'id>2' => [3],
            'id>=1' => [1, 2, 3],
            'id=' => [1, 2, 3], // because no filter
            'id=1' => [1],
            //Depends on DB 'id==' => [], // because nothing matches 0 after string coercion
            'id==1' => [1],
            // No ~= for integers
            // No ~ for integers
            'id<>' => [1, 2, 3], // because no filter
            'id<>1' => [2, 3],
            //Depends on DB 'id!=' => [1, 2, 3], // because nothing matches 0 after string coercion and negative
            'id!=1' => [2, 3],
            'id@1-2' => [1, 2],

            // Filtering on 't_short_text'
            // No < for strings
            // No <= for strings
            // No > for strings
            // No >= for strings
            't_short_text=' => [1, 2, 3], // because no filter
            't_short_text=axxxxx' => [1],
            't_short_text==' => [], // because nothing matches
            't_short_text==axxxxx' => [1],
            't_short_text~=ax' => [1],
            't_short_text~axxxxx' => [1],
            't_short_text<>' => [1, 2, 3], // because no filter
            't_short_text<>axxxxx' => [2, 3],
            't_short_text!=' => [1, 2, 3], // because nothing matches and negative
            't_short_text!=axxxxx' => [2, 3],
            // No @ for strings
        ]);

        // Filtering on 't_member' using IDs
        $filter_tests['t_member<' . strval($admin_id)] = [$guest_id];
        $filter_tests['t_member<=' . strval($admin_id)] = [$guest_id, $admin_id];
        $filter_tests['t_member>' . strval($admin_id)] = [$test_id];
        $filter_tests['t_member>=' . strval($guest_id)] = [$guest_id, $admin_id, $test_id];
        $filter_tests['t_member='] = [$guest_id, $admin_id, $test_id]; // because no filter
        $filter_tests['t_member=' . strval($guest_id)] = [$guest_id];
        //Depends on DB 't_member=='] = []; // because nothing matches 0 after string coercion
        $filter_tests['t_member==' . strval($guest_id)] = [$guest_id];
        // No ~= for integers
        // No ~ for integers
        $filter_tests['t_member<>'] = [$guest_id, $admin_id, $test_id]; // because no filter
        $filter_tests['t_member<>' . strval($guest_id)] = [$admin_id, $test_id];
        //Depends on DB 't_member!='] = [$guest_id, $admin_id, $test_id]; // because nothing matches 0 after string coercion and negative
        $filter_tests['t_member!=' . strval($guest_id)] = [$admin_id, $test_id];
        $filter_tests['t_member@' . strval($guest_id) . '-' . strval($admin_id)] = [$guest_id, $admin_id];

        // Filtering on 't_member' using usernames
        $filter_tests['t_member=' . $guest_username] = [$guest_id];
        $filter_tests['t_member==' . $guest_username] = [$guest_id];
        $filter_tests['t_member~=' . $guest_username] = [$guest_id];
        $filter_tests['t_member~' . $guest_username] = [$guest_id];
        $filter_tests['t_member<>' . $guest_username] = [$admin_id, $test_id];
        $filter_tests['t_member!=' . $guest_username] = [$admin_id, $test_id];

        $filter_tests = array_merge([
            // Filtering on 't_real'
            't_real<2.0' => [1],
            't_real<=2.0' => [1, 2],
            't_real>2.0' => [3],
            't_real>=1.1' => [2, 3],
            't_real=' => [1, 2, 3], // because no filter
            't_real=1.0' => [1],
            //Depends on DB 't_real==' => [], // because nothing matches 0.0 after string coercion
            't_real==1.0' => [1],
            // No ~= for floats
            // No ~ for floats
            't_real<>' => [1, 2, 3], // because no filter
            't_real<>1.0' => [2, 3],
            //Depends on DB 't_real!=' => [1, 2, 3], // because nothing matches 0.0 after string coercion and negative
            't_real!=1' => [2, 3],
            't_real@1.1-2.0' => [2],

            // Filtering on 't_time'
            't_time<2000000' => [1],
            't_time<=2000000' => [1, 2],
            't_time>2000000' => [3],
            't_time>=1100000' => [2, 3],
            't_time=' => [1, 2, 3], // because no filter
            't_time=1000000' => [1],
            //Depends on DB 't_time==' => [], // because nothing matches 0 after string coercion
            't_time==1000000' => [1],
            // No ~= for integers
            // No ~ for integers
            't_time<>' => [1, 2, 3], // because no filter
            't_time<>1000000' => [2, 3],
            //Depends on DB 't_time!=' => [1, 2, 3], // because nothing matches 0 after string coercion and negative
            't_time!=1000000' => [2, 3],
            't_time@1000000-2000000' => [1, 2],

            // Filtering on 't_language_name'
            // No < for strings
            // No <= for strings
            // No > for strings
            // No >= for strings
            't_language_name=' => [1, 2, 3], // because no filter
            't_language_name=EN' => [1],
            't_language_name==' => [], // because nothing matches
            't_language_name==EN' => [1],
            't_language_name~=EN' => [1],
            't_language_name~EN' => [1],
            't_language_name<>' => [1, 2, 3], // because no filter
            't_language_name<>EN' => [2, 3],
            't_language_name!=' => [1, 2, 3], // because nothing matches and negative
            't_language_name!=EN' => [2, 3],
            // No @ for strings

            // Filtering on 't_id_text'
            // No < for strings
            // No <= for strings
            // No > for strings
            // No >= for strings
            't_id_text=' => [1, 2, 3], // because no filter
            't_id_text=axxxxx' => [1],
            't_id_text==' => [], // because nothing matches
            't_id_text==axxxxx' => [1],
            't_id_text~=axxxxx' => [1],
            't_id_text~axxxxx' => [1],
            't_id_text<>' => [1, 2, 3], // because no filter
            't_id_text<>axxxxx' => [2, 3],
            't_id_text!=' => [1, 2, 3], // because nothing matches and negative
            't_id_text!=axxxxx' => [2, 3],
            // No @ for strings

            // Filtering on 't_binary'
            't_binary<1' => [1],
            't_binary<=1' => [1, 2, 3],
            't_binary>0' => [2, 3],
            't_binary>=0' => [1, 2, 3],
            't_binary=' => [1, 2, 3], // because no filter
            't_binary=0' => [1],
            //Depends on DB 't_binary==' => [1], // because 0 matches after string coercion
            't_binary==0' => [1],
            // No ~= for integers
            // No ~ for integers
            't_binary<>' => [1, 2, 3], // because no filter
            't_binary<>0' => [2, 3],
            //Depends on DB 't_binary!=' => [2, 3], // because 0 matches after string coercion and negative
            't_binary!=0' => [2, 3],
            't_binary@0-1' => [1, 2, 3],

            // Filtering on nothing
            '' => [1, 2, 3],
            'junk' => [1, 2, 3],

            // Filtering on multiple clauses
            'id=1,id=2' => [],
            'id=1,t_binary=0' => [1],

            // Filtering on multiple keys
            //Depends on DB 'id|t_id_text=1' => [1],
            //Depends on DB 'id|t_id_text=axxxxx' => [1],
            //Depends on DB 'id|t_id_text=zxxxxx' => [],
            'id|t_member=4' => [],

            // Filtering on multiple values
            'id=1|2' => [1, 2],
            'id=4|5' => [],
            't_id_text=axxxxx|bxxxxx' => [1, 2],
            't_id_text#axxxxx|bxxxxx' => [1, 2],
            't_id_text~=axxxxx|bxxxxx' => [1, 2],
            't_id_text~axxxxx|bxxxxx' => [1, 2],

            // Filtering on protected field
            't_secret=x' => [1, 2, 3],
            't_secret=y' => [1, 2, 3],

            // Filtering on rating
            'compound_rating=5' => [1],
            'average_rating=1.75' => [1],
            'fixed_random=123456789' => [],

            // Filtering on SEO
            'meta_keywords~abc' => [1],
            'meta_keywords~def' => [1],
            'meta_keywords~ghi' => [],
            'meta_keywords=' => [1, 2, 3], // because no filter
            'meta_keywords==' => [], // because nothing matches
            'meta_description=abc' => [1],
            'meta_description=ghi' => [],
            'meta_description=' => [1, 2, 3], // because no filter
            'meta_description==' => [], // because nothing matches

            // Linking tables
            '{t_linker=temp_test_linked.id},temp_test_linked.l_something=123' => [1],
        ]);

        foreach ($filter_tests as $filter => $filter_expected) {
            unset($filter_tests[$filter]);
            $filter = str_replace('Guest', $guest_username, $filter);
            $filter_tests[$filter] = $filter_expected;
        }

        foreach ($filter_tests as $filter => $filter_expected) {
            list($extra_join, $extra_where) = filtercode_to_sql($GLOBALS['SITE_DB'], parse_filtercode($filter), 'temp_test');
            $sql = 'SELECT DISTINCT r.id FROM ' . get_table_prefix() . 'temp_test r' . implode('', $extra_join) . ' WHERE 1=1' . $extra_where;
            $results = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query($sql));

            sort($results);
            sort($filter_expected);

            $this->assertTrue($results == $filter_expected, 'Failed Filtercode check for: ' . str_replace('%', '%%', $filter) . ', got: ' . implode(',', array_map('strval', $results)) . ', expected: ' . implode(',', array_map('strval', $filter_expected)) . '; query: ' . str_replace('%', '%%', $sql));
        }

        // Test using POST environment
        $filter_tests = [
            ['id<id_op><id>', 'id', '<', '2', [1]],
            ['id<id_op><id>', 'id', '@', '1-2', [1, 2]],
            ['t_short_text<t_short_text_op><t_short_text>', 't_short_text', '=', 'axxxxx', [1]],
            ['t_member<t_member_op><t_member>', 't_member', '=', $guest_username, [1]],
            ['t_real<t_real_op><t_real>', 't_real', '>=', '1.1', [2, 3]],
            ['t_real<t_real_op><t_real>', 't_real', '@', '1.1-2.0', [2]],
            ['t_time<t_time_op><t_time>', 't_time', '=', '2000000', [2]],
            ['t_binary<t_binary_op><t_binary>', 't_binary', '>', '0', [2, 3]],
        ];
        foreach ($filter_tests as $filter_parts) {
            list($filter, $key, $op, $val, $filter_expected) = $filter_parts;

            if ($val == 2000000) {
                $_POST['filter_' . $key . '_day'] = 24;
                $_POST['filter_' . $key . '_month'] = 1;
                $_POST['filter_' . $key . '_year'] = 1970;
                $_POST['filter_' . $key . '_hour'] = 3;
                $_POST['filter_' . $key . '_minute'] = 33;
                $_POST['filter_' . $key . '_seconds'] = 20;
                $_POST['timezone'] = 'UTC';
            } else {
                $_POST['filter_' . $key] = $val;
            }

            $_POST[$key . '_op'] = $op;

            list($extra_join, $extra_where) = filtercode_to_sql($GLOBALS['SITE_DB'], parse_filtercode($filter), 'temp_test');
            $sql = 'SELECT DISTINCT r.id FROM ' . get_table_prefix() . 'temp_test r' . implode('', $extra_join) . ' WHERE 1=1' . $extra_where;
            $results = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query($sql));

            sort($results);
            sort($filter_expected);

            $this->assertTrue($results == $filter_expected, 'Failed Filtercode check for: ' . str_replace('%', '%%', $filter) . ', got: ' . implode(',', array_map('strval', $results)) . ', expected: ' . implode(',', array_map('strval', $filter_expected)) . ', with: ' . str_replace('%', '%%', $sql));
        }

        // Test automatic filter form seems okay
        list($form_fields, $filter, $_links) = form_for_filtercode('', [], 'temp_test');
        $this->assertTrue(strpos($form_fields->evaluate(), '<input') !== false);
        $filter_expected = 'id<id_op><id>,t_binary<t_binary_op><t_binary>,t_id_text<t_id_text_op><t_id_text>,t_linker<t_linker_op><t_linker>,t_member<t_member_op><t_member>,t_real<t_real_op><t_real>,t_short_text<t_short_text_op><t_short_text>,t_short_trans<t_short_trans_op><t_short_trans>,t_time<t_time_op><t_time>,compound_rating<compound_rating_op><compound_rating>,average_rating<average_rating_op><average_rating>,meta_keywords<meta_keywords_op><meta_keywords>,meta_description<meta_description_op><meta_description>';
        $this->assertTrue($filter == $filter_expected, 'Got: ' . $filter . '; Expected: ' . $filter_expected);
    }

    protected $ids_and_parents;
    protected $selectcode;
    protected $expected;
    protected $expected_lazy;
    protected $expected_full;

    public function get_ids_and_parents()
    {
        return $this->ids_and_parents;
    }

    public function testSelectcode()
    {
        if (($this->only !== null) && ($this->only !== 'testSelectcode')) {
            return;
        }

        $this->cleanup_db();

        $GLOBALS['SITE_DB']->create_table('temp_test_categories', [
            'id' => '*INTEGER',
            'parent_id_of_cat' => 'INTEGER',
        ]);
        $GLOBALS['SITE_DB']->create_table('temp_test_entries', [
            'id' => '*INTEGER',
            'parent_id' => 'INTEGER',
        ]);

        $this->ids_and_parents = [
            1 => 2,
            2 => 2,
            3 => 2,
            4 => 3,
            5 => 3,
            6 => 3,
            7 => 3,
            8 => 3,
            100 => 12,
            101 => 13,
        ];

        foreach (array_unique($this->ids_and_parents) as $parent_id) {
            $GLOBALS['SITE_DB']->query_insert('temp_test_categories', [
                'id' => $parent_id,
                'parent_id_of_cat' => ($parent_id == 13) ? 12 : 1,
            ]);
        }

        foreach ($this->ids_and_parents as $id => $parent_id) {
            $GLOBALS['SITE_DB']->query_insert('temp_test_entries', [
                'id' => $id,
                'parent_id' => $parent_id,
            ]);
        }

        require_code('selectcode');

        // Test scenario 1
        // ---------------

        $this->selectcode = '1,3-10,!6,12*';
        $this->expected_lazy = [1, 3, 4, 5, 7, 8, 9, 10, 100, 101];
        $this->expected_full = [1, 3, 4, 5, 7, 8, 100, 101];

        $sql = selectcode_to_sqlfragment($this->selectcode, 'id', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        $results = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('temp_test_entries', ['id'], [], 'WHERE ' . $sql));
        sort($results);
        $this->assertTrue($results == $this->expected_full, 'Failed on this Selectcode: ' . $this->selectcode . ' / ' . $sql);

        $results = selectcode_to_idlist_using_db($this->selectcode, 'id', 'temp_test_entries', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected_lazy, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_memory($this->selectcode, $this->ids_and_parents, 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected_full, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_callback($this->selectcode, [[$this, 'get_ids_and_parents'], []], 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected_lazy, 'Failed on this Selectcode: ' . $this->selectcode);

        // Test scenario 2
        // ---------------

        $this->selectcode = '*,!1';
        $this->expected = [2, 3, 4, 5, 6, 7, 8, 100, 101];

        $sql = selectcode_to_sqlfragment($this->selectcode, 'id', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        $results = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('temp_test_entries', ['id'], [], 'WHERE ' . $sql));
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode . ' / ' . $sql);

        $results = selectcode_to_idlist_using_db($this->selectcode, 'id', 'temp_test_entries', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_memory($this->selectcode, $this->ids_and_parents, 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_callback($this->selectcode, [[$this, 'get_ids_and_parents'], []], 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        // Test scenario 3
        // ---------------

        $this->selectcode = '8+';
        $this->expected = [8, 100, 101];

        $sql = selectcode_to_sqlfragment($this->selectcode, 'id', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        $results = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('temp_test_entries', ['id'], [], 'WHERE ' . $sql));
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode . ' / ' . $sql);

        $results = selectcode_to_idlist_using_db($this->selectcode, 'id', 'temp_test_entries', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_memory($this->selectcode, $this->ids_and_parents, 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_callback($this->selectcode, [[$this, 'get_ids_and_parents'], []], 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        // Test scenario 4
        // ---------------

        $this->selectcode = '2#';
        $this->expected = [1, 2, 3];

        $sql = selectcode_to_sqlfragment($this->selectcode, 'id', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        $results = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('temp_test_entries', ['id'], [], 'WHERE ' . $sql));
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode . ' / ' . $sql);

        $results = selectcode_to_idlist_using_db($this->selectcode, 'id', 'temp_test_entries', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_memory($this->selectcode, $this->ids_and_parents, 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_callback($this->selectcode, [[$this, 'get_ids_and_parents'], []], 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        // Test scenario 5
        // ---------------

        $this->selectcode = '12>';
        $this->expected = [101];

        $sql = selectcode_to_sqlfragment($this->selectcode, 'id', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        $results = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('temp_test_entries', ['id'], [], 'WHERE ' . $sql));
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode . ' / ' . $sql);

        $results = selectcode_to_idlist_using_db($this->selectcode, 'id', 'temp_test_entries', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_memory($this->selectcode, $this->ids_and_parents, 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_callback($this->selectcode, [[$this, 'get_ids_and_parents'], []], 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        // Test scenario 6
        // ---------------

        $this->selectcode = '12*,13~';
        $this->expected = [100];

        $sql = selectcode_to_sqlfragment($this->selectcode, 'id', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        $results = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('temp_test_entries', ['id'], [], 'WHERE ' . $sql));
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode . ' / ' . $sql);

        $results = selectcode_to_idlist_using_db($this->selectcode, 'id', 'temp_test_entries', 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_memory($this->selectcode, $this->ids_and_parents, 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);

        $results = selectcode_to_idlist_using_callback($this->selectcode, [[$this, 'get_ids_and_parents'], []], 'temp_test_categories', 'parent_id_of_cat', 'parent_id', 'id');
        sort($results);
        $this->assertTrue($results == $this->expected, 'Failed on this Selectcode: ' . $this->selectcode);
    }

    protected function cleanup_db()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('temp_test_categories');
        $GLOBALS['SITE_DB']->drop_table_if_exists('temp_test_entries');

        $GLOBALS['SITE_DB']->drop_table_if_exists('temp_test');
        $GLOBALS['SITE_DB']->drop_table_if_exists('temp_test_linked');

        $GLOBALS['SITE_DB']->query_delete('rating', ['rating_for_type' => 'temp_test']);

        $GLOBALS['SITE_DB']->query_delete('seo_meta', ['meta_for_type' => 'temp_test']);
        $GLOBALS['SITE_DB']->query_delete('seo_meta_keywords', ['meta_for_type' => 'temp_test']);
    }

    public function tearDown()
    {
        if (!$this->debug) {
            $this->cleanup_db();
        }

        @unlink(get_file_base() . '/sources_custom/hooks/systems/content_meta_aware/temp_test.php');
        fix_permissions(get_file_base() . '/sources_custom/hooks/systems/content_meta_aware/temp_test.php');
        sync_file(get_file_base() . '/sources_custom/hooks/systems/content_meta_aware/temp_test.php');

        parent::tearDown();
    }
}
