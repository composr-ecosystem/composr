<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    browser_detect
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('browser_detect')) {
    return do_template('RED_ALERT', ['_GUID' => 'mtnxujz7y0ldjtbsm14b370oe4y2j3th', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('browser_detect'))]);
}

$ie_needed = array_key_exists('ie_needed', $map) ? floatval($map['ie_needed']) : 11.0;
$edge_needed = array_key_exists('edge_needed', $map) ? floatval($map['edge_needed']) : 1.0;
$firefox_needed = array_key_exists('firefox_needed', $map) ? floatval($map['firefox_needed']) : 8.0;
$safari_needed = array_key_exists('safari_needed', $map) ? floatval($map['safari_needed']) : 5.0;
$chrome_needed = array_key_exists('chrome_needed', $map) ? floatval($map['chrome_needed']) : 15.0;

$attach = (array_key_exists('attach', $map)) && ($map['attach'] == '1');

require_code('browser_detect');
require_lang('browser_upgrade_suggest');

$message = '';

$browser = new Browser();
if (($browser->getBrowser() == Browser::BROWSER_IE) && (floatval($browser->getVersion()) < $ie_needed)) {
    switch (floatval($browser->getVersion())) {
        case 11.0:
            $year = '2013';
            break;
        case 10.0:
            $year = '2012';
            break;
        case 9.0:
            $year = '2010';
            break;
        case 8.0:
            $year = '2009';
            break;
        case 7.0:
            $year = '2007';
            break;
        case 6.0:
            $year = '2001';
            break;
        default:
            $year = 'pre-2001';
            break;
    }
    $message = do_lang('UPGRADE_IE', escape_html($year));
}
if (($browser->getBrowser() == Browser::BROWSER_EDGE) && (floatval($browser->getVersion()) < $edge_needed)) {
    $message = do_lang('UPGRADE_EDGE');
}
if (($browser->getBrowser() == Browser::BROWSER_FIREFOX) && (floatval($browser->getVersion()) < $firefox_needed)) {
    if ($browser->getPlatform() == Browser::PLATFORM_LINUX) {
        $message = do_lang('UPGRADE_FIREFOX_LINUX');
    } else {
        $message = do_lang('UPGRADE_FIREFOX');
    }
}
if (($browser->getBrowser() == Browser::BROWSER_SAFARI) && (floatval($browser->getVersion()) < $safari_needed)) {
    $message = do_lang('UPGRADE_SAFARI');
}
if (($browser->getBrowser() == Browser::BROWSER_CHROME) && (floatval($browser->getVersion()) < $chrome_needed)) {
    if ($browser->getPlatform() == Browser::PLATFORM_LINUX) {
        $message = do_lang('UPGRADE_CHROME_LINUX');
    } else {
        $message = do_lang('UPGRADE_CHROME');
    }
}

if ($message != '') {
    if ($attach) {
        attach_message(make_string_tempcode($message), 'warn');
    } else {
        $out = put_in_standard_box(make_string_tempcode($message));
        $out->evaluate_echo();
    }
}
