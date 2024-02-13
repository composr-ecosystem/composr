<?php /*

 Composr
 Copyright (c) Christopher Graham/Tapatalk, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cns_tapatalk
 */

/**
 * Standard Tapatalk endpoint test.
 * Never use on a live site.
 * Call via, and works with, our API in test_functions.php.
 */
function search_topic_test()
{
    $result = mobiquo_xmlrpc_simple_call(
        preg_replace('#_test$#', '', __FUNCTION__),
        [
            '%Ggfd',
            0,
            5,
        ]
    );
    print_mobiquo_result($result);
}

/**
 * Standard Tapatalk endpoint test.
 * Never use on a live site.
 * Call via, and works with, our API in test_functions.php.
 */
function search_post_test()
{
    $result = mobiquo_xmlrpc_simple_call(
        preg_replace('#_test$#', '', __FUNCTION__),
        [
            '%fgddf',
            0,
            8,
        ]
    );
    print_mobiquo_result($result);
}

/**
 * Standard Tapatalk endpoint test.
 * Never use on a live site.
 * Call via, and works with, our API in test_functions.php.
 */
function search_test()
{
    $result = mobiquo_xmlrpc_simple_call(
        preg_replace('#_test$#', '', __FUNCTION__),
        [
            [
                'page' => 1,
                'perpage' => 10,
                'keywords' => '%fgddf',
                //'userid' => '3',
                'searchuser' => '%admin',
                //'forumid' => '2',
                //'threadid' => '3',
                'titleonly' => 0,
                'showposts' => 1,
                'searchtime' => 60 * 60 * 24 * 365 * 2, // 2 years
            ]
        ]
    );
    print_mobiquo_result($result);
}
