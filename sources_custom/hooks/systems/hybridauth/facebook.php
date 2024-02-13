<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_hybridauth_facebook
{
    /**
     * Get extended integration info to enhance Hybridauth, with easier and better provider integration.
     *
     * @return array Map of integration info
     */
    public function info() : array
    {
        if (!addon_installed('facebook_support')) {
            return [];
        }

        return [
            'Facebook' => [
                'enabled' => (get_option('facebook_allow_signups') == '1') && (get_option('facebook_appid') != ''),

                // Prominence options. These could be dynamic, e.g. for countries/languages where a service is not popular, do not show a prominent button and/or lower the priority
                'prominent_button' => true, // Basically if it shows in login blocks (as opposed to just the full login screen)
                'button_precedence' => 1, // 1=most prominent, 100=least prominent

                'background_colour' => '000000', // 3B5998, except our icon contains the colour
                'text_colour' => 'FFFFFF',
                'icon' => 'links/facebook',

                'keys' => (get_option('facebook_appid') == '' || get_option('facebook_secret_code') == '') ? [] : [
                    'id' => get_option('facebook_appid'),
                    'secret' => get_option('facebook_secret_code'),
                ],
            ],
        ];
    }
}
