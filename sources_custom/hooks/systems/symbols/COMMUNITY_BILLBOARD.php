<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_symbol_COMMUNITY_BILLBOARD
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run(array $param) : string
    {
        if (!addon_installed('community_billboard')) {
            return '';
        }

        require_css('community_billboard');
        require_lang('community_billboard');

        $system = (mt_rand(0, 1) == 0);
        $_community_billboard = null;

        if ((!$system) || (get_option('system_community_billboard') == '')) {
            $_community_billboard = persistent_cache_get('COMMUNITY_BILLBOARD');
            if ($_community_billboard === null) {
                $community_billboard = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'community_billboard WHERE active_now=1 AND activation_time+days*60*60*24>' . strval(time()));
                if (empty($community_billboard)) {
                    persistent_cache_set('COMMUNITY_BILLBOARD', false);
                } else {
                    $_community_billboard = get_translated_tempcode('community_billboard', $community_billboard[0], 'the_message');
                    persistent_cache_set('COMMUNITY_BILLBOARD', $_community_billboard);
                }
            }
            if ($_community_billboard === false) {
                $_community_billboard = null;
            }
        }
        if ($_community_billboard === null) {
            $value = get_option('system_community_billboard');
        } else {
            // Must evaluate tempcode so do_lang returns a string, which is the required output
            $value = do_lang('_COMMUNITY_MESSAGE', $_community_billboard->evaluate());
        }

        return $value;
    }
}
