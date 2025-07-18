<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    polls
 */

/**
 * Hook class.
 */
class Hook_addon_registry_polls
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'A poll (voting) system.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_feedback',
        );
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(),
            'recommends' => array(),
            'conflicts_with' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/social/polls.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images/icons/24x24/menu/social/polls.png',
            'themes/default/images/icons/48x48/menu/social/polls.png',
            'sources/polls2.php',
            'sources/hooks/systems/block_ui_renderers/polls.php',
            'sources/hooks/systems/notifications/poll_chosen.php',
            'sources/hooks/systems/config/points_ADD_POLL.php',
            'sources/hooks/systems/config/points_CHOOSE_POLL.php',
            'sources/hooks/systems/config/poll_update_time.php',
            'sources/hooks/systems/realtime_rain/polls.php',
            'themes/default/templates/BLOCK_MAIN_POLL.tpl',
            'sources/hooks/systems/content_meta_aware/poll.php',
            'sources/hooks/systems/commandr_fs/polls.php',
            'sources/hooks/systems/addon_registry/polls.php',
            'sources/hooks/systems/preview/poll.php',
            'sources/hooks/modules/admin_setupwizard/polls.php',
            'sources/hooks/modules/admin_import_types/polls.php',
            'sources/hooks/systems/sitemap/poll.php',
            'themes/default/templates/POLL_BOX.tpl',
            'themes/default/templates/POLL_ANSWER.tpl',
            'themes/default/templates/POLL_ANSWER_RESULT.tpl',
            'themes/default/templates/POLL_SCREEN.tpl',
            'themes/default/templates/POLL_LIST_ENTRY.tpl',
            'themes/default/templates/POLL_RSS_SUMMARY.tpl',
            'themes/default/css/polls.css',
            'cms/pages/modules/cms_polls.php',
            'lang/EN/polls.ini',
            'site/pages/modules/polls.php',
            'sources/hooks/blocks/main_staff_checklist/polls.php',
            'sources/hooks/modules/search/polls.php',
            'sources/hooks/systems/page_groupings/polls.php',
            'sources/hooks/systems/rss/polls.php',
            'sources/hooks/systems/trackback/polls.php',
            'sources/polls.php',
            'sources/blocks/main_poll.php',
        );
    }

    /**
     * Get mapping between template names and the method of this class that can render a preview of them
     *
     * @return array The mapping
     */
    public function tpl_previews()
    {
        return array(
            'templates/BLOCK_MAIN_POLL.tpl' => 'block_main_poll',
            'templates/POLL_RSS_SUMMARY.tpl' => 'poll_rss_summary',
            'templates/POLL_ANSWER.tpl' => 'poll_answer',
            'templates/POLL_ANSWER_RESULT.tpl' => 'poll_answer_result',
            'templates/POLL_BOX.tpl' => 'poll_answer',
            'templates/POLL_LIST_ENTRY.tpl' => 'poll_list_entry',
            'templates/POLL_SCREEN.tpl' => 'poll_screen'
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_main_poll()
    {
        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_POLL', array(
                'CONTENT' => $this->poll('poll'),
                'BLOCK_PARAMS' => '',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__poll_rss_summary()
    {
        require_code('xml');

        $_summary = do_lorem_template('POLL_RSS_SUMMARY', array(
            'ANSWERS' => placeholder_array(),
        ));
        $summary = xmlentities($_summary->evaluate());

        $if_comments = do_lorem_template('RSS_ENTRY_COMMENTS', array(
            'COMMENT_URL' => placeholder_url(),
            'ID' => placeholder_id(),
        ), null, false, null, '.xml', 'xml');

        return array(
            lorem_globalise(do_lorem_template('RSS_ENTRY', array(
                'VIEW_URL' => placeholder_url(),
                'SUMMARY' => $summary,
                'EDIT_DATE' => placeholder_date(),
                'IF_COMMENTS' => $if_comments,
                'TITLE' => lorem_phrase(),
                'CATEGORY_RAW' => null,
                'CATEGORY' => '',
                'AUTHOR' => lorem_word(),
                'ID' => placeholder_id(),
                'NEWS' => lorem_paragraph(),
                'DATE' => placeholder_date(),
            ), null, false, null, '.xml', 'xml'), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__poll_answer()
    {
        return $this->poll('poll');
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__poll_answer_result()
    {
        return $this->poll('result');
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @param  string $section View type.
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function poll($section = '')
    {
        $tpl = new Tempcode();
        switch ($section) {
            case 'poll':
                foreach (placeholder_array() as $k => $v) {
                    $tpl->attach(do_lorem_template('POLL_ANSWER', array(
                        'PID' => placeholder_id(),
                        'I' => strval($k),
                        'CAST' => strval($k),
                        'VOTE_URL' => placeholder_url(),
                        'ANSWER' => lorem_phrase(),
                        'ANSWER_PLAIN' => lorem_phrase(),
                    )));
                }
                break;

            case 'result':
                foreach (placeholder_array() as $k => $v) {
                    $tpl->attach(do_lorem_template('POLL_ANSWER_RESULT', array(
                        'PID' => placeholder_id(),
                        'I' => strval($k),
                        'VOTE_URL' => placeholder_url(),
                        'ANSWER' => lorem_phrase(),
                        'ANSWER_PLAIN' => lorem_phrase(),
                        'WIDTH' => strval($k),
                        'VOTES' => placeholder_number(),
                    )));
                }
                break;

            default:
                foreach (placeholder_array() as $k => $v) {
                    $tpl->attach(do_lorem_template('POLL_ANSWER', array(
                        'PID' => placeholder_id(),
                        'I' => strval($k),
                        'CAST' => strval($k),
                        'VOTE_URL' => placeholder_url(),
                        'ANSWER' => lorem_phrase(),
                        'ANSWER_PLAIN' => lorem_phrase(),
                    )));
                }
                foreach (placeholder_array() as $k => $v) {
                    $tpl->attach(do_lorem_template('POLL_ANSWER_RESULT', array(
                        'PID' => placeholder_id(),
                        'I' => strval($k),
                        'VOTE_URL' => placeholder_url(),
                        'ANSWER' => lorem_phrase(),
                        'ANSWER_PLAIN' => lorem_phrase(),
                        'WIDTH' => strval($k),
                        'VOTES' => placeholder_number(),
                    )));
                }
        }

        $wrap_content = do_lorem_template('POLL_BOX', array(
            '_GUID' => '4c6b026f7ed96f0b5b8408eb5e5affb5',
            'VOTE_URL' => placeholder_url(),
            'GIVE_CONTEXT' => true,
            'SUBMITTER' => placeholder_id(),
            'RESULT_URL' => placeholder_url(),
            'SUBMIT_URL' => placeholder_url(),
            'ARCHIVE_URL' => placeholder_url(),
            'PID' => placeholder_id(),
            'COMMENT_COUNT' => placeholder_number(),
            'QUESTION_PLAIN' => lorem_phrase(),
            'QUESTION' => lorem_phrase(),
            'CONTENT' => $tpl,
            'FULL_URL' => placeholder_url(),
        ));

        return array(
            lorem_globalise($wrap_content, null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__poll_list_entry()
    {
        return array(
            lorem_globalise(do_lorem_template('POLL_LIST_ENTRY', array(
                'QUESTION' => lorem_phrase(),
                'STATUS' => lorem_phrase(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__poll_screen()
    {
        require_lang('trackbacks');

        $trackbacks = new Tempcode();
        foreach (placeholder_array(1) as $k => $v) {
            $trackbacks->attach(do_lorem_template('TRACKBACK', array(
                'ID' => placeholder_id(),
                'TIME_RAW' => placeholder_date_raw(),
                'TIME' => placeholder_date(),
                'URL' => placeholder_url(),
                'TITLE' => lorem_phrase(),
                'EXCERPT' => lorem_paragraph(),
                'NAME' => placeholder_id(),
            )));
        }
        $trackback_details = do_lorem_template('TRACKBACK_WRAPPER', array(
            'TRACKBACKS' => $trackbacks,
            'TRACKBACK_FEEDBACK_TYPE' => placeholder_id(),
            'TRACKBACK_ID' => placeholder_id(),
            'TRACKBACK_TITLE' => lorem_phrase(),
        ));

        $rating_details = '';
        $comments = '';
        $comment_details = do_lorem_template('COMMENTS_WRAPPER', array(
            'TYPE' => lorem_word(),
            'ID' => placeholder_id(),
            'REVIEW_RATING_CRITERIA' => array(),
            'AUTHORISED_FORUM_URL' => placeholder_url(),
            'FORM' => placeholder_form(),
            'COMMENTS' => $comments,
            'SORT' => 'relevance',
        ));

        $poll_details = $this->poll('poll');

        return array(
            lorem_globalise(do_lorem_template('POLL_SCREEN', array(
                'TITLE' => lorem_title(),
                'DATE_RAW' => placeholder_date_raw(),
                'ADD_DATE_RAW' => placeholder_date_raw(),
                'EDIT_DATE_RAW' => placeholder_date_raw(),
                'DATE' => placeholder_date(),
                'ADD_DATE' => placeholder_date(),
                'EDIT_DATE' => placeholder_date(),
                'VIEWS' => placeholder_number(),
                'TRACKBACK_DETAILS' => $trackback_details,
                'RATING_DETAILS' => $rating_details,
                'COMMENT_DETAILS' => $comment_details,
                'EDIT_URL' => placeholder_url(),
                'POLL_DETAILS' => $poll_details,
                'SUBMITTER' => placeholder_id(),
                'ID' => placeholder_id(),
            )), null, '', true)
        );
    }
}
