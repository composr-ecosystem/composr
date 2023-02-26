<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class Hook_hybridauth__misc_overrides
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
            'BitBucket' => [
                'background_colour' => '205081',
                'text_colour' => 'FFFFFF',
            ],
            'Dropbox' => [
                'background_colour' => '000000', // 1087DD, except our icon contains the colour
                'text_colour' => 'FFFFFF',
                'icon' => 'links/dropbox',
            ],
            'MicrosoftGraph' => [
                'label' => 'Microsoft',
                'icon' => 'links/microsoft',
            ],
            'Yahoo' => [
                'background_colour' => 'F2EDAA', // 720E9E, except our icon contains the colour
                'text_colour' => '000000',
                'icon' => 'links/yahoo',
            ],
        ];
    }
}
