<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    data_mappr
 */

function init__catalogues2($code)
{
    return str_replace(
        "decache('main_cc_embed');",
        "
        decache('main_cc_embed');
        decache('main_google_map');
        ",
        $code
    );
}
