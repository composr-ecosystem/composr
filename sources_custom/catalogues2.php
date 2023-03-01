<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    data_mappr
 */

function init__catalogues2($code)
{
    if (!addon_installed('data_mappr')) {
        return $code;
    }

    return str_replace(
        "delete_cache_entry('main_cc_embed');",
        "
        delete_cache_entry('main_cc_embed');
        delete_cache_entry('main_google_map');
        ",
        $code
    );
}
