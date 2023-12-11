<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    charity_banners
 */

/**
 * Add a banner to the database, and return the new ID of that banner in the database.
 *
 * @param  ID_TEXT $name The name of the banner
 * @param  URLPATH $imgurl The URL to the banner image
 * @param  SHORT_TEXT $title_text The title text for the banner (only used for text banners, and functions as the 'trigger text' if the banner type is shown inline)
 * @param  SHORT_TEXT $caption The caption of the banner
 * @param  ?integer $campaign_remaining The number of hits the banner may have (null: not applicable for this banner type)
 * @range  0 max
 * @param  URLPATH $site_url The URL to the site the banner leads to
 * @param  integer $importance_modulus The banners "importance modulus"
 * @range  1 max
 * @param  LONG_TEXT $notes Any notes associated with the banner
 * @param  SHORT_INTEGER $the_type The type of banner (0=permanent, 1=campaign, 2=fallback)
 * @set    0 1 2
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
function add_banner_quiet($name, $imgurl, $title_text, $caption, $campaign_remaining, $site_url, $importance_modulus, $notes, $the_type, $expiry_date, $submitter, $validated = 0, $b_type = '', $time = null, $hits_from = 0, $hits_to = 0, $views_from = 0, $views_to = 0, $edit_date = null)
{
    if (!is_numeric($importance_modulus)) {
        $importance_modulus = 3;
    }
    if (!is_numeric($campaign_remaining)) {
        $campaign_remaining = null;
    }
    if (is_null($time)) {
        $time = time();
    }
    if (is_null($submitter)) {
        $submitter = get_member();
    }

    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('banners', 'name', array('name' => $name));
    if (is_null($test)) {
        if (!addon_installed('unvalidated')) {
            $validated = 1;
        }
        $map = array(
            'b_title_text' => $title_text,
            'b_direct_code' => '',
            'b_type' => $b_type,
            'edit_date' => $edit_date,
            'add_date' => $time,
            'expiry_date' => $expiry_date,
            'the_type' => $the_type,
            'submitter' => $submitter,
            'name' => $name,
            'img_url' => $imgurl,
            'campaign_remaining' => $campaign_remaining,
            'site_url' => $site_url,
            'importance_modulus' => $importance_modulus,
            'notes' => '',
            'validated' => $validated,
            'hits_from' => $hits_from,
            'hits_to' => $hits_to,
            'views_from' => $views_from,
            'views_to' => $views_to,
        );
        $map += insert_lang_comcode('caption', $caption, 2);
        $GLOBALS['SITE_DB']->query_insert('banners', $map);

        if (function_exists('decache')) {
            decache('main_banner_wave');
            decache('main_top_sites');
        }

        log_it('ADD_BANNER', $name, $caption);
    }
}
