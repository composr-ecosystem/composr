<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    google_analytics
 */

/**
 * Hook class.
 */
class Hook_admin_stats_google_analytics
{
    /**
     * Define stats screens implemented in this hook.
     *
     * @return ?array A pair: entry-point, do-next information (null: hook is disabled).
     */
    public function info()
    {
        if (!addon_installed('google_analytics')) {
            return null;
        }

        require_code('google_analytics');
        require_lang('google_analytics');

        $result = google_analytics_initialise(true);
        if (is_object($result)) {
            return null;
        }

        return [
            ['demographics' => ['_GOOGLE_ANALYTICS', 'buttons/search'],],
            ['buttons/search', ['_SELF', ['type' => 'google_analytics'], '_SELF'], do_lang('_GOOGLE_ANALYTICS'), 'DOC_GOOGLE_ANALYTICS'],
        ];
    }

    /**
     * The UI to show Google Analytics.
     *
     * @param  object $ob The stats module object
     * @param  string $type The screen type
     * @return Tempcode The UI
     */
    public function google_analytics($ob, $type)
    {
        require_code('google_analytics');
        require_lang('google_analytics');

        set_helper_panel_text(comcode_lang_string('DOC_GOOGLE_ANALYTICS'));

        $ga = render_google_analytics(get_param_string('id', '*'));
        return do_template('PAGINATION_SCREEN', [
            '_GUID' => '48dedd8da83a0ebbf1a9e050d5cf6ac5',
            'TITLE' => get_screen_title('_GOOGLE_ANALYTICS'),
            'CONTENT' => $ga,
        ]);
    }
}
