<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    world_regions
 */

/**
 * Create CPF fields for address.
 * Assumes Conversr.
 */
function install_address_fields()
{
    require_lang('cns');

    $GLOBALS['FORUM_DRIVER']->install_create_custom_field('street_address', 100, /*locked=*/0, /*viewable=*/0, /*settable=*/1, /*required=*/0, '', 'long_text');
    $GLOBALS['FORUM_DRIVER']->install_create_custom_field('city', 40, /*locked=*/0, /*viewable=*/0, /*settable=*/1, /*required=*/0, '', 'short_text');
    $GLOBALS['FORUM_DRIVER']->install_create_custom_field('county', 40, /*locked=*/0, /*viewable=*/0, /*settable=*/1, /*required=*/0, '', 'short_text');

    require_code('locations/us');
    $states = '=(Outside USA)';
    $_states = get_states_US();
    foreach ($_states as $code => $name) {
        $states .= '|' . $code . '=' . $name;
    }
    $GLOBALS['FORUM_DRIVER']->install_create_custom_field('state', 2, /*locked=*/0, /*viewable=*/0, /*settable=*/1, /*required=*/0, '', 'list', 0, $states);

    $GLOBALS['FORUM_DRIVER']->install_create_custom_field('post_code', 20, /*locked=*/0, /*viewable=*/0, /*settable=*/1, /*required=*/0, '', 'short_text');

    require_code('locations');
    $countries = '';
    $_countries = find_countries();
    foreach ($_countries as $code => $name) {
        if ($countries != '') {
            $countries .= '|';
        }
        $countries .= $code . '=' . $name;
    }
    $GLOBALS['FORUM_DRIVER']->install_create_custom_field('country', 5, /*locked=*/0, /*viewable=*/0, /*settable=*/1, /*required=*/0, '', 'list', 0, $countries);
}
