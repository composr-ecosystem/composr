<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */

/**
 * Hook class.
 */
class Hook_hybridauth_google
{
    /**
     * Get extended integration info to enhance Hybridauth, with easier and better provider integration.
     *
     * @return array Map of integration info
     */
    public function info() : array
    {
        if (!addon_installed('hybridauth')) {
            return [];
        }

        return [
            'Google' => [
                'enabled' => (get_option('google_allow_signups') == '1') && (get_option('google_apis_client_id') != ''),

                // Prominence options. These could be dynamic, e.g. for countries/languages where a service is not popular, do not show a prominent button and/or lower the priority
                'prominent_button' => true, // Basically if it shows in login blocks (as opposed to just the full login screen)
                'button_precedence' => 2, // 1=most prominent, 100=least prominent

                'background_colour' => '000000', // 720E9E, except our icon contains the colour
                'text_colour' => 'FFFFFF',
                'icon' => 'links/google',

                'keys' => (get_option('google_apis_client_id') == '' || get_option('google_apis_client_secret') == '') ? [] : [
                    'id' => get_option('google_apis_client_id'),
                    'secret' => get_option('google_apis_client_secret'),
                ],
            ],

            'YouTube' => [
                'enabled' => (get_option('google_allow_signups') == '1') && (get_option('google_apis_client_id') != ''),

                'keys' => (get_option('google_apis_client_id') == '' || get_option('google_apis_client_secret') == '') ? [] : [
                    'id' => get_option('google_apis_client_id'),
                    'secret' => get_option('google_apis_client_secret'),
                ],
            ],
        ];
    }
}
