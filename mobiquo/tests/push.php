<?php /*

 Composr
 Copyright (c) ocProducts/Tapatalk, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Tapatalk test.
 * Never use on a live site.
 */
function push_test()
{
    $topic_id = 3;
    $subject = 'Subject';
    $body = '[quote="test"]Quoting test[/quote] and a mention for @test ' . uniqid('', false);

    require_lang('cns');
    require_code('cns_posts_action');
    cns_make_post($topic_id, $subject, $body);
}

/**
 * Standard Tapatalk endpoint test.
 * Never use on a live site.
 * Call via, and works with, our API in test_functions.php.
 */
function reset_push_slug_test()
{
    $result = mobiquo_post_simple_call(
        preg_replace('#_test$#', '', __FUNCTION__),
        [
            'code' => '456',
        ]
    );
    print_mobiquo_result($result);
}

/**
 * Standard Tapatalk endpoint test.
 * Never use on a live site.
 * Call via, and works with, our API in test_functions.php.
 */
function push_content_check_test()
{
    $result = mobiquo_post_simple_call(
        preg_replace('#_test$#', '', __FUNCTION__),
        [
            'code' => '456',
            'format' => 'serialize',
            'data' => serialize([
                'type' => 'newtopic',

                // For posts
                'id' => '39',
                'subid' => '71',
                'author' => 'admin',
                'authorid' => '2',

                // For PMs
                //'mid' => 123,
            ]),
        ]
    );
    print_mobiquo_result($result);
}
