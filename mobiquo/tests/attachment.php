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
function upload_attach_test()
{
    $result = mobiquo_post_simple_call(
        preg_replace('#_test$#', '', __FUNCTION__),
        [
            'method_name' => 'upload_attach',
        ],
        'admin',
        ['content[]' => get_file_base() . '/themes/default/images/cns_default_avatars/default_set/cartoons/crazy.jpg']
    );
    print_mobiquo_result($result);
}

/**
 * Standard Tapatalk endpoint test.
 * Never use on a live site.
 * Call via, and works with, our API in test_functions.php.
 */
function upload_avatar_test()
{
    $result = mobiquo_post_simple_call(
        preg_replace('#_test$#', '', __FUNCTION__),
        [
            'method_name' => 'upload_avatar',
        ],
        'admin',
        ['content[]' => get_file_base() . '/themes/default/images/cns_default_avatars/default_set/cartoons/crazy.jpg']
    );
    print_mobiquo_result($result);
}

/**
 * Standard Tapatalk endpoint test.
 * Never use on a live site.
 * Call via, and works with, our API in test_functions.php.
 */
function remove_attachment_test()
{
    $result = mobiquo_xmlrpc_simple_call(
        preg_replace('#_test$#', '', __FUNCTION__),
        [
            '34',
            '',
            '',
        ],
        'admin' // Log in as admin. Only works with a backdoored IP
    );
    print_mobiquo_result($result);
}
