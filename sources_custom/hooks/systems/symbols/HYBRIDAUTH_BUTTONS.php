<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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

        $providers = enumerate_hybridauth_providers();

        $only_prominent = !empty($param[1]);
        $max = !empty($param[2]) ? intval($param[2]) : null;

        $keep = symbol_tempcode('KEEP');

        // Where to redirect to
        $page_after_login = get_option('page_after_login');
        if ($page_after_login != '') {
            if (strpos($page_after_login, ':') === false) {
                $zone = get_page_zone($page_after_login, false);
                if ($zone === null) {
                    $zone = 'site';
                }
                $return_url = static_evaluate_tempcode(build_url(['page' => $page_after_login], $zone));
            } else {
                $return_url = page_link_to_url($page_after_login);
            }
        } else {
            if ((get_page_name() == 'join') || (currently_logging_in())) {
                $return_url = static_evaluate_tempcode(build_url(['page' => ''], ''));
            } else {
                $return_url = get_self_url(true, true);
            }
        }
        $return_url_part = urlencode(static_evaluate_tempcode(protect_url_parameter($return_url)));

        $buttons = '';
        $i = 0;
        foreach ($providers as $provider => $info) {
            if (($only_prominent) && (!$info['prominent_button'])) {
                continue;
            }

            if (($max !== null) && ($i == $max)) {
                break;
            }

            if (!$info['enabled']) {
                continue;
            }

            $url = find_script('hybridauth') . '?provider=' . urlencode($provider) . '&composr_return_url=' . $return_url_part . $keep->evaluate();

            $button = do_template('HYBRIDAUTH_BUTTON', [
                '_GUID' => '274651f0ae26f38cd09bf11e30a8ef23',
                'CODENAME' => $provider,
                'LABEL' => $info['label'],
                'BACKGROUND_COLOUR' => $info['background_colour'],
                'TEXT_COLOUR' => $info['text_colour'],
                'ICON' => $info['icon'],
                'URL' => $url,
            ]);
            $buttons .= $button->evaluate();

            $i++;
        }

        return $buttons;
    }
}
