<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class Hook_hybridauth_apple
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
            'Apple' => [
                'enabled' => null,

                // Prominence options. These could be dynamic, e.g. for countries/languages where a service is not popular, do not show a prominent button and/or lower the priority
                'prominent_button' => true, // Basically if it shows in login blocks (as opposed to just the full login screen)
                'button_precedence' => 4, // 1=most prominent, 100=least prominent

                'background_colour' => '050708',
                'text_colour' => 'FFFFFF',
                'icon' => 'links/apple',

                'keys' => [
                ],
            ],
        ];
    }
}
