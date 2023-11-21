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
class Hook_symbol_HYBRIDAUTH_BUTTONS_CSS
{
    public function run($param)
    {
        if (!addon_installed('hybridauth')) {
            return '';
        }

        require_code('hybridauth');
        $providers = enumerate_hybridauth_providers();

        $css = '';
        foreach ($providers as $provider => $info) {
            if (!$info['enabled']) {
                continue;
            }

            $_css = do_template('_hybridauth_button', [
                '_GUID' => '9e32c801bc53a57f37b77a4ac8f95428',
                'CODENAME' => $provider,
                'BACKGROUND_COLOUR' => $info['background_colour'],
                'TEXT_COLOUR' => $info['text_colour'],
                'ICON' => $info['icon'],
            ], null, false, null, '.css', 'css');
            $css .= $_css->evaluate();
        }

        return $css;
    }
}
