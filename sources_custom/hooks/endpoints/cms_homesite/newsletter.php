<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_endpoint_cms_homesite_newsletter
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Info about the hook
     */
    public function info(?string $type, ?string $id) : array
    {
        return [
            'authorization' => false,
            'log_stats_event' => 'cms_homesite/newsletter',
        ];
    }

    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        if (!addon_installed('cms_homesite')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite')));
        }
        if (!addon_installed('newsletter')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('newsletter')));
        }

        $url = get_param_string('url', false, INPUT_FILTER_URL_GENERAL);
        $email = get_param_string('email', false, INPUT_FILTER_GET_IDENTIFIER);
        $advertise_on = get_param_integer('advertise_on', 0);
        $lang = user_lang__with__translation_override();

        if ($advertise_on == 1) {
            $test = $GLOBALS['SITE_DB']->query_select_value_if_there('may_feature', 'url', ['url' => $url]);
            if ($test === null) {
                $GLOBALS['SITE_DB']->query_insert('may_feature', ['url' => $url]);
            }
        } else { // Un-featuring
            $GLOBALS['SITE_DB']->query_delete('may_feature', ['url' => $url]);
        }

        if (($email != 'dont_sign_me_up@composr.app') && ($email != '')) {
            require_code('newsletter');
            require_code('newsletter2');
            basic_newsletter_join($email, $lang);
        }

        return ['success' => true];
    }
}
