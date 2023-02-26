<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/*EXTRA FUNCTIONS: sleep*/

/**
 * Composr test case class (unit testing).
 */
class _filter_xml_test_set extends cms_test_case
{
    protected $session_id = null;

    public function setUp()
    {
        parent::setUp();

        $this->session_id = $this->establish_admin_callback_session();

        require_code('files');
        require_code('csrf_filter');
    }

    public function testNonFilter()
    {
        if (($this->only !== null) && ($this->only != 'testNonFilter')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <filter members="100">
                    <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                        <shun>test</shun>
                    </qualify>
                </filter>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = 'test';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result !== null);
    }

    public function testFilter()
    {
        if (($this->only !== null) && ($this->only != 'testFilter')) {
            return;
        }

        $guest_id = $GLOBALS['FORUM_DRIVER']->get_guest_id();
        $admin_id = $GLOBALS['FORUM_DRIVER']->get_guest_id() + 1;

        $test_xml = '
            <fieldRestrictions>
                <filter members="' . strval($admin_id) . '">
                    <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                        <shun>test</shun>
                    </qualify>
                </filter>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = 'test';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result === null);
    }

    public function testNonQualify()
    {
        if (($this->only !== null) && ($this->only != 'testNonQualify')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_x" types="add,_add,_edit,__edit" fields="title">
                    <shun>test</shun>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = 'test';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result !== null);
    }

    public function testQualify()
    {
        if (($this->only !== null) && ($this->only != 'testQualify')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <shun>test</shun>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = 'test';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result === null);
    }

    public function testRemoveShout()
    {
        if (($this->only !== null) && ($this->only != 'testRemoveShout')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <removeShout />
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $rnd = strval(mt_rand(1, 100000));
        $title = 'EXAMPLE' . $rnd;

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        if (get_db_type() == 'xml') {
            sleep(1); // Need different timestamps because IDs are randomised
        }
        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result !== null);

        $rows = $GLOBALS['SITE_DB']->query_select('news', ['*'], [], 'ORDER BY date_and_time DESC, id DESC', 1);
        if (array_key_exists(0, $rows)) {
            $row = $rows[0];
            $this->assertTrue(get_translated_text($row['title']) == 'Example' . $rnd);
        }
    }

    public function testSentenceCase()
    {
        if (($this->only !== null) && ($this->only != 'testSentenceCase')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <sentenceCase />
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = 'this is a test';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        if (get_db_type() == 'xml') {
            sleep(1); // Need different timestamps because IDs are randomised
        }
        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result !== null);

        $rows = $GLOBALS['SITE_DB']->query_select('news', ['*'], [], 'ORDER BY date_and_time DESC, id DESC'/*, 1*/);
        if (array_key_exists(0, $rows)) {
            $row = $rows[0];
            $this->assertTrue(get_translated_text($row['title']) == 'This is a test');
        }
    }

    public function testTitleCase()
    {
        if (($this->only !== null) && ($this->only != 'testTitleCase')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <titleCase />
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = 'this is a test';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        if (get_db_type() == 'xml') {
            sleep(1); // Need different timestamps because IDs are randomised
        }
        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result !== null);

        $rows = $GLOBALS['SITE_DB']->query_select('news', ['*'], [], 'ORDER BY date_and_time DESC, id DESC', 1);
        if (array_key_exists(0, $rows)) {
            $row = $rows[0];
            $this->assertTrue(get_translated_text($row['title']) == 'This Is A Test');
        }
    }

    public function testAppend()
    {
        if (($this->only !== null) && ($this->only != 'testAppend')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <prepend>foobar</prepend>
                    <append>foobar</append>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = 'EXAMPLE';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        if (get_db_type() == 'xml') {
            sleep(1); // Need different timestamps because IDs are randomised
        }
        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result !== null);

        $rows = $GLOBALS['SITE_DB']->query_select('news', ['*'], [], 'ORDER BY date_and_time DESC, id DESC', 1);
        if (array_key_exists(0, $rows)) {
            $row = $rows[0];
            $this->assertTrue(get_translated_text($row['title']) == 'foobarEXAMPLEfoobar');
        }
    }

    public function testReplace()
    {
        if (($this->only !== null) && ($this->only != 'testReplace')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <replace from="blah">foobar</replace>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = 'blah';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        if (get_db_type() == 'xml') {
            sleep(1); // Need different timestamps because IDs are randomised
        }
        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result !== null);

        $rows = $GLOBALS['SITE_DB']->query_select('news', ['*'], [], 'ORDER BY date_and_time DESC, id DESC', 1);
        if (array_key_exists(0, $rows)) {
            $row = $rows[0];
            $this->assertTrue(get_translated_text($row['title']) == 'foobar');
        }
    }

    public function testDeepClean()
    {
        if (($this->only !== null) && ($this->only != 'testDeepClean')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <deepClean />
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = ' blah ';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

        if (get_db_type() == 'xml') {
            sleep(1); // Need different timestamps because IDs are randomised
        }
        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result !== null);

        $rows = $GLOBALS['SITE_DB']->query_select('news', ['*'], [], 'ORDER BY date_and_time DESC, id DESC', 1);
        if (array_key_exists(0, $rows)) {
            $row = $rows[0];
            $_title = get_translated_text($row['title']);
            $this->assertTrue($_title == 'blah', 'Got ' . $_title);
        }
    }

    public function testDefaultFields()
    {
        if (($this->only !== null) && ($this->only != 'testDefaultFields')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add" fields="title">
                    <replace>foobar</replace>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $title = 'EXAMPLE';

        $post = [
            'title' => $title,
            'main_news_category' => '7',
            'author' => 'admin',
            'validated' => '1',
            'post' => 'Test Test Test Test Test',
            'news' => 'Test Test Test Test Test',
            'csrf_token' => generate_csrf_token(true),
            'confirm_double_post' => '1',
        ];

        $url = build_url(['page' => 'cms_news', 'type' => 'add'], 'cms');

        if (get_db_type() == 'xml') {
            sleep(1); // Need different timestamps because IDs are randomised
        }
        $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result !== null);

        if ($result !== null) {
            $this->assertTrue(substr_count($result, ' value="foobar"') == 1);
        }
    }

    public function testMinLength()
    {
        if (($this->only !== null) && ($this->only != 'testMinLength')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <minLength>4</minLength>
                    <maxLength>6</maxLength>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $expects = [
            'xxx' => false,
            'xxxx' => true,
            'xxxxxx' => true,
            'xxxxxxx' => false,
        ];

        foreach ($expects as $title => $expect) {
            $post = [
                'title' => $title,
                'main_news_category' => '7',
                'author' => 'admin',
                'validated' => '1',
                'post' => 'Test Test Test Test Test',
                'news' => 'Test Test Test Test Test',
                'csrf_token' => generate_csrf_token(true),
                'confirm_double_post' => '1',
            ];

            $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

            $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
            if ($expect) {
                $this->assertTrue($result !== null);
            } else {
                $this->assertTrue($result === null);
            }
        }
    }

    public function testPossibilitySet()
    {
        if (($this->only !== null) && ($this->only != 'testPossibilitySet')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <possibilitySet>a,b,c</possibilitySet>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $expects = [
            'b' => true,
            'x' => false,
        ];

        foreach ($expects as $title => $expect) {
            $post = [
                'title' => $title,
                'main_news_category' => '7',
                'author' => 'admin',
                'validated' => '1',
                'post' => 'Test Test Test Test Test',
                'news' => 'Test Test Test Test Test',
                'csrf_token' => generate_csrf_token(true),
                'confirm_double_post' => '1',
            ];

            $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

            $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
            if ($expect) {
                $this->assertTrue($result !== null);
            } else {
                $this->assertTrue($result === null);
            }
        }
    }

    public function testDisallowedWord()
    {
        if (($this->only !== null) && ($this->only != 'testDisallowedWord')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <disallowedWord>yo%</disallowedWord>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $expects = [
            'hello' => true,
            'yogurt' => false,
        ];

        foreach ($expects as $title => $expect) {
            $post = [
                'title' => $title,
                'main_news_category' => '7',
                'author' => 'admin',
                'validated' => '1',
                'post' => 'Test Test Test Test Test',
                'news' => 'Test Test Test Test Test',
                'csrf_token' => generate_csrf_token(true),
                'confirm_double_post' => '1',
            ];

            $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

            $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
            if ($expect) {
                $this->assertTrue($result !== null, 'Got ' . gettype($result) . ' for ' . $title);
            } else {
                $this->assertTrue($result === null, 'Got ' . gettype($result) . ' for ' . $title);
            }
        }
    }

    public function testDisallowedSubstring()
    {
        if (($this->only !== null) && ($this->only != 'testDisallowedSubstring')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <disallowedSubstring>blah blah blah</disallowedSubstring>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $expects = [
            'blah blah' => true,
            'blah blah blah' => false,
            'blah blah blah blah' => false,
        ];

        foreach ($expects as $title => $expect) {
            $post = [
                'title' => $title,
                'main_news_category' => '7',
                'author' => 'admin',
                'validated' => '1',
                'post' => 'Test Test Test Test Test',
                'news' => 'Test Test Test Test Test',
                'csrf_token' => generate_csrf_token(true),
                'confirm_double_post' => '1',
            ];

            $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

            $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
            if ($expect) {
                $this->assertTrue($result !== null);
            } else {
                $this->assertTrue($result === null);
            }
        }
    }

    public function testShun()
    {
        if (($this->only !== null) && ($this->only != 'testShun')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <shun>xxx</shun>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $expects = [
            'foobar' => true,
            'xxx' => false,
        ];

        foreach ($expects as $title => $expect) {
            $post = [
                'title' => $title,
                'main_news_category' => '7',
                'author' => 'admin',
                'validated' => '1',
                'post' => 'Test Test Test Test Test',
                'news' => 'Test Test Test Test Test',
                'csrf_token' => generate_csrf_token(true),
                'confirm_double_post' => '1',
            ];

            $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

            $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
            if ($expect) {
                $this->assertTrue($result !== null);
            } else {
                $this->assertTrue($result === null);
            }
        }
    }

    public function testPattern()
    {
        if (($this->only !== null) && ($this->only != 'testPattern')) {
            return;
        }

        $test_xml = '
            <fieldRestrictions>
                <qualify pages="cms_news" types="add,_add,_edit,__edit" fields="title">
                    <pattern>x+</pattern>
                </qualify>
            </fieldRestrictions>
        ';
        cms_file_put_contents_safe(get_custom_file_base() . '/data_custom/xml_config/fields.xml', $test_xml, FILE_WRITE_BOM);

        $expects = [
            'foobar' => false,
            'xxx' => true,
        ];

        foreach ($expects as $title => $expect) {
            $post = [
                'title' => $title,
                'main_news_category' => '7',
                'author' => 'admin',
                'validated' => '1',
                'post' => 'Test Test Test Test Test',
                'news' => 'Test Test Test Test Test',
                'csrf_token' => generate_csrf_token(true),
                'confirm_double_post' => '1',
            ];

            $url = build_url(['page' => 'cms_news', 'type' => '_add'], 'cms');

            $result = http_get_contents($url->evaluate(), ['trigger_error' => false, 'timeout' => 20.0, 'post_params' => $post, 'cookies' => [get_session_cookie() => $this->session_id]]);
            if ($expect) {
                $this->assertTrue($result !== null);
            } else {
                $this->assertTrue($result === null);
            }
        }
    }

    public function tearDown()
    {
        @unlink(get_custom_file_base() . '/data_custom/xml_config/fields.xml');
        sync_file(get_custom_file_base() . '/data_custom/xml_config/fields.xml');

        parent::tearDown();
    }
}
