<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_code('developer_tools');
destrictify();

require_code('facebook/facebook');
require_lang('facebook');

$title = get_screen_title('FACEBOOK_OAUTH');

$facebook_appid = get_option('facebook_appid');

if ($facebook_appid == '') {
    $config_url = build_url(array('page' => 'admin_config', 'type' => 'category', 'id' => 'COMPOSR_APIS', 'redirect' => get_self_url(true)), get_module_zone('admin_config'), null, false, false, false, 'group_FACEBOOK_SYNDICATION');
    $echo = redirect_screen($title, $config_url, do_lang_tempcode('FACEBOOK_SETUP_FIRST'));
    $echo->evaluate_echo();
    return;
}

require_code('hooks/systems/syndication/facebook');
$ob = new Hook_syndication_facebook();

$result = $ob->auth_set(null, get_self_url(false, false, array('oauth_in_progress' => 1)));

if ($result) {
    $out = do_lang_tempcode('FACEBOOK_OAUTH_SUCCESS');
} else {
    $out = do_lang_tempcode('SOME_ERRORS_OCCURRED');
}

$title->evaluate_echo();

$out->evaluate_echo();
