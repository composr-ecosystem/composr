<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    twitter_support
 */

/**
 * Hook class.
 */
class Hook_hybridauth_twitter
{
    /**
     * Get extended integration info to enhance Hybridauth, with easier and better provider integration.
     *
     * @return array Map of integration info
     */
    public function info() : array
    {
        if (!addon_installed('twitter_support')) {
            return [];
        }

        return [
            'Twitter' => [
                'enabled' => (get_option('twitter_allow_signups') == '1') && (get_option('twitter_api_key') != ''),

                // Prominence options. These could be dynamic, e.g. for countries/languages where a service is not popular, do not show a prominent button and/or lower the priority
                'prominent_button' => true, // Basically if it shows in login blocks (as opposed to just the full login screen)
                'button_precedence' => 3, // 1=most prominent, 100=least prominent

                'background_colour' => '000000', // 720E9E, except our icon contains the colour
                'text_colour' => 'FFFFFF',
                'icon' => 'links/twitter',

                'keys' => (get_option('twitter_api_key') == '' || get_option('twitter_api_secret') == '') ? [] : [
                    'id' => get_option('twitter_api_key'),
                    'secret' => get_option('twitter_api_secret'),
                ],
            ],
        ];
    }
}
