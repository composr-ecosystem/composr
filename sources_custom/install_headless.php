<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    meta_toolkit
 */

function do_install_to($database, $username, $password, $table_prefix, $safe_mode, $forum_driver = 'cns', $board_path = null, $forum_base_url = null, $database_forums = null, $username_forums = null, $password_forums = null, $extra_settings = [], $do_index_test = true, $db_type = null)
{
    // Most Composr MySQL drivers auto-create the DB if missing, if root, but mysql_pdo does not because of how the connection works
    if (strpos(get_db_type(), 'mysql') !== false) {
        if (get_db_site_user() == 'root') {
            $GLOBALS['SITE_DB']->query('CREATE DATABASE IF NOT EXISTS ' . $database, null, 0, true);
        } else if ($username == 'root') {
            $db = new DatabaseConnector(get_db_site(), get_db_site_host(), $username, $password, $table_prefix);
            $db->query('CREATE DATABASE IF NOT EXISTS ' . $database, null, 0, true);
            unset($db);
        }
    }

    copy(get_file_base() . '/_config.php', get_file_base() . '/_config.php.bak');
    fix_permissions(get_file_base() . '/_config.php.bak');

    $success = _do_install_to($database, $username, $password, $table_prefix, $safe_mode, $forum_driver, $board_path, $forum_base_url, $database_forums, $username_forums, $password_forums, $extra_settings, $db_type);

    if ($success && $do_index_test) {
        $url = get_base_url() . '/index.php?keep_query_limit=0';
        $http_result = cms_http_request($url, ['convert_to_internal_encoding' => true, 'ignore_http_status' => true, 'trigger_error' => false, 'timeout' => ($db_type == 'xml') ? 200.0 : 30.0]);
        $data = $http_result->data;
        $success = (in_array($http_result->message, ['200', '503'/*site closed*/])) && (strpos($data, '<!--ERROR-->') === false);

        if (/*(!$success) && */(isset($_GET['debug']))) {
            @var_dump($url);
            @var_dump($http_result->message);
            @var_dump($http_result->message_b);
            $error = clean_installer_output_for_code_display($data);
            @var_dump(escape_html($error));
            cms_flush_safe();

            if ((!$success) && (isset($_GET['debug']))) {
                exit('Exiting early due to error on home page test');
            }
        }
    }

    @unlink(get_file_base() . '/_config.php');
    @rename(get_file_base() . '/_config.php.bak', get_file_base() . '/_config.php');

    return $success;
}

function _do_install_to($database, $username, $password, $table_prefix, $safe_mode, $forum_driver, $board_path, $forum_base_url, $database_forums, $username_forums, $password_forums, $extra_settings, $db_type)
{
    if ($db_type === null) {
        $db_type = get_db_type();
    }
    if ($board_path === null) {
        $board_path = get_file_base() . '/forums';
    }
    if ($database_forums === null) {
        $database_forums = $database;
    }
    if ($username_forums === null) {
        $username_forums = $username;
    }
    if ($password_forums === null) {
        $password_forums = $password;
    }

    $settings = $extra_settings + [
        'max' => '1000',
        'default_lang' => fallback_lang(),
        'email' => 'foo@example.com',
        'advertise_on' => '0',
        'use_multi_db' => '0',
        'use_msn' => '0',
        'db_type' => $db_type,
        'forum_type' => $forum_driver,
        'board_path' => $board_path,
        'forum_base_url' => $forum_base_url,
        'domain' => get_domain(),
        'base_url' => get_base_url(),
        'table_prefix' => $table_prefix,
        'cns_table_prefix' => $table_prefix,
        'master_password' => '',
        'master_password_confirm' => '',
        'send_error_emails_ocproducts' => '1',
        'admin_username' => 'admin',
        'cns_admin_password' => '',
        'cns_admin_password_confirm' => '',
        'clear_existing_forums_on_install' => 'yes',
        'db_site' => $database,
        'db_site_host' => get_db_site_host(),
        'db_site_user' => $username,
        'db_site_password' => $password,
        'user_cookie' => 'cms_member_id',
        'pass_cookie' => 'cms_member_hash',
        'cookie_domain' => '',
        'cookie_path' => '/',
        'cookie_days' => '1825',
        'db_forums' => $database_forums,
        'db_forums_host' => get_db_site_host(),
        'db_forums_user' => $username_forums,
        'db_forums_password' => $password_forums,
        'multi_lang_content' => '0',
        'self_learning_cache' => '0',
        'confirm' => '1',
    ];

    $stages = [
        [
            [],
            [],
        ],

        [
            [
                'step' => '2',
            ],
            $settings,
        ],

        [
            [
                'step' => '3',
            ],
            $settings,
        ],

        [
            [
                'step' => '4',
            ],
            $settings,
        ],

        [
            [
                'step' => '5',
            ],
            $settings,
        ],

        [
            [
                'step' => '6',
            ],
            $settings,
        ],

        [
            [
                'step' => '7',
            ],
            $settings,
        ],

        [
            [
                'step' => '8',
            ],
            $settings,
        ],

        [
            [
                'step' => '9',
            ],
            $settings,
        ],

        [
            [
                'step' => '10',
            ],
            $settings,
        ],
    ];

    foreach ($stages as $stage) {
        list($get, $post) = $stage;
        $url = get_base_url() . '/install.php?keep_safe_mode=' . ($safe_mode ? '1' : '0');
        if (!empty($get)) {
            $url .= '&' . http_build_query($get);
        }
        $http_result = cms_http_request($url, ['convert_to_internal_encoding' => true, 'post_params' => $post, 'ignore_http_status' => true, 'trigger_error' => false, 'timeout' => ($db_type == 'xml') ? 240.0 : 50.0]);
        $data = $http_result->data;
        $success = (in_array($http_result->message, ['200'])) && (strpos($data, '<!--ERROR-->') === false);

        if (/*(!$success) && */(isset($_GET['debug']))) {
            @var_dump($url);
            @var_dump($http_result->message);
            @var_dump($http_result->message_b);
            $error = clean_installer_output_for_code_display($data);
            @var_dump(escape_html($error));
            cms_flush_safe();

            if ((!$success) && (isset($_GET['debug']))) {
                exit('Exiting early due to error on ' . json_encode($stage));
            }
        }

        if (!$success) {
            return false; // Don't keep installing if there's an error
        }
    }

    return true;
}

function clean_installer_output_for_code_display($data)
{
    $data = preg_replace('#<script.*</script>#Us', '', $data);
    $data = preg_replace('#^.*An error has occurred#s', 'An error has occurred', $data);
    $data = str_replace('>', ">\n", $data);
    $data = strip_tags($data);
    $data = cms_preg_replace_safe('#(\s*\n\s*)+#', "\n", $data);
    return $data;
}
