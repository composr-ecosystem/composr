<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    meta_toolkit
 */

// This code is used to find code in our ecosystem that we should not touch with regular Composr processes.
//  We don't count it in line counts, don't do automatic reformatting, don't do full CQC scans.

function list_untouchable_third_party_directories()
{
    return [
        'caches',
        'data/ace',
        'data/ckeditor',
        'data/ckeditor/plugins/codemirror',
        'data_custom/ckeditor',
        'data_custom/pdf_viewer',
        'data/polyfills',
        'docs/api',
        'docs/composr-api-template',
        'docs/jsdoc',
        'exports',
        'imports',
        'mobiquo/lib',
        'mobiquo/smartbanner',
        'nbproject',
        '_old',
        //'sources/diff', We maintain this now
        'sources_custom/aws_ses',
        'sources_custom/Cloudinary',
        'sources_custom/composr_mobile_sdk',
        'sources_custom/imap',
        'sources_custom/geshi',
        'sources_custom/getid3',
        'sources_custom/programe',
        'sources_custom/sabredav',
        'sources_custom/spout',
        'sources_custom/swift_mailer',
        'sources_custom/Transliterator',
        'sources_custom/hybridauth',
        'temp',
        '_tests/assets',
        '_tests/codechecker',
        '_tests/html_dump',
        '_tests/screens_tested',
        '_tests/simpletest',
        'themes/admin/templates_cached/EN',
        'themes/default/templates_cached/EN',
        'themes/_unnamed_/templates_cached/EN',
        'tracker',
        'uploads/website_specific/test',
        'vendor',
    ];
}

function list_untouchable_third_party_files()
{
    return [
        //'sources/crc24.php', We maintain this now
        '_config.php',
        'aps/test/TEST-META.xml',
        'aps/test/composrIDEtest.xml',
        'data/curl-ca-bundle.crt',
        'data_custom/errorlog.php',
        'data_custom/execute_temp.php',
        'data_custom/webfonts/adgs-icons.svg',
        'data/modules/admin_stats/IP_Country.txt',
        'install.sql',
        'mobiquo/lib/xmlrpc.php',
        'mobiquo/lib/xmlrpcs.php',
        'mobiquo/license_agreement.txt',
        'mobiquo/smartbanner/appbanner.css',
        'mobiquo/smartbanner/appbanner.js',
        'mobiquo/tapatalkdetect.js',
        'sources_custom/browser_detect.php',
        'sources_custom/curl.php',
        'sources_custom/geshi.php',
        'sources_custom/hooks/modules/chat_bots/trickstr.php',
        'sources_custom/sugar_crm_lib.php',
        'sources_custom/twitter.php',
        'sources/firephp.php',
        //'sources/m_zip.php', We maintain this now
        //'sources/jsmin.php', We maintain this now
        //'sources/lang_stemmer_EN.php', We maintain this now
        'sources/mail_dkim.php',
        '_tests/codechecker/codechecker.ini',
        '_tests/codechecker/nbactions.xml',
        '_tests/codechecker/pom.xml',
        '_tests/libs/mf_parse.php',
        'themes/default/css_custom/columns.css',
        'themes/default/css_custom/confluence.css',
        'themes/default/css_custom/flip.css',
        'themes/default/css_custom/google_search.css',
        'themes/default/css/skitter.css',
        'themes/default/css_custom/sortable_tables.css',
        'themes/default/css_custom/unslider.css',
        'themes/default/css/mediaelementplayer.css',
        'themes/default/css/jquery_ui.css',
        'themes/default/css/widget_color.css',
        'themes/default/css/widget_plupload.css',
        'themes/default/css/widget_select2.css',
        'themes/default/css/widget_date.css',
        'themes/default/css/toastify.css',
        'themes/default/javascript/charts.js',
        'themes/default/javascript_custom/columns.js',
        'themes/default/javascript_custom/confluence2.js',
        'themes/default/javascript_custom/confluence.js',
        'themes/default/javascript_custom/jquery_flip.js',
        'themes/default/javascript/skitter.js',
        'themes/default/javascript_custom/sortable_tables.js',
        'themes/default/javascript_custom/tag_cloud.js',
        'themes/default/javascript_custom/unslider.js',
        'themes/default/javascript/glide.js',
        'themes/default/javascript/jquery.js',
        'themes/default/javascript/jquery_ui.js',
        'themes/default/javascript/webfontloader.js',
        'themes/default/javascript/_json5.js',
        'themes/default/javascript/masonry.js',
        'themes/default/javascript/mediaelement-and-player.js',
        'themes/default/javascript/modernizr.js',
        'themes/default/javascript/plupload.js',
        'themes/default/javascript/select2.js',
        'themes/default/javascript/sound.js',
        'themes/default/javascript/toastify.js',
        'themes/default/javascript/widget_color.js',
        'themes/default/javascript/widget_date.js',
        'themes/default/javascript/xsl_mopup.js',
        'themes/default/javascript/cookie_consent.js',
        'themes/default/javascript/_polyfill_fetch.js',
        'themes/default/javascript/_polyfill_general.js',
        'themes/default/javascript/_polyfill_keyboardevent_key.js',
        'themes/default/javascript/_polyfill_url.js',
        'themes/default/javascript/_polyfill_web_animations.js',
        'themes/default/javascript/jquery_autocomplete.js',
        'themes/default/css/toastify.css',
    ];
}
