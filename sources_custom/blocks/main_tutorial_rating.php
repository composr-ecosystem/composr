<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_tutorials
 */

/**
 * Block class.
 */
class Block_main_tutorial_rating
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'composr_tutorials';
        $info['parameters'] = [];
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters
     * @return Tempcode The result of execution
     */
    public function run(array $map) : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('composr_tutorials', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('composr_homesite')) {
            return do_template('RED_ALERT', ['_GUID' => 'hz8wk7almyfhzemamnguz0e6ex01qrvs', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite'))]);
        }
        if (!addon_installed('composr_homesite_support_credits')) {
            return do_template('RED_ALERT', ['_GUID' => 'dopmcgzft9wr03wark2ydpfkwdzalztg', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite_support_credits'))]);
        }
        if (!addon_installed('cms_release_build')) {
            return do_template('RED_ALERT', ['_GUID' => 'mz0bowx3abdddomvv19ps29mpcnolnak', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_release_build'))]);
        }

        $block_id = get_block_id($map);

        $page_name = get_page_name();

        $views = $GLOBALS['SITE_DB']->query_select_value_if_there('tutorials_internal', 't_views', ['t_page_name' => $page_name]);
        if ($views === null) {
            $GLOBALS['SITE_DB']->query_insert('tutorials_internal', ['t_views' => 0, 't_page_name' => $page_name]);

            $views = 0;
        }
        $GLOBALS['SITE_DB']->query_update('tutorials_internal', ['t_views' => $views + 1], ['t_page_name' => $page_name], '', 1);

        require_code('feedback');

        $self_url = get_self_url();
        $self_title = $page_name;
        $id = $page_name;
        $test_changed = post_param_string('rating_' . $id, '');
        if ($test_changed != '') {
            delete_cache_entry('main_rating');
        }
        actualise_rating(true, 'tutorial', $id, $self_url, $self_title);

        $rating = display_rating($self_url, $self_title, 'tutorial', $id, 'RATING_INLINE_DYNAMIC');

        return do_template('BLOCK_MAIN_TUTORIAL_RATING', [
            '_GUID' => 'f68915b7d913e4736b558d0ccd59634a',
            'BLOCK_ID' => $block_id,
            'RATING' => $rating,
        ]);
    }
}
