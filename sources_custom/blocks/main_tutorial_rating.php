<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['parameters'] = array();
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $page_name = get_page_name();

        $views = $GLOBALS['SITE_DB']->query_select_value_if_there('tutorials_internal', 't_views', array('t_page_name' => $page_name));
        if ($views === null) {
            $GLOBALS['SITE_DB']->query_insert('tutorials_internal', array('t_views' => 0, 't_page_name' => $page_name));

            $views = 0;
        }
        $GLOBALS['SITE_DB']->query_update('tutorials_internal', array('t_views' => $views + 1), array('t_page_name' => $page_name), '', 1);

        require_code('feedback');

        $self_url = get_self_url();
        $self_title = $page_name;
        $id = $page_name;
        $test_changed = post_param_string('rating_' . $id, '');
        if ($test_changed != '') {
            decache('main_rating');
        }
        actualise_rating(true, 'tutorial', $id, $self_url, $self_title);

        $rating = display_rating($self_url, $self_title, 'tutorial', $id, 'RATING_INLINE_DYNAMIC');

        return do_template('BLOCK_MAIN_TUTORIAL_RATING', array('_GUID' => 'f68915b7d913e4736b558d0ccd59634a', 'RATING' => $rating));
    }
}
