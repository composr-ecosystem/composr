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

/**
 * Hook class.
 */
class Hook_addon_registry_testing_platform
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for (used in generating the TAR filename).
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }
    
    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }
    
    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return null;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Development';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return ['Marcus Baker', 'Jason Sweat', 'Travis Swicegood', 'Perrick Penet', 'Edward Z. Yang', 'ocProducts'];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'LGPL (SimpleTest), tests licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'The Composr testing platform.

This framework is designed to allow auto-runnable tests to easily be written for Composr. The advantage to this testing technique is once a test is written it can be re-run very easily -- being able to re-run a whole test set before each release will dramatically reduce the chance of new bugs creeping into releases, as tests would not pass in this circumstance. New bugs in new releases is always a problem for complex software like Composr, as it is a huge package and it\'s very easy to accidentally cause (and not notice) a new problem when fixing an old one.

[b]Do not run this on a production system as it will interfere with installs and may contain security holes.[/b]

This addon may have dependencies on any non-bundled Composr addon as it is designed to run directly out of a GitLab clone of Composr (it is only available in the addon directory due to auto-packaging).

Make sure html_dump and anything in it has 777 permissions (full write permissions).

[title="2"]Running[/title]

Simply call up [tt]http://yourbaseurl/_tests/index.php[/tt].

From there you can choose to run tests that have been written.

[title="2"]Writing tests[/title]

