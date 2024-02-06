<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    charity_banners
 */

/**
 * Add a banner to the database, and return the new ID of that banner in the database.
 * Simpler than add_banner, as it returns if the banner already exists, and saves less data.
 *
 * @param  ID_TEXT $name The name of the banner
 * @param  URLPATH $imgurl The URL to the banner image
 * @param  SHORT_TEXT $title_text The title text for the banner (only used for text banners, and functions as the 'trigger text' if the banner type is shown inline)
 * @param  SHORT_TEXT $caption The caption of the banner
 * @param  ?integer $campaign_remaining The number of hits the banner may have (null: not applicable for this banner type)
 * @range  0 max
 * @param  URLPATH $site_url The URL to the site the banner leads to
 * @param  integer $display_likelihood The banner's "Display likelihood"
 * @range  1 max
 * @param  LONG_TEXT $notes Any notes associated with the banner
 * @param  SHORT_INTEGER $deployment_agreement The type of banner (0=permanent, 1=campaign, 2=fallback)
 * @set 0 1 2
 * @param  ?TIME $expiry_date The banner expiry date (null: never)
 * @param  ?MEMBER $submitter The banners submitter (null: current member)
 * @param  BINARY $validated Whether the banner has been validated
 * @param  ID_TEXT $b_type The banner type (can be anything, where blank means 'normal')
 * @param  ?TIME $time The time the banner was added (null: now)
 * @param  integer $hits_from The number of return hits from this banners site
 * @param  integer $hits_to The number of banner hits to this banners site
 * @param  integer $views_from The number of return views from this banners site
 * @param  integer $views_to The number of banner views to this banners site
 * @param  ?TIME $edit_date The banner edit date (null: never)
 */
function add_banner_quiet(string $name, string $imgurl, string $title_text, string $caption, ?int $campaign_remaining, string $site_url, int $display_likelihood, string $notes, int $deployment_agreement, ?int $expiry_date, ?int $submitter, int $validated = 0, string $b_type = '', ?int $time = null, int $hits_from = 0, int $hits_to = 0, int $views_from = 0, int $views_to = 0, ?int $edit_date = null)
{
    if (!is_numeric($display_likelihood)) {
        $display_likelihood = 3;
    }
    if (!is_numeric($campaign_remaining)) {
        $campaign_remaining = null;
    }
    if ($time === null) {
        $time = time();
    }
    if ($submitter === null) {
        $submitter = get_member();
    }

    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('banners', 'name', ['name' => $name]);
    if ($test === null) {
        if (!addon_installed('validation')) {
            $validated = 1;
        }
        $map = [
            'title_text' => $title_text,
            'direct_code' => '',
            'b_type' => $b_type,
            'edit_date' => $edit_date,
            'add_date' => $time,
            'expiry_date' => $expiry_date,
            'deployment_agreement' => $deployment_agreement,
            'submitter' => $submitter,
            'name' => $name,
            'img_url' => $imgurl,
            'campaign_remaining' => $campaign_remaining,
            'site_url' => $site_url,
            'display_likelihood' => $display_likelihood,
            'notes' => '',
            'validated' => $validated,
            'hits_from' => $hits_from,
            'hits_to' => $hits_to,
            'views_from' => $views_from,
            'views_to' => $views_to,
        ];
        $map += insert_lang_comcode('caption', $caption, 2);
        $GLOBALS['SITE_DB']->query_insert('banners', $map);

        if (function_exists('delete_cache_entry')) {
            delete_cache_entry('main_banner_wave');
            delete_cache_entry('main_top_sites');
        }

        log_it('ADD_BANNER', $name, $caption);
    }
}
