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
class Hook_symbol_HYBRIDAUTH_BUTTONS
{
    public function run($param)
    {
        if (!addon_installed('hybridauth')) {
            return '';
        }

        require_code('hybridauth');
        require_lang('hybridauth');
        require_css('hybridauth');

        $providers = enumerate_hybridauth_providers();

        $only_prominent = !empty($param[1]);

        $keep = symbol_tempcode('KEEP');

        $buttons = '';
        foreach ($providers as $provider => $info) {
            if (($only_prominent) && (!$info['prominent_button'])) {
                continue;
            }

            $return_url_part = urlencode(static_evaluate_tempcode(protect_url_parameter(get_self_url(true, true))));
            $url = find_script('hybridauth') . '?provider=' . urlencode($provider) . '&composr_return_url=' . $return_url_part . $keep->evaluate();

            $button = do_template('HYBRIDAUTH_BUTTON', [
                'CODENAME' => $provider,
                'LABEL' => $info['label'],
                'BACKGROUND_COLOUR' => $info['background_colour'],
                'TEXT_COLOUR' => $info['text_colour'],
                'ICON' => $info['icon'],
                'URL' => $url,
            ]);
            $buttons .= $button->evaluate();
        }

        return $buttons;
    }
}