Tests are stored under the [tt]_tests/tests[/tt] directory, and are classed as either "regression tests" (tests written to illustrate a bug, that fail before we fix the bug, but pass after we fix the bug) or "unit tests" (a test designed to test some part of Composr, we usually actually refer to these as \'automated tests\').
Tests are PHP scripts, so a good understand if PHP is required to write them.

The testing framework is built around SimpleTest (https://github.com/simpletest/simpletest), so all their API can be used. We have extended it a little bit, so:
- you can call up page-links
- any pages loaded up are saved as HTML so you can check them via other means also (e.g. passing through an HTML validator, or checking them manually for aesthetic issues).
- you can make Composr think you are a logged in administrator
- there is some standard setUp/tearDown code should use for any test, to make sure Composr starts in a good state for testing (currently it just makes sure the site is not closed)
Read about the SimpleTest API on their website to understand what things like assertTrue mean, and what tools you have at your disposal.

[title="2"]Continuous Integration (CI)[/title]

The CI server tracks every commit to a branch, and runs the test set against it. Any failures are then posted to the commit that triggered the CI run.

This is great as running the full test set on a dev machine with each commit is unreasonable (it takes about 20 minutes). With CI issues can still be picked up quickly and resolved without the branch\'s stability drifting.

[title="2"]Contributing[/title]

We welcome any new tests you might want to write for us. We only have one at the moment and ideally we would have thousands, so there\'s a lot of work to do! The more tests we have, the more stable Composr will be.
Test writing can be fun, and doesn\'t take long if you already know programming. It\'s a great way to contribute in your free time without getting stuck in large projects.

If you\'ve written some tests please make a pull request.
We hope other users will appreciate your efforts and give you some points to reward your work.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [
            'codebook',
        ];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [ // Actually, it's all
                'meta_toolkit',
            ],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/tool.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/testing_platform.php',
            'data_custom/continuous_integration.php',
            'lang_custom/EN/phpdoc.ini',
            'sources_custom/phpdoc.php',
            'sources_custom/phpstub.php',
            'sources_custom/continuous_integration.php',
            'data_custom/functions.bin',

            '_tests/simpletest/.phan/.htaccess',
            '_tests/simpletest/.phan/index.html',
            '_tests/simpletest/extensions/.htaccess',
            '_tests/simpletest/extensions/coverage/.htaccess',
            '_tests/simpletest/extensions/coverage/bin/.htaccess',
            '_tests/simpletest/extensions/coverage/bin/index.html',
            '_tests/simpletest/extensions/coverage/index.html',
            '_tests/simpletest/extensions/coverage/test/.htaccess',
            '_tests/simpletest/extensions/coverage/test/index.html',
            '_tests/simpletest/extensions/coverage/test/sample/.htaccess',
            '_tests/simpletest/extensions/coverage/test/sample/index.html',
            '_tests/simpletest/extensions/css/.htaccess',
            '_tests/simpletest/extensions/css/index.html',
            '_tests/simpletest/extensions/dom_tester/.htaccess',
            '_tests/simpletest/extensions/dom_tester/index.html',
            '_tests/simpletest/extensions/dom_tester/test/.htaccess',
            '_tests/simpletest/extensions/dom_tester/test/index.html',
            '_tests/simpletest/extensions/dom_tester/test/support/.htaccess',
            '_tests/simpletest/extensions/dom_tester/test/support/index.html',
            '_tests/simpletest/extensions/img/.htaccess',
            '_tests/simpletest/extensions/img/index.html',
            '_tests/simpletest/extensions/index.html',
            '_tests/simpletest/extensions/js/.htaccess',
            '_tests/simpletest/extensions/js/index.html',
            '_tests/simpletest/extensions/js/tests/.htaccess',
            '_tests/simpletest/extensions/js/tests/index.html',
            '_tests/simpletest/extensions/phpunit/.htaccess',
            '_tests/simpletest/extensions/phpunit/index.html',
            '_tests/simpletest/extensions/phpunit/tests/.htaccess',
            '_tests/simpletest/extensions/phpunit/tests/index.html',
            '_tests/simpletest/extensions/selenium/.htaccess',
            '_tests/simpletest/extensions/selenium/index.html',
            '_tests/simpletest/extensions/selenium/test/.htaccess',
            '_tests/simpletest/extensions/selenium/test/index.html',
            '_tests/simpletest/extensions/testdox/.htaccess',
            '_tests/simpletest/extensions/testdox/index.html',
            '_tests/simpletest/extensions/treemap_reporter/.htaccess',
            '_tests/simpletest/extensions/treemap_reporter/index.html',
            '_tests/simpletest/extensions/treemap_reporter/test/.htaccess',
            '_tests/simpletest/extensions/treemap_reporter/test/index.html',
            '_tests/simpletest/index.html',
            '_tests/simpletest/test/.htaccess',
            '_tests/simpletest/test/index.html',
            '_tests/simpletest/test/issues/.htaccess',
            '_tests/simpletest/test/issues/index.html',
            '_tests/simpletest/test/site/base_tag/.htaccess',
            '_tests/simpletest/test/site/base_tag/index.html',
            '_tests/simpletest/test/site/path/.htaccess',
            '_tests/simpletest/test/site/path/index.html',
            '_tests/simpletest/test/site/protected/index.html',
            '_tests/simpletest/test/support/.htaccess',
            '_tests/simpletest/test/support/collector/.htaccess',
            '_tests/simpletest/test/support/collector/index.html',
            '_tests/simpletest/test/support/index.html',
            '_tests/simpletest/test/visual/.htaccess',
            '_tests/simpletest/test/visual/index.html',
            '_tests/simpletest/arguments.php',
            '_tests/simpletest/authentication.php',
            '_tests/simpletest/autorun.php',
            '_tests/simpletest/browser.php',
            '_tests/simpletest/Changelog.md',
            '_tests/simpletest/collector.php',
            '_tests/simpletest/compatibility.php',
            '_tests/simpletest/composer.json',
            '_tests/simpletest/cookies.php',
            '_tests/simpletest/default_reporter.php',
            '_tests/simpletest/detached.php',
            '_tests/simpletest/dumper.php',
            '_tests/simpletest/eclipse.php',
            '_tests/simpletest/encoding.php',
            '_tests/simpletest/errors.php',
            '_tests/simpletest/exceptions.php',
            '_tests/simpletest/expectation.php',
            '_tests/simpletest/extensions/colortext_reporter.php',
            '_tests/simpletest/extensions/coverage/autocoverage.php',
            '_tests/simpletest/extensions/coverage/bin/php-coverage-close.php',
            '_tests/simpletest/extensions/coverage/bin/php-coverage-open.php',
            '_tests/simpletest/extensions/coverage/bin/php-coverage-report.php',
            '_tests/simpletest/extensions/coverage/coverage_calculator.php',
            '_tests/simpletest/extensions/coverage/coverage_data_handler.php',
            '_tests/simpletest/extensions/coverage/coverage.php',
            '_tests/simpletest/extensions/coverage/coverage_reporter.php',
            '_tests/simpletest/extensions/coverage/coverage_utils.php',
            '_tests/simpletest/extensions/coverage/coverage_writer.php',
            '_tests/simpletest/extensions/coverage/templates/file.php',
            '_tests/simpletest/extensions/coverage/templates/index.php',
            '_tests/simpletest/extensions/coverage/test/coverage_calculator_test.php',
            '_tests/simpletest/extensions/coverage/test/coverage_data_handler_test.php',
            '_tests/simpletest/extensions/coverage/test/coverage_reporter_test.php',
            '_tests/simpletest/extensions/coverage/test/coverage_test.php',
            '_tests/simpletest/extensions/coverage/test/coverage_utils_test.php',
            '_tests/simpletest/extensions/coverage/test/coverage_writer_test.php',
            '_tests/simpletest/extensions/coverage/test/sample/code.php',
            '_tests/simpletest/extensions/coverage/test/test.php',
            '_tests/simpletest/extensions/css/webunit.css',
            '_tests/simpletest/extensions/dom_tester/css_selector.php',
            '_tests/simpletest/extensions/dom_tester.php',
            '_tests/simpletest/extensions/dom_tester/test/dom_tester_doc_test.php',
            '_tests/simpletest/extensions/dom_tester/test/dom_tester_test.php',
            '_tests/simpletest/extensions/dom_tester/test/support/child_adjacent.html',
            '_tests/simpletest/extensions/dom_tester/test/support/dom_tester.html',
            '_tests/simpletest/extensions/img/wait.gif',
            '_tests/simpletest/extensions/js/tests/TestOfWebunit.js.html',
            '_tests/simpletest/extensions/js/webunit.js',
            '_tests/simpletest/extensions/js/x.js',
            '_tests/simpletest/extensions/junit_xml_reporter.php',
            '_tests/simpletest/extensions/phpunit/PHPUnitTestCase.php',
            '_tests/simpletest/extensions/phpunit/tests/adapter_test.php',
            '_tests/simpletest/extensions/selenese_tester.php',
            '_tests/simpletest/extensions/selenium.php',
            '_tests/simpletest/extensions/selenium/remote-control.php',
            '_tests/simpletest/extensions/selenium/test/remote-control_test.php',
            '_tests/simpletest/extensions/testdox.php',
            '_tests/simpletest/extensions/testdox/test.php',
            '_tests/simpletest/extensions/treemap_reporter/jquery.php',
            '_tests/simpletest/extensions/treemap_reporter.php',
            '_tests/simpletest/extensions/treemap_reporter/test/treemap_node_test.php',
            '_tests/simpletest/extensions/treemap_reporter/treemap_recorder.php',
            '_tests/simpletest/extensions/webunit_reporter.php',
            '_tests/simpletest/form.php',
            '_tests/simpletest/frames.php',
            '_tests/simpletest/.htaccess',
            '_tests/simpletest/http.php',
            '_tests/simpletest/invoker.php',
            '_tests/simpletest/LICENSE',
            '_tests/simpletest/mock_objects.php',
            '_tests/simpletest/page.php',
            '_tests/simpletest/.phan/config.php',
            '_tests/simpletest/php_parser.php',
            '_tests/simpletest/README.md',
            '_tests/simpletest/recorder.php',
            '_tests/simpletest/reflection.php',
            '_tests/simpletest/remote.php',
            '_tests/simpletest/reporter.php',
            '_tests/simpletest/scorer.php',
            '_tests/simpletest/selector.php',
            '_tests/simpletest/shell_tester.php',
            '_tests/simpletest/simpletest.php',
            '_tests/simpletest/socket.php',
            '_tests/simpletest/tag.php',
            '_tests/simpletest/test/acceptance_test.php',
            '_tests/simpletest/test/all_tests.php',
            '_tests/simpletest/test/arguments_test.php',
            '_tests/simpletest/test/authentication_test.php',
            '_tests/simpletest/test/autorun_test.php',
            '_tests/simpletest/test/bad_test_suite.php',
            '_tests/simpletest/test/browser_test.php',
            '_tests/simpletest/test_case.php',
            '_tests/simpletest/test/collector_test.php',
            '_tests/simpletest/test/command_line_test.php',
            '_tests/simpletest/test/compatibility_test.php',
            '_tests/simpletest/test/cookies_test.php',
            '_tests/simpletest/test/detached_test.php',
            '_tests/simpletest/test/dumper_test.php',
            '_tests/simpletest/test/eclipse_test.php',
            '_tests/simpletest/test/encoding_test.php',
            '_tests/simpletest/test/errors_test.php',
            '_tests/simpletest/test/exceptions_test.php',
            '_tests/simpletest/test/expectation_test.php',
            '_tests/simpletest/test/extensions_tests.php',
            '_tests/simpletest/test/form_test.php',
            '_tests/simpletest/test/frames_test.php',
            '_tests/simpletest/test/http_test.php',
            '_tests/simpletest/test/interfaces_test.php',
            '_tests/simpletest/test/issues/test_issue29.php',
            '_tests/simpletest/test/issues/test_issue34.php',
            '_tests/simpletest/test/live_test.php',
            '_tests/simpletest/test/mock_objects_test.php',
            '_tests/simpletest/test/page_test.php',
            '_tests/simpletest/test/parse_error_test.php',
            '_tests/simpletest/test/parsing_test.php',
            '_tests/simpletest/test/php_parser_test.php',
            '_tests/simpletest/test/recorder_test.php',
            '_tests/simpletest/test/reflection_test.php',
            '_tests/simpletest/test/remote_test.php',
            '_tests/simpletest/test/shell_tester_test.php',
            '_tests/simpletest/test/shell_test.php',
            '_tests/simpletest/test/simpletest_test.php',
            '_tests/simpletest/test/site/1.html',
            '_tests/simpletest/test/site/2.html',
            '_tests/simpletest/test/site/3.html',
            '_tests/simpletest/test/site/base_change_redirect.php',
            '_tests/simpletest/test/site/base_tag/base_link.html',
            '_tests/simpletest/test/site/base_tag/form.html',
            '_tests/simpletest/test/site/base_tag/frameset.html',
            '_tests/simpletest/test/site/base_tag/frameset_with_base_tag.html',
            '_tests/simpletest/test/site/base_tag/page_1.html',
            '_tests/simpletest/test/site/base_tag/page_2.html',
            '_tests/simpletest/test/site/base_tag/relative_link.html',
            '_tests/simpletest/test/site/cookie_based_counter.php',
            '_tests/simpletest/test/site/counting_frameset.html',
            '_tests/simpletest/test/site/double_base_change_redirect.php',
            '_tests/simpletest/test/site/file.html',
            '_tests/simpletest/test/site/form_data_encoded_form.html',
            '_tests/simpletest/test/site/form.html',
            '_tests/simpletest/test/site/form_with_array_based_inputs.php',
            '_tests/simpletest/test/site/form_with_false_defaults.html',
            '_tests/simpletest/test/site/form_with_mixed_post_and_get.html',
            '_tests/simpletest/test/site/form_without_action.php',
            '_tests/simpletest/test/site/form_with_quoted_values.php',
            '_tests/simpletest/test/site/form_with_radio_buttons.html',
            '_tests/simpletest/test/site/form_with_tricky_defaults.html',
            '_tests/simpletest/test/site/form_with_unnamed_submit.html',
            '_tests/simpletest/test/site/frame_a.html',
            '_tests/simpletest/test/site/frame_b.html',
            '_tests/simpletest/test/site/frame_links.html',
            '_tests/simpletest/test/site/frameset.html',
            '_tests/simpletest/test/site/front_controller_style/a_page.php',
            '_tests/simpletest/test/site/front_controller_style/index.php',
            '_tests/simpletest/test/site/front_controller_style/show_request.php',
            '_tests/simpletest/test/site/.htaccess',
            '_tests/simpletest/test/site/index.html',
            '_tests/simpletest/test/site/link_confirm.php',
            '_tests/simpletest/test/site/local_redirect.php',
            '_tests/simpletest/test/site/messy_frameset.html',
            '_tests/simpletest/test/site/multiple_widget_form.html',
            '_tests/simpletest/test/site/nested_frameset.html',
            '_tests/simpletest/test/site/network_confirm.php',
            '_tests/simpletest/test/site/one_page_frameset.html',
            '_tests/simpletest/test/site/page_request.php',
            '_tests/simpletest/test/site/path/base_change_redirect.php',
            '_tests/simpletest/test/site/path/network_confirm.php',
            '_tests/simpletest/test/site/path/show_cookies.php',
            '_tests/simpletest/test/site/protected/1.html',
            '_tests/simpletest/test/site/protected/2.html',
            '_tests/simpletest/test/site/protected/3.html',
            '_tests/simpletest/test/site/protected/.htaccess',
            '_tests/simpletest/test/site/protected/.htpasswd',
            '_tests/simpletest/test/site/protected/local_redirect.php',
            '_tests/simpletest/test/site/protected/network_confirm.php',
            '_tests/simpletest/test/site/redirect.php',
            '_tests/simpletest/test/site/request_methods.php',
            '_tests/simpletest/test/site/savant_style_form.html',
            '_tests/simpletest/test/site/search.png',
            '_tests/simpletest/test/site/self_form.php',
            '_tests/simpletest/test/site/self.php',
            '_tests/simpletest/test/site/set_cookies.php',
            '_tests/simpletest/test/site/slow_page.php',
            '_tests/simpletest/test/site/timestamp.php',
            '_tests/simpletest/test/site/upload_form.html',
            '_tests/simpletest/test/site/upload_handler.php',
            '_tests/simpletest/test/socket_test.php',
            '_tests/simpletest/test/support/collector/collectable.1',
            '_tests/simpletest/test/support/collector/collectable.2',
            '_tests/simpletest/test/support/empty_test_file.php',
            '_tests/simpletest/test/support/failing_test.php',
            '_tests/simpletest/test/support/latin1_sample',
            '_tests/simpletest/test/support/passing_test.php',
            '_tests/simpletest/test/support/recorder_sample.php',
            '_tests/simpletest/test/support/spl_examples.php',
            '_tests/simpletest/test/support/supplementary_upload_sample.txt',
            '_tests/simpletest/test/support/test1.php',
            '_tests/simpletest/test/support/upload_sample.txt',
            '_tests/simpletest/test/tag_test.php',
            '_tests/simpletest/test/test_with_parse_error.php',
            '_tests/simpletest/test/unit_tester_test.php',
            '_tests/simpletest/test/unit_tests.php',
            '_tests/simpletest/test/url_test.php',
            '_tests/simpletest/test/user_agent_test.php',
            '_tests/simpletest/test/utf8_test.php',
            '_tests/simpletest/test/visual_test.php',
            '_tests/simpletest/test/visual/visual_errors.php',
            '_tests/simpletest/test/web_tester_test.php',
            '_tests/simpletest/test/xml_test.php',
            '_tests/simpletest/tidy_parser.php',
            '_tests/simpletest/unit_tester.php',
            '_tests/simpletest/url.php',
            '_tests/simpletest/user_agent.php',
            '_tests/simpletest/VERSION',
            '_tests/simpletest/web_tester.php',
            '_tests/simpletest/xml.php',

            '_tests/index.php',
            '_tests/sleep.php',
            '_tests/cmstest/bootstrap.php',
            '_tests/cmstest/cms_test_case.php',
            '_tests/cmstest/.htaccess',
            '_tests/cmstest/index.html',
            '_tests/libs/.htaccess',
            '_tests/libs/index.html',
            '_tests/libs/mf_parse.php',
            '_tests/tests/.htaccess',
            '_tests/tests/index.html',

            '_tests/screens_tested/.htaccess',
            '_tests/screens_tested/index.html',
            '_tests/html_dump/forums_test_set/.htaccess',
            '_tests/html_dump/forums_test_set/index.html',
            '_tests/html_dump/.htaccess',
            '_tests/html_dump/index.html',

            '_tests/codechecker/build.sh',
            '_tests/codechecker/check.php',
            '_tests/codechecker/codechecker.app/Contents/Info.plist',
            '_tests/codechecker/codechecker.app/Contents/MacOS/codechecker',
            '_tests/codechecker/codechecker.bat',
            '_tests/codechecker/codechecker.php',
            '_tests/codechecker/codechecker.sh',
            '_tests/codechecker/index.html',
            '_tests/codechecker/lex.php',
            '_tests/codechecker/lib.php',
            '_tests/codechecker/metadata.php',
            '_tests/codechecker/nbactions.xml',
            '_tests/codechecker/non_errors.txt',
            '_tests/codechecker/parse.php',
            '_tests/codechecker/phpdoc_parser.php',
            '_tests/codechecker/pom.xml',
            '_tests/codechecker/readme.txt',
            '_tests/codechecker/src/main/java/com/composrfoundation/codequalitychecker/AboutDialog.java',
            '_tests/codechecker/src/main/java/com/composrfoundation/codequalitychecker/MainDialog.java',
            '_tests/codechecker/src/main/java/com/composrfoundation/codequalitychecker/Main.java',
            '_tests/codechecker/src/main/java/com/composrfoundation/codequalitychecker/OptionsDialog.java',
            '_tests/codechecker/src/main/java/com/composrfoundation/codequalitychecker/ProcessingDialog.java',
            '_tests/codechecker/src/main/java/com/composrfoundation/codequalitychecker/SearchFile.java',
            '_tests/codechecker/src/main/java/com/composrfoundation/codequalitychecker/VerticalFlowLayout.java',
            '_tests/codechecker/target/classes/index.html',
            '_tests/codechecker/target/codequalitychecker-11.0.jar',
            '_tests/codechecker/target/maven-archiver/pom.properties',
            '_tests/codechecker/tests.php',

            '_tests/assets/images/16x9.gif',
            '_tests/assets/images/16x9.jpeg',
            '_tests/assets/images/16x9.jpg',
            '_tests/assets/images/16x9.png',
            '_tests/assets/images/18x32.gif',
            '_tests/assets/images/18x32.jpeg',
            '_tests/assets/images/18x32.jpg',
            '_tests/assets/images/18x32.png',
            '_tests/assets/images/1x1.gif',
            '_tests/assets/images/1x1.jpeg',
            '_tests/assets/images/1x1.jpg',
            '_tests/assets/images/1x1.png',
            '_tests/assets/images/2x2.gif',
            '_tests/assets/images/2x2.jpeg',
            '_tests/assets/images/2x2.jpg',
            '_tests/assets/images/2x2.png',
            '_tests/assets/images/32x18.gif',
            '_tests/assets/images/32x18.jpeg',
            '_tests/assets/images/32x18.jpg',
            '_tests/assets/images/32x18.png',
            '_tests/assets/images/3x4.gif',
            '_tests/assets/images/3x4.jpeg',
            '_tests/assets/images/3x4.jpg',
            '_tests/assets/images/3x4.png',
            '_tests/assets/images/4x3.gif',
            '_tests/assets/images/4x3.jpeg',
            '_tests/assets/images/4x3.jpg',
            '_tests/assets/images/4x3.png',
            '_tests/assets/images/6x8.gif',
            '_tests/assets/images/6x8.jpeg',
            '_tests/assets/images/6x8.jpg',
            '_tests/assets/images/6x8.png',
            '_tests/assets/images/8x6.gif',
            '_tests/assets/images/8x6.jpeg',
            '_tests/assets/images/8x6.jpg',
            '_tests/assets/images/8x6.png',
            '_tests/assets/images/9x16.gif',
            '_tests/assets/images/9x16.jpeg',
            '_tests/assets/images/9x16.jpg',
            '_tests/assets/images/9x16.png',
            '_tests/assets/images/crop_both_32x18_16x9.gif',
            '_tests/assets/images/crop_both_32x18_16x9.jpeg',
            '_tests/assets/images/crop_both_32x18_16x9.jpg',
            '_tests/assets/images/crop_both_32x18_16x9.png',
            '_tests/assets/images/crop_both_32x18_4x8.gif',
            '_tests/assets/images/crop_both_32x18_4x8.jpeg',
            '_tests/assets/images/crop_both_32x18_4x8.jpg',
            '_tests/assets/images/crop_both_32x18_4x8.png',
            '_tests/assets/images/crop_both_32x18_8x8.gif',
            '_tests/assets/images/crop_both_32x18_8x8.jpeg',
            '_tests/assets/images/crop_both_32x18_8x8.jpg',
            '_tests/assets/images/crop_both_32x18_8x8.png',
            '_tests/assets/images/crop_end_32x18_16x9.gif',
            '_tests/assets/images/crop_end_32x18_16x9.jpeg',
            '_tests/assets/images/crop_end_32x18_16x9.jpg',
            '_tests/assets/images/crop_end_32x18_16x9.png',
            '_tests/assets/images/crop_end_32x18_4x8.gif',
            '_tests/assets/images/crop_end_32x18_4x8.jpeg',
            '_tests/assets/images/crop_end_32x18_4x8.jpg',
            '_tests/assets/images/crop_end_32x18_4x8.png',
            '_tests/assets/images/crop_end_32x18_8x8.gif',
            '_tests/assets/images/crop_end_32x18_8x8.jpeg',
            '_tests/assets/images/crop_end_32x18_8x8.jpg',
            '_tests/assets/images/crop_end_32x18_8x8.png',
            '_tests/assets/images/crop_end_if_horizontal_32x18_16x9.gif',
            '_tests/assets/images/crop_end_if_horizontal_32x18_16x9.jpeg',
            '_tests/assets/images/crop_end_if_horizontal_32x18_16x9.jpg',
            '_tests/assets/images/crop_end_if_horizontal_32x18_16x9.png',
            '_tests/assets/images/crop_end_if_horizontal_32x18_4x8.gif',
            '_tests/assets/images/crop_end_if_horizontal_32x18_4x8.jpeg',
            '_tests/assets/images/crop_end_if_horizontal_32x18_4x8.jpg',
            '_tests/assets/images/crop_end_if_horizontal_32x18_4x8.png',
            '_tests/assets/images/crop_end_if_horizontal_32x18_8x8.gif',
            '_tests/assets/images/crop_end_if_horizontal_32x18_8x8.jpeg',
            '_tests/assets/images/crop_end_if_horizontal_32x18_8x8.jpg',
            '_tests/assets/images/crop_end_if_horizontal_32x18_8x8.png',
            '_tests/assets/images/crop_end_if_vertical_32x18_16x9.gif',
            '_tests/assets/images/crop_end_if_vertical_32x18_16x9.jpeg',
            '_tests/assets/images/crop_end_if_vertical_32x18_16x9.jpg',
            '_tests/assets/images/crop_end_if_vertical_32x18_16x9.png',
            '_tests/assets/images/crop_end_if_vertical_32x18_4x8.gif',
            '_tests/assets/images/crop_end_if_vertical_32x18_4x8.jpeg',
            '_tests/assets/images/crop_end_if_vertical_32x18_4x8.jpg',
            '_tests/assets/images/crop_end_if_vertical_32x18_4x8.png',
            '_tests/assets/images/crop_end_if_vertical_32x18_8x8.gif',
            '_tests/assets/images/crop_end_if_vertical_32x18_8x8.jpeg',
            '_tests/assets/images/crop_end_if_vertical_32x18_8x8.jpg',
            '_tests/assets/images/crop_end_if_vertical_32x18_8x8.png',
            '_tests/assets/images/crop_start_32x18_16x9.gif',
            '_tests/assets/images/crop_start_32x18_16x9.jpeg',
            '_tests/assets/images/crop_start_32x18_16x9.jpg',
            '_tests/assets/images/crop_start_32x18_16x9.png',
            '_tests/assets/images/crop_start_32x18_4x8.gif',
            '_tests/assets/images/crop_start_32x18_4x8.jpeg',
            '_tests/assets/images/crop_start_32x18_4x8.jpg',
            '_tests/assets/images/crop_start_32x18_4x8.png',
            '_tests/assets/images/crop_start_32x18_8x8.gif',
            '_tests/assets/images/crop_start_32x18_8x8.jpeg',
            '_tests/assets/images/crop_start_32x18_8x8.jpg',
            '_tests/assets/images/crop_start_32x18_8x8.png',
            '_tests/assets/images/crop_start_if_horizontal_32x18_16x9.gif',
            '_tests/assets/images/crop_start_if_horizontal_32x18_16x9.jpeg',
            '_tests/assets/images/crop_start_if_horizontal_32x18_16x9.jpg',
            '_tests/assets/images/crop_start_if_horizontal_32x18_16x9.png',
            '_tests/assets/images/crop_start_if_horizontal_32x18_4x8.gif',
            '_tests/assets/images/crop_start_if_horizontal_32x18_4x8.jpeg',
            '_tests/assets/images/crop_start_if_horizontal_32x18_4x8.jpg',
            '_tests/assets/images/crop_start_if_horizontal_32x18_4x8.png',
            '_tests/assets/images/crop_start_if_horizontal_32x18_8x8.gif',
            '_tests/assets/images/crop_start_if_horizontal_32x18_8x8.jpeg',
            '_tests/assets/images/crop_start_if_horizontal_32x18_8x8.jpg',
            '_tests/assets/images/crop_start_if_horizontal_32x18_8x8.png',
            '_tests/assets/images/crop_start_if_vertical_32x18_16x9.gif',
            '_tests/assets/images/crop_start_if_vertical_32x18_16x9.jpeg',
            '_tests/assets/images/crop_start_if_vertical_32x18_16x9.jpg',
            '_tests/assets/images/crop_start_if_vertical_32x18_16x9.png',
            '_tests/assets/images/crop_start_if_vertical_32x18_4x8.gif',
            '_tests/assets/images/crop_start_if_vertical_32x18_4x8.jpeg',
            '_tests/assets/images/crop_start_if_vertical_32x18_4x8.jpg',
            '_tests/assets/images/crop_start_if_vertical_32x18_4x8.png',
            '_tests/assets/images/crop_start_if_vertical_32x18_8x8.gif',
            '_tests/assets/images/crop_start_if_vertical_32x18_8x8.jpeg',
            '_tests/assets/images/crop_start_if_vertical_32x18_8x8.jpg',
            '_tests/assets/images/crop_start_if_vertical_32x18_8x8.png',
            '_tests/assets/images/exifrotated.jpg',
            '_tests/assets/images/index.html',
            '_tests/assets/images/pad_crop_both_32x18_16x9.gif',
            '_tests/assets/images/pad_crop_both_32x18_16x9.jpeg',
            '_tests/assets/images/pad_crop_both_32x18_16x9.jpg',
            '_tests/assets/images/pad_crop_both_32x18_16x9.png',
            '_tests/assets/images/quadrant.gif',
            '_tests/assets/images/quadrant.jpeg',
            '_tests/assets/images/quadrant.jpg',
            '_tests/assets/images/quadrant.png',
            '_tests/assets/images/translucent.png',
            '_tests/assets/images/transparent.gif',
            '_tests/assets/images/transparent_palette_alpha.png',
            '_tests/assets/images/transparent_palette_binary.png',
            '_tests/assets/images/transparent.png',
            '_tests/assets/images/tux.svg',
            '_tests/assets/index.html',
            '_tests/assets/media/early_cinema.mp4',
            '_tests/assets/media/index.html',
            '_tests/assets/media/sine.mp3',
            '_tests/assets/spreadsheets/index.html',
            '_tests/assets/spreadsheets/test.csv',
            '_tests/assets/spreadsheets/test.ods',
            '_tests/assets/spreadsheets/test-scsv.txt',
            '_tests/assets/spreadsheets/test-tsv.txt',
            '_tests/assets/spreadsheets/test.xlsx',
            '_tests/assets/text/index.html',
            '_tests/assets/text/iso-8859-1.txt',
            '_tests/assets/text/utf-16be.txt',
            '_tests/assets/text/utf-16.txt',
            '_tests/assets/text/utf-8.txt',
            '_tests/assets/xml/atom.cms',
            '_tests/assets/xml/rss.cms',
            '_tests/assets/xml/index.html',
            '_tests/assets/pdf_sample.pdf',
            '_tests/assets/encryption/index.html',
            '_tests/assets/encryption/private.pem',
            '_tests/assets/encryption/public.pem',

            // It is worth noting that most of these are not true unit tests, but integration tests, or other kinds of test.
            // We use the broad term 'automated test'.
            //  Pedantry aside - we just want to test as effectively and efficiently and broadly as we can ;-).
            //   Whatever we can quickly achieve as a testing win for our total platform stability/coherence, we go with.
            //   Contributions for deeper, detailed, testing, are very welcome.
            '_tests/tests/regression_tests/.htaccess',
            '_tests/tests/regression_tests/index.html',
            '_tests/tests/regression_tests/input_filter_post_block.php',
            '_tests/tests/unit_tests/_api_transifex.php',
            '_tests/tests/unit_tests/curl_wrapper.php',
            '_tests/tests/unit_tests/_actionlog.php',
            '_tests/tests/unit_tests/addon_dependency_naming.php',
            '_tests/tests/unit_tests/addon_guards.php',
            '_tests/tests/unit_tests/addon_hook_quality.php',
            '_tests/tests/unit_tests/addon_references.php',
            '_tests/tests/unit_tests/addon_screenshots.php',
            '_tests/tests/unit_tests/addon_setupwizard.php',
            '_tests/tests/unit_tests/adminzone_search.php',
            '_tests/tests/unit_tests/_protocol_imap.php',
            '_tests/tests/unit_tests/_api_confluence.php',
            '_tests/tests/unit_tests/allow_php_in_templates.php',
            '_tests/tests/unit_tests/antispam.php',
            '_tests/tests/unit_tests/api_classes_documented.php',
            '_tests/tests/unit_tests/authors.php',
            '_tests/tests/unit_tests/auth.php',
            '_tests/tests/unit_tests/awards.php',
            '_tests/tests/unit_tests/aws_ses.php',
            '_tests/tests/unit_tests/__backups.php',
            '_tests/tests/unit_tests/banners.php',
            '_tests/tests/unit_tests/___bash_parser.php',
            '_tests/tests/unit_tests/basic_code_formatting.php',
            '_tests/tests/unit_tests/___blob_slowdown.php',
            '_tests/tests/unit_tests/blocks.php',
            '_tests/tests/unit_tests/bot_detection.php',
            '_tests/tests/unit_tests/broken_includes.php',
            '_tests/tests/unit_tests/__broken_links.php',
            '_tests/tests/unit_tests/browser_upgrade_suggest.php',
            '_tests/tests/unit_tests/bump_member_group_timeout.php',
            '_tests/tests/unit_tests/calendar_events.php',
            '_tests/tests/unit_tests/calendar_event_types.php',
            '_tests/tests/unit_tests/catalogues_categories.php',
            '_tests/tests/unit_tests/catalogues.php',
            '_tests/tests/unit_tests/cdn_config.php',
            '_tests/tests/unit_tests/character_sets.php',
            '_tests/tests/unit_tests/chatrooms.php',
            '_tests/tests/unit_tests/chmod_consistency.php',
            '_tests/tests/unit_tests/clean_reinstall.php',
            '_tests/tests/unit_tests/closed_file.php',
            '_tests/tests/unit_tests/cloudflare_ip_range_sync.php',
            '_tests/tests/unit_tests/_api_cloudinary.php',
            '_tests/tests/unit_tests/catalogues_module.php',
            '_tests/tests/unit_tests/cma_hooks.php',
            '_tests/tests/unit_tests/cms_merge.php',
            '_tests/tests/unit_tests/comcode_code.php',
            '_tests/tests/unit_tests/__do_lang_tempcode_escaping.php',
            '_tests/tests/unit_tests/comcode_from_html.php',
            '_tests/tests/unit_tests/comcode_pages.php',
            '_tests/tests/unit_tests/comcode.php',
            '_tests/tests/unit_tests/comcode_to_text.php',
            '_tests/tests/unit_tests/comcode_wysiwyg.php',
            '_tests/tests/unit_tests/commandr_command_lang_strings.php',
            '_tests/tests/unit_tests/_commandr_fs.php',
            '_tests/tests/unit_tests/comment_encapsulation.php',
            '_tests/tests/unit_tests/comments.php',
            '_tests/tests/unit_tests/js_file.php',
            '_tests/tests/unit_tests/_api_moz.php',
            '_tests/tests/unit_tests/community_billboard.php',
            '_tests/tests/unit_tests/config_lang_strings.php',
            '_tests/tests/unit_tests/config_options_in_templates.php',
            '_tests/tests/unit_tests/config.php',
            '_tests/tests/unit_tests/_api_google_search_console.php',
            '_tests/tests/unit_tests/_copyright.php',
            '_tests/tests/unit_tests/themewizard_colours.php',
            '_tests/tests/unit_tests/ids.php',
            '_tests/tests/unit_tests/core_fields.php',
            '_tests/tests/unit_tests/_api_google_safe_browsing.php',
            '_tests/tests/unit_tests/___timezones.php',
            '_tests/tests/unit_tests/permission_modules.php',
            '_tests/tests/unit_tests/cpfs.php',
            '_tests/tests/unit_tests/cqc_adminzone.php',
            '_tests/tests/unit_tests/cqc_blocks.php',
            '_tests/tests/unit_tests/cqc_cms.php',
            '_tests/tests/unit_tests/cqc_cns.php',
            '_tests/tests/unit_tests/cqc_database.php',
            '_tests/tests/unit_tests/cqc__explicit_fail.php',
            '_tests/tests/unit_tests/cqc_forumdrivers.php',
            '_tests/tests/unit_tests/cqc_forum.php',
            '_tests/tests/unit_tests/cqc__function_sigs.php',
            '_tests/tests/unit_tests/cqc_hooks.php',
            '_tests/tests/unit_tests/_cqc_nonbundled.php',
            '_tests/tests/unit_tests/cqc_persistent_caching.php',
            '_tests/tests/unit_tests/cqc_rest.php',
            '_tests/tests/unit_tests/cqc_site.php',
            '_tests/tests/unit_tests/cqc_sources.php',
            '_tests/tests/unit_tests/cqc_welcome.php',
            '_tests/tests/unit_tests/phpbb_post_parser.php',
            '_tests/tests/unit_tests/__critical_error_display.php',
            '_tests/tests/unit_tests/crypt.php',
            '_tests/tests/unit_tests/csrf_tags.php',
            '_tests/tests/unit_tests/css_beta.php',
            '_tests/tests/unit_tests/css_file.php',
            '_tests/tests/unit_tests/_api_currency.php',
            '_tests/tests/unit_tests/___database_integrity.php',
            '_tests/tests/unit_tests/database_misc.php',
            '_tests/tests/unit_tests/database_query_parameterised.php',
            '_tests/tests/unit_tests/database_relations.php',
            '_tests/tests/unit_tests/db_correctness.php',
            '_tests/tests/unit_tests/_eslint.php',
            '_tests/tests/unit_tests/___demonstratr.php',
            '_tests/tests/unit_tests/dev_environment.php',
            '_tests/tests/unit_tests/diff.php',
            '_tests/tests/unit_tests/dns.php',
            '_tests/tests/unit_tests/guids.php',
            '_tests/tests/unit_tests/file_security.php',
            '_tests/tests/unit_tests/download_indexing.php',
            '_tests/tests/unit_tests/downloads_categories.php',
            '_tests/tests/unit_tests/downloads_http_cycle.php',
            '_tests/tests/unit_tests/downloads.php',
            '_tests/tests/unit_tests/ecommerce_custom.php',
            '_tests/tests/unit_tests/ecommerce_shipping.php',
            '_tests/tests/unit_tests/_api_ecommerce_tax.php',
            '_tests/tests/unit_tests/js_icon_use.php',
            '_tests/tests/unit_tests/comma_lists.php',
            '_tests/tests/unit_tests/emoticons.php',
            '_tests/tests/unit_tests/env_vars.php',
            '_tests/tests/unit_tests/__extra_logging.php',
            '_tests/tests/unit_tests/_feeds_and_podcasts.php',
            '_tests/tests/unit_tests/file_naming.php',
            '_tests/tests/unit_tests/file_type_safelisting.php',
            '_tests/tests/unit_tests/__filtering.php',
            '_tests/tests/unit_tests/_filter_xml.php',
            '_tests/tests/unit_tests/_api_ecommerce_shipping.php',
            '_tests/tests/unit_tests/find_broken_screen_links.php',
            '_tests/tests/unit_tests/firephp.php',
            '_tests/tests/unit_tests/___form_to_email.php',
            '_tests/tests/unit_tests/forum_drivers.php',
            '_tests/tests/unit_tests/forum_groupings.php',
            '_tests/tests/unit_tests/forum_polls.php',
            '_tests/tests/unit_tests/forums.php',
            '_tests/tests/unit_tests/galleries.php',
            '_tests/tests/unit_tests/gallery_images.php',
            '_tests/tests/unit_tests/gallery_media_defaults.php',
            '_tests/tests/unit_tests/_api_geocoding.php',
            '_tests/tests/unit_tests/geshi.php',
            '_tests/tests/unit_tests/getid3.php',
            '_tests/tests/unit_tests/params.php',
            '_tests/tests/unit_tests/path_references.php',
            '_tests/tests/unit_tests/git_conflicts.php',
            '_tests/tests/unit_tests/suphp.php',
            '_tests/tests/unit_tests/glossary.php',
            '_tests/tests/unit_tests/google_appengine.php',
            '_tests/tests/unit_tests/___health_check.php',
            '_tests/tests/unit_tests/.htaccess',
            '_tests/tests/unit_tests/disk_usage_spec.php',
            '_tests/tests/unit_tests/httpauth.php',
            '_tests/tests/unit_tests/http_obscure_cases.php',
            '_tests/tests/unit_tests/___api_twitter.php',
            '_tests/tests/unit_tests/http.php',
            '_tests/tests/unit_tests/_http_timeouts.php',
            '_tests/tests/unit_tests/hyperlink_targets.php',
            '_tests/tests/unit_tests/image_compression.php',
            '_tests/tests/unit_tests/_images.php',
            '_tests/tests/unit_tests/index.html',
            '_tests/tests/unit_tests/__installer_forum_drivers.php',
            '_tests/tests/unit_tests/__installer.php',
            '_tests/tests/unit_tests/__installer_xml_db.php',
            '_tests/tests/unit_tests/_api_ipstack.php',
            '_tests/tests/unit_tests/js_lang_references.php',
            '_tests/tests/unit_tests/js_standards.php',
            '_tests/tests/unit_tests/js_strict_mode.php',
            '_tests/tests/unit_tests/gravatars.php',
            '_tests/tests/unit_tests/lang_api.php',
            '_tests/tests/unit_tests/lang_administrative_split.php',
            '_tests/tests/unit_tests/lang_descriptions.php',
            '_tests/tests/unit_tests/lang_duplication.php',
            '_tests/tests/unit_tests/lang_grammar.php',
            '_tests/tests/unit_tests/lang_html_safe.php',
            '_tests/tests/unit_tests/lang_ini_size.php',
            '_tests/tests/unit_tests/lang_inline_editing.php',
            '_tests/tests/unit_tests/lang_misc.php',
            '_tests/tests/unit_tests/_lang_no_unused.php',
            '_tests/tests/unit_tests/_lang_spelling_epic.php',
            '_tests/tests/unit_tests/lang_spelling.php',
            '_tests/tests/unit_tests/lang_stemmer.php',
            '_tests/tests/unit_tests/lang_string_special_validity.php',
            '_tests/tests/unit_tests/log_refs.php',
            '_tests/tests/unit_tests/template_previews_meta.php',
            '_tests/tests/unit_tests/mail.php',
            '_tests/tests/unit_tests/maintenance_codes.php',
            '_tests/tests/unit_tests/media.php',
            '_tests/tests/unit_tests/member_banning.php',
            '_tests/tests/unit_tests/members.php',
            '_tests/tests/unit_tests/menus.php',
            '_tests/tests/unit_tests/microformats.php',
            '_tests/tests/unit_tests/lang_tokeniser.php',
            '_tests/tests/unit_tests/missing_block_params.php',
            '_tests/tests/unit_tests/missing_colour_equations.php',
            '_tests/tests/unit_tests/aaa_modularisation.php',
            '_tests/tests/unit_tests/module_names_defined.php',
            '_tests/tests/unit_tests/multi_moderations.php',
            '_tests/tests/unit_tests/news_categories.php',
            '_tests/tests/unit_tests/_newsletters.php',
            '_tests/tests/unit_tests/newsletters.php',
            '_tests/tests/unit_tests/news.php',
            '_tests/tests/unit_tests/notification_classifications.php',
            '_tests/tests/unit_tests/notifications.php',
            '_tests/tests/unit_tests/_oembed.php',
            '_tests/tests/unit_tests/optimisations_fragile.php',
            '_tests/tests/unit_tests/override_issues.php',
            '_tests/tests/unit_tests/override_notes_consistency.php',
            '_tests/tests/unit_tests/overused_globals.php',
            '_tests/tests/unit_tests/password_censor.php',
            '_tests/tests/unit_tests/password_strength.php',
            '_tests/tests/unit_tests/___performance.php',
            '_tests/tests/unit_tests/permissions.php',
            '_tests/tests/unit_tests/persistent_cache.php',
            '_tests/tests/unit_tests/ip_bans.php',
            '_tests/tests/unit_tests/temporal.php',
            '_tests/tests/unit_tests/phpdoc.php',
            '_tests/tests/unit_tests/phpstub_accuracy.php',
            '_tests/tests/unit_tests/php_versioning.php',
            '_tests/tests/unit_tests/points.php',
            '_tests/tests/unit_tests/polls.php',
            '_tests/tests/unit_tests/posts.php',
            '_tests/tests/unit_tests/post_templates.php',
            '_tests/tests/unit_tests/privacy_hooks.php',
            '_tests/tests/unit_tests/privilege_existence.php',
            '_tests/tests/unit_tests/quizzes.php',
            '_tests/tests/unit_tests/__rate_limiting.php',
            '_tests/tests/unit_tests/rating.php',
            '_tests/tests/unit_tests/resource_closing.php',
            '_tests/tests/unit_tests/___resource_fs.php',
            '_tests/tests/unit_tests/rest.php',
            '_tests/tests/unit_tests/rootkit_detection.php',
            '_tests/tests/unit_tests/rss.php',
            '_tests/tests/unit_tests/___search.php',
            '_tests/tests/unit_tests/search.php',
            '_tests/tests/unit_tests/sensible_git_branches.php',
            '_tests/tests/unit_tests/seo.php',
            '_tests/tests/unit_tests/_setupwizard.php',
            '_tests/tests/unit_tests/shopping_order_management.php',
            '_tests/tests/unit_tests/shopping.php',
            '_tests/tests/unit_tests/should_ignore_file.php',
            '_tests/tests/unit_tests/__sitemap.php',
            '_tests/tests/unit_tests/sitemap_submit.php',
            '_tests/tests/unit_tests/sorting.php',
            '_tests/tests/unit_tests/_special_links.php',
            '_tests/tests/unit_tests/__specsettings_documented.php',
            '_tests/tests/unit_tests/spreadsheets.php',
            '_tests/tests/unit_tests/form_reserved_names.php',
            '_tests/tests/unit_tests/sql_compat.php',
            '_tests/tests/unit_tests/standard_dir_files.php',
            '_tests/tests/unit_tests/___static_caching.php',
            '_tests/tests/unit_tests/strip_tags.php',
            '_tests/tests/unit_tests/tar.php',
            '_tests/tests/unit_tests/_tasks.php',
            '_tests/tests/unit_tests/database_unsupported_sql.php',
            '_tests/tests/unit_tests/tempcode_errors.php',
            '_tests/tests/unit_tests/tempcode_mistakes.php',
            '_tests/tests/unit_tests/tempcode.php',
            '_tests/tests/unit_tests/template_no_unused.php',
            '_tests/tests/unit_tests/template_parameter_consistency.php',
            '_tests/tests/unit_tests/_template_previews.php',
            '_tests/tests/unit_tests/templates.php',
            '_tests/tests/unit_tests/template_xss.php',
            '_tests/tests/unit_tests/theme_images.php',
            '_tests/tests/unit_tests/themeini_images.php',
            '_tests/tests/unit_tests/___third_party_code.php',
            '_tests/tests/unit_tests/ticket_types.php',
            '_tests/tests/unit_tests/_tracker_categories.php',
            '_tests/tests/unit_tests/_api_translation.php',
            '_tests/tests/unit_tests/transliteration.php',
            '_tests/tests/unit_tests/encryption.php',
            '_tests/tests/unit_tests/simulated_wildcard_match.php',
            '_tests/tests/unit_tests/tutorial_image_consistency.php',
            '_tests/tests/unit_tests/tutorial_nav_paths.php',
            '_tests/tests/unit_tests/_tutorial_quality.php',
            '_tests/tests/unit_tests/tutorial_quality.php',
            '_tests/tests/unit_tests/tutorials_all_linked.php',
            '_tests/tests/unit_tests/tutorials_broken_links.php',
            '_tests/tests/unit_tests/tutorials_codebox.php',
            '_tests/tests/unit_tests/tutorial_title_structure.php',
            '_tests/tests/unit_tests/ip_addresses.php',
            '_tests/tests/unit_tests/type_sanitisation.php',
            '_tests/tests/unit_tests/ua_detection.php',
            '_tests/tests/unit_tests/unpack.php',
            '_tests/tests/unit_tests/upload_directory.php',
            '_tests/tests/unit_tests/url_management.php',
            '_tests/tests/unit_tests/url_monikers.php',
            '_tests/tests/unit_tests/urls_simplifier.php',
            '_tests/tests/unit_tests/us_english.php',
            '_tests/tests/unit_tests/scripts.php',
            '_tests/tests/unit_tests/usergroup_subscriptions.php',
            '_tests/tests/unit_tests/versioning.php',
            '_tests/tests/unit_tests/warnings.php',
            '_tests/tests/unit_tests/_api_weather.php',
            '_tests/tests/unit_tests/webdav.php',
            '_tests/tests/unit_tests/web_platform.php',
            '_tests/tests/unit_tests/_web_resources.php',
            '_tests/tests/unit_tests/webstandards.php',
            '_tests/tests/unit_tests/welcome_emails.php',
            '_tests/tests/unit_tests/wiki.php',
            '_tests/tests/unit_tests/xhtml_substr.php',
            '_tests/tests/unit_tests/xml_db.php',
            '_tests/tests/unit_tests/xml_sitemaps.php',
            '_tests/tests/unit_tests/xss.php',
            '_tests/tests/unit_tests/zip.php',
            '_tests/tests/unit_tests/string_functions.php',
            '_tests/tests/unit_tests/lang_missing_parameters.php',
            '_tests/tests/unit_tests/__leader_board.php',
            '_tests/tests/unit_tests/mobile_detect.php',
            '_tests/tests/unit_tests/new_window_labels.php',
            '_tests/tests/unit_tests/import.php',
            '_tests/tests/unit_tests/firewall_rules.php',
            '_tests/tests/unit_tests/hooks.php',
            'data_custom/search_test.php',
        ];
    }
}
